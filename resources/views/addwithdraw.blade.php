<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add Withdraw</title>
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
            <div class="w-full bg-white rounded-lg h-fit mx-auto">
                <div class="p-3 text-center">
                    <h1 class="font-extrabold text-3xl">Add withdraw</h1>
                </div>
                <div class="p-6">
                    <form id="totalForm" class="space-y-3" method="post" action="{{ route('postwithdraw') }}"
                        enctype="multipart/form-data">
                        @csrf @method('post')
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-2">
                            <div class="space-y-2">
                                <label class="font-semibold text-black">Atas Nama:</label>
                                <input type="text"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                    id="name" name="name" required />
                            </div>
                            <div class="space-y-2">
                                <label class="font-semibold text-black">Payment Type:</label>
                                <select id="payment_type" name="payment_type"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                    required>
                                    <option></option>
                                    <option value="BCA">BCA</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-2">
                            <div class="space-y-2">
                                <label class="font-semibold text-black">Jumlah:</label>
                                <input type="number"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                    id="amount" name="amount" required />
                            </div>
                            <div class="space-y-2">
                                <label class="font-semibold text-black">No Rekening:</label>
                                <input type="number"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full"
                                    id="no_rek" name="no_rek" required />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="font-semibold text-black">Catatan:</label>
                            <textarea class="bg-gray-50 border border-gray-300 text-gray-900 p-2 rounded-lg w-full" id="note" name="note"
                                required></textarea>
                            <p class="text-gray-500 text-right"><span id="charCount"></span>/200 characters</p>
                        </div>

                        <button id="submitBtn" type="submit"
                            class="bg-blue-500 text-white p-4 w-full hover:text-black rounded-lg">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.getElementById('note').addEventListener('input', function() {
            var maxLength = 200;
            var currentLength = this.value.length;

            document.getElementById('charCount').innerText = currentLength + '/' + maxLength;

            if (currentLength >= maxLength) {
                this.setAttribute('disabled', true);
            } else {
                this.removeAttribute('disabled');
            }
        });
        const form = document.getElementById('totalForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
        });
    </script>
</body>

</html>
