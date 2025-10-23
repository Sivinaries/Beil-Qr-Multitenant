<?php

namespace App\Http\Controllers;

use App\Models\Chair;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    public function loginQr(Request $request, $id)
    {
        $chair = Chair::findOrFail($id);

        $qrUrl = route('signin', [
            'qrToken' => $chair->qr_token,
            'name' => $chair->name,
            'storeId' => $chair->store_id
        ]);

        $qrCode = QrCode::size(400)->generate($qrUrl);

        $filename = "qrcodes/" . Str::random(10) . ".svg";
        Storage::disk('public')->put($filename, $qrCode);

        return view('qrcode', ['filename' => $filename, 'chair' => $chair]);
    }
}
