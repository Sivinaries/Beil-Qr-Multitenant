<!DOCTYPE html>
<html lang="en">

<head>
    <title>Akun</title>
    @include('user.layout.head')
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
                            <h1 class="text-center text-xl font-extralight">Akun</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-20"></div>

            {{-- BODY --}}
            <div class="p-4 space-y-4">
                <div class="space-y-2">
                    <h1><span class="text-red-500">*</span> Nama</h1>
                    <input class="border w-full rounded-lg p-2" type="text" id="layanan" name="layanan"
                        value="{{ $chair->name }}" readonly>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
