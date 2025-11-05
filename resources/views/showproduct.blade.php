<!DOCTYPE html>
<html lang="en">

<head>
    <title>Products</title>
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
            <div class='w-full rounded-lg bg-white h-fit mx-auto'>
                <div class="p-3">
                    <div class="p-3 text-center">
                        <h1 class="font-extrabold text-3xl">Detailes</h1>
                    </div>
                    <div class='my-0 lg:my-8 xl:my-8 2xl:my-8'>
                        <div class='p-4 space-y-8'>
                            <form action="{{ route('postcart') }}" method="post">
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                <input type="hidden" name="quantity" id="quantityInput" value="1">
                                <div class='grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-2 gap-2'>
                                    <div class='p-2 rounded-lg'>
                                        <img src="{{ asset('storage/img/' . basename($menu->img)) }}"
                                            alt="Product Image" class='mx-auto my-auto w-48 h-96 rounded-lg relative' />
                                    </div>
                                    <div class='space-y-6'>
                                        <h1 class='font-extrabold text-2xl text-black '>{{ $menu->name }}</h1>
                                        <p class='font-light text-lg text-black'>
                                            {{ $menu->description }}
                                        </p>
                                        <h2 class='font-bold text-xl text-black my-auto'>
                                            {{ number_format($menu->price, 0, ',', '.') }}
                                        </h2>
                                        <div>
                                            <p class='w-full text-xl font-medium rounded-lg p-2'>*Discount</p>
                                            <select name="discount_id" class='border p-2 w-full bg-gray-50 rounded-lg'>
                                                <option value="">No Discount</option>
                                                @foreach ($discount as $discount)
                                                    <option value="{{ $discount->id }}">{{ $discount->name }}
                                                        ({{ $discount->percentage }}%)</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <p class='w-full text-xl font-medium rounded-lg p-2'>*Notes</p>
                                            <textarea name="notes" class='border p-2 w-full bg-gray-50 rounded-lg'>{{ $menu->notes }}</textarea>
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class='grid grid-cols-2 w-full'>
                                    <div class=''>
                                        <div class='flex items-center justify-center space-x-6'>
                                            <button type="button"
                                                class='bg-black bg-opacity-90 text-white font-bold text-base rounded-lg w-12 h-12 flex items-center justify-center'
                                                onclick="decrement()">
                                                -
                                            </button>
                                            <div class='text-black text-center' id="quantityDisplay">1</div>
                                            <button type="button"
                                                class='bg-black bg-opacity-90 text-white font-bold text-base rounded-lg w-12 h-12 flex items-center justify-center'
                                                onclick="increment()">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class='bg-black bg-opacity-90 text-white font-bold text-base rounded-full  text-center'>
                                        Add To Cart
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        function increment() {
            var quantityDisplay = document.getElementById('quantityDisplay');
            var quantityInput = document.getElementById('quantityInput');
            var quantity = parseInt(quantityDisplay.innerText);
            quantity++;
            quantityDisplay.innerText = quantity;
            quantityInput.value = quantity;
        }

        function decrement() {
            var quantityDisplay = document.getElementById('quantityDisplay');
            var quantityInput = document.getElementById('quantityInput');
            var quantity = parseInt(quantityDisplay.innerText);
            if (quantity > 1) {
                quantity--;
                quantityDisplay.innerText = quantity;
                quantityInput.value = quantity;
            }
        }
    </script>

    @include('sweetalert::alert')

</body>

</html>
