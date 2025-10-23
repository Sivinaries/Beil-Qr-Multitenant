<div class="flex">
    <aside id="sidebar"
        class="font-poppins fixed inset-y-0 my-6 ml-4 w-full max-w-72 md:max-w-60 xl:max-w-64 2xl:max-w-64 z-50 rounded-lg bg-white shadow-2xl overflow-y-scroll transform transition-transform duration-300 -translate-x-full md:translate-x-0 ease-in-out">
        <div class="p-2">
            <div class="p-4">
                <a class="text-center" href="{{ route('dashboard') }}">
                    <img class="w-24 h-12 mx-auto" src="{{ asset('beil.svg') }}" alt="">
                </a>
            </div>
            <hr class="mx-5 shadow-2xl bg-transparent rounded-r-lg rounded-l-lg" />
            <div>
                <ul class="">
                    @if (auth()->user()->level == 'User')
                        <li class="p-4 mx-2">
                            <a class="" href="{{ route('dashboard') }}">
                                <div class="flex space-x-4">
                                    <div class="bg-red-600 p-2 rounded-lg">
                                        <i class="material-icons text-white">home</i>
                                    </div>
                                    <div class="my-auto">
                                        <h1 class="text-gray-500 hover:text-black text-base font-normal">Dashboard</h1>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="p-4 mx-2">
                            <a class="" href="{{ route('order') }}">
                                <div class="flex space-x-4">
                                    <div class="bg-red-600 p-2 rounded-lg">
                                        <i class="material-icons text-white">shopping_cart</i>
                                    </div>
                                    <div class="my-auto">
                                        <h1 class="text-gray-500 hover:text-black text-base font-normal">Order</h1>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->level == 'Admin')
                        <li class="p-4 mx-2">
                            <a class="" href="{{ route('dashboard') }}">
                                <div class="flex space-x-4">
                                    <div class="bg-red-600 p-2 rounded-lg">
                                        <i class="fa-solid fa-house"></i>
                                    </div>
                                    <div class="my-auto">
                                        <h1 class="text-gray-500 hover:text-black text-base font-normal">Home</h1>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endif
                   <li class="p-4 mx-2">
                        <div class="flex space-x-4">
                            <div class="bg-red-600 p-2 rounded-lg">
                                <i class="material-icons text-white">settings</i>
                            </div>
                            <div class="my-auto">
                                <h1 class="text-black text-base font-normal">Manage</h1>
                            </div>
                        </div>
                    </li>
                    <hr class="mx-5 shadow-2xl bg-transparent rounded-r-lg rounded-l-lg" />
                    @if (auth()->user()->level == 'User')
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('chair') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Chairs</h1>
                                </a>
                            </div>
                        </li>
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('category') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Category</h1>
                                </a>
                            </div>
                        </li>
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('showcase') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Showcase</h1>
                                </a>
                            </div>
                        </li>
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('product') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Product</h1>
                                </a>
                            </div>
                        </li>
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('discount') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Discount</h1>
                                </a>
                            </div>
                        </li>
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('settlement') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Settlement</h1>
                                </a>
                            </div>
                        </li>
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('expense') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Expense</h1>
                                </a>
                            </div>
                        </li>
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('history') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">History</h1>
                                </a>
                            </div>
                        </li>
                    @endif
                    @if (auth()->user()->level == 'Admin')
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('user') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">User</h1>
                                </a>
                            </div>
                        </li>
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('store') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Store</h1>
                                </a>
                            </div>
                        </li>
                        <li class="p-4 mx-2">
                            <div class="ml-16 md:ml-14">
                                <a href="{{ route('history') }}">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Payment</h1>
                                </a>
                            </div>
                        </li>
                    @endif
                    <hr class="mx-5 shadow-2xl bg-transparent rounded-r-lg rounded-l-lg" />
                    <li class="p-4 mx-2">
                        <a class="" href="">
                            <div class="flex space-x-4">
                                <div class="bg-red-600 p-2 rounded-lg">
                                    <i class="material-icons text-white">support_agent</i>
                                </div>
                                <div class="my-auto">
                                    <h1 class="text-gray-500 hover:text-black text-base font-normal">Support</h1>
                                </div>
                            </div>
                        </a>
                    </li>

                    <li class="p-4 mx-2">
                        <form class="" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <div class="flex space-x-4">
                                <div class="bg-red-600 p-2 rounded-lg">
                                    <i class="material-icons font-extrabold rotate-180 text-white">logout</i>
                                </div>
                                <button class="text-gray-500 hover:text-black text-base font-normal" type="submit">
                                    Logout
                                </button>
                            </div>
                        </form>
                    </li>

                </ul>
            </div>
        </div>
    </aside>
</div>
