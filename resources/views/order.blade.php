<!DOCTYPE html>
<html lang="en">

<head>
    <title>Orders</title>
    @include('layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
</head>

<body class="bg-gray-50">

    <!-- sidenav  -->
    @include('layout.sidebar')
    <!-- end sidenav -->
    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        <!-- Navbar -->
        @include('layout.navbar')
        <!-- end Navbar -->
        <div class="p-5">
            <div class="w-full rounded-lg bg-white h-fit mx-auto">
                <div class="p-3">
                    <div class="flex justify-between">
                        <h1 class="font-extrabold text-3xl">Orders</h1>
                        <a class="p-2 px-8 bg-green-500 rounded-lg text-white hover:text-black text-center"
                            href="{{ route('addorder') }}">+ Add</a>
                    </div>
                </div>
                <div class="p-2">
                    <h1 class="font-extrabold text-2xl">Tasks</h1>
                    <div class="overflow-auto">
                        <table id="myTable" class="bg-gray-50 border-2">
                            <thead class="w-full">
                                <th>No</th>
                                <th>Date</th>
                                <th>Id</th>
                                <th>Layanan</th>
                                <th>User</th>
                                <th>Chair</th>
                                <th>Order</th>
                                <th>Payment</th>
                                <th>Total</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($orders as $order)
                                    <tr class="border-2">
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $order->created_at ?? 'N/A' }}</td>
                                        <td>{{ $order->no_order ?? 'N/A' }}</td>
                                        <td>{{ $order->layanan ?? 'N/A' }}</td>
                                        <td>{{ $order->cart->user->name ?? 'N/A' }}</td>
                                        <td>{{ $order->cart->chair->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($order->cart->cartMenus)
                                                @foreach ($order->cart->cartMenus as $cartMenu)
                                                    {{ $cartMenu->menu->name ?? 'N/A' }} -
                                                    {{ $cartMenu->quantity ?? 0 }} -
                                                    {{ $cartMenu->notes ?? 'No notes' }} <br />
                                                @endforeach
                                            @else
                                                No items
                                            @endif
                                        </td>
                                        <td>{{ $order->payment_type ?? 'N/A' }}</td>
                                        <td>
                                            Rp. {{ number_format($order->cart->total_amount ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td>{{ $order->alamat ?? 'N/A' }}</td>
                                        <td>{{ $order->status ?? 'Unknown' }}</td>
                                        <td class="flex gap-2">
                                            <div class="w-full">
                                                <form action="{{ route('archive', ['orderId' => $order->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 px-4 w-full text-white hover:text-black bg-blue-500 rounded-lg text-center">
                                                        Done
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="w-full">
                                                <form
                                                    class="p-2 px-4 text-white hover:text-black bg-red-500 rounded-lg text-center"
                                                    method="post"
                                                    action="{{ route('delorder', ['id' => $order->id]) }}">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = $('#myTable').DataTable();

        });
    </script>
</body>

</html>
