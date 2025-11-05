<!DOCTYPE html>
<html lang="en">

<head>
    <title>Detail Withdraw</title>
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
                <div class="p-3 space-y-4">
                    <div class="">
                        <h1 class="font-extrabold text-3xl">Detail Withdraw</h1>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('sweetalert::alert')

</body>

</html>
