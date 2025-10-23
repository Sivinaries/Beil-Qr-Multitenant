<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
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
            <div class='w-full rounded-lg h-fit mx-auto'>
                <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-4 lg:grid-cols-4 gap-4 p-2">
                    <!-- card1 -->
                    <a href="{{ route('order') }}">
                        <div class="bg-red-500 p-6 rounded-lg shadow-xl">
                            <h1 class="text-2xl text-white font-bold">{{ $total_order }}</h1>
                            <h1 class="text-xl font-light text-white text-right">Order</h1>
                        </div>
                    </a>
                    <!-- card2 -->
                    <a href="{{ 'product' }}">
                        <div class="bg-blue-500 p-6 rounded-lg shadow-xl">
                            <h1 class="text-2xl text-white font-bold">{{ $total_product }}</h1>
                            <h1 class="text-xl font-light text-white text-right">Product</h1>
                        </div>
                    </a>
                    <!-- card3 -->
                    <a href="{{ route('chair') }}">
                        <div class="bg-green-500 p-6 rounded-lg shadow-xl">
                            <h1 class="text-2xl text-white font-bold">{{ $total_users }}</h1>
                            <h1 class="text-xl font-light text-white text-right">Chairs</h1>
                        </div>
                    </a>
                    <!-- card4 -->
                    <a href="#">
                        <div class="bg-yellow-500 p-6 rounded-lg shadow-xl">
                            <h1 class="text-2xl text-white font-bold">{{ $top_seller }}</h1>
                            <h1 class="text-xl font-light text-white text-right">Top Seller</h1>
                        </div>
                    </a>
                </div>
            </div>

            <!-- chart section -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-2 lg:grid-cols-2 ">
                <!-- chart 1: Total Order -->
                <div class="p-6 bg-white rounded-lg shadow-xl">
                    <h1 class="font-light">Total Order</h1>
                    <i class="fa fa-arrow-up text-lime-500"></i>
                    <canvas id="grafikHistoy" width="100" height="50"></canvas>
                </div>
                <!-- chart 2: Total Revenue -->
                <div class="p-6 bg-white rounded-lg shadow-xl">
                    <h1 class="font-light">Total Revenue</h1>
                    <i class="fa fa-arrow-up text-lime-500"></i>
                    <canvas id="grafikRevenue" width="100" height="50"></canvas>
                </div>
                <!-- chart 3: Settlement -->
                <div class="p-6 bg-white rounded-lg shadow-xl">
                    <h1 class="font-light">Settlement</h1>
                    <i class="fa fa-arrow-up text-lime-500"></i>
                    <label for="dateSelect">Select date:</label>
                    <select class="border bg-gray-100 p-2 rounded-lg" id="dateSelect" onchange="updateChart()">
                        @foreach ($labels3 as $date)
                            <option value="{{ $date }}" {{ $selectedDate == $date ? 'selected' : '' }}>
                                {{ $date }}</option>
                        @endforeach
                    </select>
                    <canvas id="grafikSettlement" width="100" height="50"></canvas>
                </div>
                <!-- chart 4: Total Expense -->
                <div class="p-6 bg-white rounded-lg shadow-xl">
                    <h1 class="font-light">Total Expense</h1>
                    <i class="fa fa-arrow-up text-lime-500"></i>
                    <canvas id="grafikExpense" width="100" height="50"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('layout.script')
    @include('sweetalert::alert')

</body>

</html>
