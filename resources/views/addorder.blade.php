<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Order</title>
    @include('layout.head')
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
            <div class='w-full bg-white rounded-lg h-fit mx-auto'>
                <div class="p-3 text-center">
                    <h1 class="font-extrabold text-3xl">Add Order</h1>
                </div>
                <div class="p-2">
                    <div class="space-y-5">
                        <label class="font-semibold text-lg text-black">Cart:</label>
                        <div class="p-2 bg-gray-200 w-full rounded-lg">
                            <div class="grid grid-cols-2 xl:grid-cols-6 lg:grid-cols-6 gap-2 p-2">
                                @foreach ($cart->cartMenus as $menu)
                                    <div class='w-full'>
                                        <div class='p-2 rounded-lg relative bg-red-900 space-y-2'>
                                            <div class='space-y-2'>
                                                <div class='bg-gray-100 p-2 rounded-lg '>
                                                    <img src="{{ asset('storage/img/' . basename($menu->menu->img)) }}"
                                                        alt="Product Image"
                                                        class='mx-auto my-auto w-14 h-17 rounded-lg relative' />
                                                </div>
                                                <div class='space-y-1'>
                                                    <div class="flex justify-between">
                                                        <h1 class='font-extrabold text-sm text-white'>
                                                            {{ $menu->menu->name }}</h1>
                                                        <h1 class="font-extrabold text-sm text-white">X</h1>
                                                        <h1 class="font-extrabold text-sm text-white">
                                                            {{ $menu->quantity }}</h1>
                                                    </div>
                                                    <p class='font-light text-sm text-white line-clamp-1'>
                                                        -{{ $menu->notes }}
                                                    </p>
                                                    <p class='font-light text-sm text-white line-clamp-1'>
                                                        -
                                                        @if ($menu->discount)
                                                            {{ $menu->discount->name }} -
                                                            {{ $menu->discount->percentage }} %
                                                        @endif
                                                    </p>
                                                    <h2 class='font-bold text-sm my-auto text-white'>
                                                        {{ number_format($menu->menu->price, 0, ',', '.') }}
                                                    </h2>

                                                </div>
                                                <div class="bg-red-500 text-white p-2 rounded-lg text-center">
                                                    <form class="text-center" method="post"
                                                        action="{{ route('removecart', ['id' => $menu->id]) }}">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <div class="text-center">
                            <a class="hover:text-black text-white text-xl bg-green-500 p-2 w-fit rounded-lg"
                                href="{{ route('addcart') }}">Add Product</a>
                        </div>
                        <form id="orderForm" class="space-y-5" method="post" action="{{ route('postorder') }}">
                            @csrf
                            @method('post')
                            <div class="space-y-2">
                                <label class="font-semibold text-black text-lg">Atas nama:</label>
                                <input type="name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                    id="atas_nama" name="atas_nama" required>
                            </div>
                            <div class="space-y-2">
                                <label class="font-semibold text-black text-lg">No Telpon:</label>
                                <input type="name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                    id="no_telpon" name="no_telpon" required>
                            </div>

                            <div class="flex justify-between">
                                <div class="flex gap-2 my-auto">
                                    <label class="text-black font-bold text-2xl">Total:</label>
                                    <h1 class="text-2xl">{{ number_format($cart->total_amount, 0, ',', '.') }}</h1>
                                </div>
                                <button id="submitBtn" type="submit"
                                    class="text-xl p-2 w-fit rounded-lg {{ $cart->total_amount <= 0 ? 'bg-gray-200 cursor-not-allowed text-black' : 'bg-blue-500 hover:bg-blue-600 text-white' }}"
                                    {{ $cart->total_amount <= 0 ? 'disabled' : '' }}>
                                    Make Order
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
    </main>
    <script>
        const form = document.getElementById('orderForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
        });
    </script>
</body>

</html>
