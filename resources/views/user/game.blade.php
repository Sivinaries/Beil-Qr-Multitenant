<!DOCTYPE html>
<html lang="en">

<head>
    <title>Game</title>
    @include('user.layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto h-screen '>
        <div class='sm:max-w-sm'>
            {{-- NAVBAR --}}
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-6 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                    <div class="flex ">
                        <a href="{{ route('user-home') }}">
                            <div>
                                <img src="{{ asset('/img/home.svg') }}" alt="">
                            </div>
                        </a>
                        <div class="mx-auto">
                            <h1 class="text-center text-xl font-extralight">Game</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-20"></div>

            {{-- BODY --}}
            <div class="p-4 ">
                <div>
                    <h1 class="text-4xl font-light text-center">Coming Soon !!!!</h1>
                </div>

            </div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = new DataTable('#myTable', {
              
            });
        });
    </script>

</body>

</html>
