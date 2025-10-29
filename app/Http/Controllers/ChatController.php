<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Chat;
use App\Models\Store;
use App\Models\Histoy;
use Prism\Prism\Prism;
use Illuminate\Http\Request;
use Prism\Prism\Enums\Provider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\CommonMarkConverter;
use Prism\Prism\Exceptions\PrismException;

class ChatController extends Controller
{

    private function getStoreId()
    {
        $user = Auth::user();

        return $user && $user->store ? $user->store->id : null;
    }

    public function bot()
    {
        $storeId = $this->getStoreId(); // Mendapatkan store ID pengguna saat ini

        if (!$storeId) {
            return view('addstore');
        }

        // Reset (hapus) semua chat yang terkait dengan store ini
        Chat::where('store_id', $storeId)->delete();

        // Jika kamu ingin menyisipkan chat default setelah reset, bisa ditambahkan di sini
        // Chat::create([...]);

        // Ambil chat terbaru (akan kosong karena sudah direset)
        $chats = Chat::where('store_id', $storeId)
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get();

        return view('bot', compact('chats'));
    }

    public function gen(Request $request)
    {
        $storeId = $this->getStoreId();
        if (!$storeId) return view('addstore');

        $prompt = trim($request->input('prompt'));
        if (!$prompt || strlen($prompt) < 3) {
            return response()->json(['error' => 'Prompt tidak boleh kosong dan harus lebih dari 3 karakter.'], 400);
        }

        // Cek apakah prompt bisa dijawab langsung dari database (analisis internal)
        $responseFromDatabase = $this->analyzePrompt($prompt, $storeId);
        if ($responseFromDatabase) {
            Chat::create([
                'store_id' => $storeId,
                'prompt' => $prompt,
                'response' => $responseFromDatabase,
            ]);
            return response()->json(['response' => $responseFromDatabase], 200);
        }

        // Ambil 5 chat terakhir sebagai konteks supaya AI dapat memahami percakapan
        $previousChats = Chat::where('store_id', $storeId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->reverse();

        $conversationContext = '';
        foreach ($previousChats as $chat) {
            $conversationContext .= "User: {$chat->prompt}\nBot: {$this->stripHtml($chat->response)}\n";
        }

        $fullPrompt = $conversationContext . "User: {$prompt}\nBot:";

        try {
            $response = Prism::text()
                ->using(Provider::Gemini, 'gemini-2.0-flash')
                ->withPrompt($fullPrompt)
                ->asText();

            $generatedText = trim($response->text);

            // Convert markdown text ke HTML
            $converter = new CommonMarkConverter();
            $generatedHtml = $converter->convert($generatedText)->getContent();

            Chat::create([
                'store_id' => $storeId,
                'prompt' => $prompt,
                'response' => $generatedHtml,
            ]);

            return response()->json(['response' => $generatedHtml], 200);
        } catch (PrismException $e) {
            Log::error('Text generation failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal menghasilkan teks. Silakan coba lagi nanti.'], 500);
        } catch (Throwable $e) {
            Log::error('Generic error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Terjadi kesalahan tak terduga. Silakan coba lagi nanti.'], 500);
        }
    }

    private function stripHtml($html)
    {
        return trim(strip_tags($html));
    }

    private function analyzePrompt($prompt, $storeId)
    {
        $promptLower = strtolower($prompt);

        // Tangkap pola keyword utama dengan regex yang lebih fleksibel
        $patterns = [
            'sales_today' => '/(total )?(penjualan|pendapatan|omset) (hari ini|sekarang)/i',
            'sales_month' => '/(total )?(penjualan|pendapatan|omset) bulan (ini|[a-z]+ \d{4})/i',
            'orders_today' => '/jumlah (order|pesanan) (hari ini|sekarang)/i',
            'top_payment' => '/pembayaran (terbanyak|populer|paling sering)/i',
            'sales_week_trend' => '/tren (penjualan|pendapatan) minggu ini/i',
            'best_selling_products' => '/produk (terlaris|paling laris|favorit)/i',
            'branch_sales' => '/penjualan cabang (.+)/i',
            'average_sales_today' => '/rata-rata (penjualan|pendapatan) (per transaksi )?(hari ini|sekarang)/i',
            'total_transactions_today' => '/total transaksi (hari ini|sekarang)/i',
        ];

        // 1. Total penjualan hari ini
        if (preg_match($patterns['sales_today'], $prompt)) {
            $today = now()->toDateString();
            $total = Histoy::where('store_id', $storeId)
                ->where('status', 'settlement')
                ->whereDate('created_at', $today)
                ->sum('total_amount');

            return "Total penjualan hari ini adalah Rp " . number_format($total, 0, ',', '.');
        }

        // 2. Total penjualan bulan ini atau bulan tertentu
        if (preg_match($patterns['sales_month'], $prompt, $matches)) {
            $now = now();
            $monthString = strtolower(trim($matches[2] ?? ''));

            if (in_array($monthString, ['ini', 'bulan ini'])) {
                $year = $now->year;
                $month = $now->month;
            } else {
                // Coba parsing bulan dan tahun dari input (contoh: 'juni 2024')
                try {
                    $dt = \Carbon\Carbon::createFromFormat('F Y', ucfirst($monthString));
                    $year = $dt->year;
                    $month = $dt->month;
                } catch (\Exception $e) {
                    // fallback ke bulan sekarang kalau gagal parse
                    $year = $now->year;
                    $month = $now->month;
                }
            }

            $total = Histoy::where('store_id', $storeId)
                ->where('status', 'settlement')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('total_amount');

            $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

            return "Total penjualan bulan {$monthName} adalah Rp " . number_format($total, 0, ',', '.');
        }

        // 3. Jumlah order hari ini
        if (preg_match($patterns['orders_today'], $prompt)) {
            $count = Histoy::where('store_id', $storeId)
                ->where('status', 'settlement')
                ->whereDate('created_at', now())
                ->count();

            return "Jumlah order hari ini adalah $count pesanan.";
        }

        // 4. Pembayaran terbanyak
        if (preg_match($patterns['top_payment'], $prompt)) {
            $top = Histoy::where('store_id', $storeId)
                ->where('status', 'settlement')
                ->select('payment_type', DB::raw('COUNT(*) as total'))
                ->groupBy('payment_type')
                ->orderByDesc('total')
                ->first();

            if (!$top) return "Belum ada data pembayaran.";

            $description = match (strtolower($top->payment_type)) {
                'qris' => 'QRIS (scan QR code)',
                'cash' => 'tunai (cash)',
                'gopay' => 'GoPay (dompet digital)',
                'ovo' => 'OVO (dompet digital)',
                'dana' => 'Dana (dompet digital)',
                default => ucfirst($top->payment_type),
            };

            return "Jenis pembayaran terbanyak adalah {$description} sebanyak {$top->total} kali.";
        }

        // 5. Tren penjualan minggu ini
        if (preg_match($patterns['sales_week_trend'], $prompt)) {
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();
            $sales = Histoy::where('store_id', $storeId)
                ->where('status', 'settlement')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            if ($sales->isEmpty()) return "Belum ada penjualan minggu ini.";

            $result = "Tren penjualan minggu ini:\n";
            foreach ($sales as $day) {
                $result .= date('D, d M', strtotime($day->date)) . ": Rp " . number_format($day->total, 0, ',', '.') . "\n";
            }
            return $result;
        }

        // 6. Produk terlaris
        if (preg_match($patterns['best_selling_products'], $prompt)) {
            $productsRaw = Histoy::where('store_id', $storeId)
                ->where('status', 'settlement')
                ->get(['order']);

            $productCounts = [];

            foreach ($productsRaw as $row) {
                $parts = explode(' - ', $row->order);
                $productName = $parts[0] ?? null;
                $qty = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : 1;

                if (!$productName) continue;

                if (!isset($productCounts[$productName])) {
                    $productCounts[$productName] = 0;
                }
                $productCounts[$productName] += $qty;
            }

            if (empty($productCounts)) {
                return "Belum ada data produk.";
            }

            arsort($productCounts);

            $result = "Produk terlaris (berdasarkan jumlah kuantitas terjual):\n";
            $topProducts = array_slice($productCounts, 0, 5, true);
            foreach ($topProducts as $name => $count) {
                $result .= "- $name: $count pcs terjual\n";
            }
            return $result;
        }

        // 7. Penjualan cabang spesifik
        if (preg_match($patterns['branch_sales'], $prompt, $matches)) {
            $storeName = trim($matches[1]);
            $store = Store::whereRaw('LOWER(name) = ?', [strtolower($storeName)])->first();

            if (!$store) return "Cabang \"$storeName\" tidak ditemukan.";

            $total = Histoy::where('store_id', $store->id)
                ->where('status', 'settlement')
                ->whereDate('created_at', now()->toDateString())
                ->sum('total_amount');

            return "Total penjualan hari ini untuk cabang {$store->name}: Rp " . number_format($total, 0, ',', '.');
        }

        // 8. Rata-rata penjualan per transaksi hari ini
        if (preg_match($patterns['average_sales_today'], $prompt)) {
            $today = now()->toDateString();
            $total = Histoy::where('store_id', $storeId)
                ->where('status', 'settlement')
                ->whereDate('created_at', $today)
                ->sum('total_amount');

            $count = Histoy::where('store_id', $storeId)
                ->where('status', 'settlement')
                ->whereDate('created_at', $today)
                ->count();

            if ($count === 0) return "Belum ada transaksi hari ini.";

            $average = $total / $count;
            return "Rata-rata penjualan per transaksi hari ini adalah Rp " . number_format($average, 0, ',', '.');
        }

        // 9. Total transaksi hari ini
        if (preg_match($patterns['total_transactions_today'], $prompt)) {
            $count = Histoy::where('store_id', $storeId)
                ->where('status', 'settlement')
                ->whereDate('created_at', now())
                ->count();

            return "Total transaksi hari ini adalah $count transaksi.";
        }

        // Jika tidak ada pola cocok, return null supaya fallback ke AI generasi teks
        return null;
    }
}
