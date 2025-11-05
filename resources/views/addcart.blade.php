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
                        <h1 class="font-extrabold text-3xl">Create Order</h1>
                    </div>
                    <div class="grid grid-cols-2 xl:grid-cols-6 lg:grid-cols-6 gap-2 p-2">
                        @foreach ($menus as $menu)
                            <div class='w-full'>
                                <a href="{{ route('showproduct', ['id' => $menu->id]) }}">
                                    <div class='p-2 rounded-lg relative bg-red-900 space-y-2'>
                                        <div class='space-y-2'>
                                            <div class='bg-gray-100 p-2 rounded-lg '>
                                                <img src="{{ asset('storage/img/' . basename($menu->img)) }}"
                                                    alt="Product Image"
                                                    class='mx-auto my-auto w-14 h-17 rounded-lg relative' />
                                            </div>
                                            <div class='space-y-1'>
                                                <h1 class='font-extrabold text-sm text-white'>{{ $menu->name }}</h1>
                                                <p class='font-light text-sm text-white line-clamp-1'>
                                                    {{ $menu->description }}
                                                </p>
                                                <h2 class='font-bold text-sm my-auto text-white'>
                                                    {{ number_format($menu->price, 0, ',', '.') }}
                                                </h2>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('sweetalert::alert')

</body>

</html>
