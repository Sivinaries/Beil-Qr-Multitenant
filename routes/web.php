<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChairController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Pagescontroller;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\HistoyController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\SettlementController;

//AUTH CONTROLLER
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
Route::match(['get', 'post'], '/signin', [AuthController::class, 'signin'])->name('signin');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//GOOGLE CONTROLLER
Route::get('/auth/redirect', [GoogleController::class, 'redirectToGoogle'])->name('auth-google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('call-google');

Route::middleware('auth:web')->group(function () {
    //ADMIN

    //PAGES CONTROLLER
    Route::get('/dashboard', [Pagescontroller::class, 'dashboard'])->name('dashboard');
    Route::get('/profil', [Pagescontroller::class, 'profil'])->name('profil');

    //STORE CONTROLLER
    Route::get('/store', [StoreController::class, 'index'])->name('store');
    Route::get('/addstore', [StoreController::class, 'create'])->name('addstore');
    Route::post('/poststore', [StoreController::class, 'store'])->name('poststore');
    Route::delete('/store/{id}/delete', [StoreController::class, 'destroy'])->name('delstore');

    //CHAIR CONTROLLER
    Route::get('/chair', [ChairController::class, 'index'])->name('chair');
    Route::get('/addchair', [ChairController::class, 'create'])->name('addchair');
    Route::post('/postchair', [ChairController::class, 'store'])->name('postchair');
    Route::delete('/chair/{id}/delete', [ChairController::class, 'destroy'])->name('delchair');

    //USER CONTROLLER
    Route::get('/users', [UserController::class, 'index'])->name('user');
    Route::get('/createuser', [UserController::class, 'create'])->name('adduser');
    Route::post('/postuser', [UserController::class, 'store'])->name('postuser');
    Route::delete('/user/{id}/delete', [UserController::class, 'destroy'])->name('deluser');

    //ORDER CONTROLLER
    Route::get('/order', [OrderController::class, 'index'])->name('order');
    Route::get('/createorder', [OrderController::class, 'create'])->name('addorder');
    Route::post('/postorder', [OrderController::class, 'store'])->name('postorder');
    Route::delete('/order/{id}/delete', [OrderController::class, 'destroy'])->name('delorder');
    Route::post('/order/{orderId}/archive', [OrderController::class, 'archive'])->name('archive');
    Route::post('/cashpayment', [OrderController::class, 'cashpayment'])->name('cashpayment');

    //MENU CONTROLLER
    Route::get('/product', [ProductController::class, 'index'])->name('product');
    Route::get('/createproduct', [ProductController::class, 'create'])->name('addproduct');
    Route::post('/postproduct', [ProductController::class, 'store'])->name('postproduct');
    Route::get('/editproduct/{id}', [ProductController::class, 'edit'])->name('editproduct');
    Route::get('/product/{id}/show', [ProductController::class, 'show'])->name('showproduct');
    Route::put('/product/{id}/update', [ProductController::class, 'update'])->name('updateproduct');
    Route::delete('/product/{id}/delete', [ProductController::class, 'destroy'])->name('delproduct');

    //CATEGORY CONTROLLER
    Route::get('/category', [CategoryController::class, 'index'])->name('category');
    Route::get('/addcategory', [CategoryController::class, 'create'])->name('addcategory');
    Route::post('/postcategory', [CategoryController::class, 'store'])->name('postcategory');
    Route::get('/editcategory/{id}', [CategoryController::class, 'edit'])->name('editcategory');
    Route::put('/category/{id}/update', [CategoryController::class, 'update'])->name('updatecategory');
    Route::delete('/category/{id}/delete', [CategoryController::class, 'destroy'])->name('delcategory');

    //SHOWCASE CONTROLLER
    Route::get('/showcase', [ShowcaseController::class, 'index'])->name('showcase');
    Route::get('/addshowcase', [ShowcaseController::class, 'create'])->name('addshowcase');
    Route::post('/postshowcase', [ShowcaseController::class, 'store'])->name('postshowcase');
    Route::get('/editshowcase/{id}', [ShowcaseController::class, 'edit'])->name('editshowcase');
    Route::put('/showcase/{id}/update', [ShowcaseController::class, 'update'])->name('updateshowcase');
    Route::delete('/showcase/{id}/delete', [ShowcaseController::class, 'destroy'])->name('delshowcase');

    //HISTORY CONTROLLER
    Route::get('/history', [Histoycontroller::class, 'index'])->name('history');
    Route::get('/export-orders', [HistoyController::class, 'exportOrders'])->name('exportOrders');

    //CART  CONTROLLER
    Route::get('/cart', [Cartcontroller::class, 'index'])->name('addcart');
    Route::post('/postcart', [CartController::class, 'store'])->name('postcart');
    Route::delete('/cart/{id}/delete', [CartController::class, 'destroy'])->name('removecart');

    //DISCOUNT CONTROLLER
    Route::get('/discount', [DiscountController::class, 'index'])->name('discount');
    Route::get('/adddiscount', [DiscountController::class, 'create'])->name('adddiscount');
    Route::post('/postdiscount', [DiscountController::class, 'store'])->name('postdiscount');
    Route::get('/editdiscounts/{id}', [DiscountController::class, 'edit'])->name('editdiscount');
    Route::put('/discounts/{id}/update', [DiscountController::class, 'update'])->name('updatediscount');
    Route::delete('/discounts/{id}/delete', [DiscountController::class, 'destroy'])->name('deldiscount');

    //EXPENSE CONTROLLER
    Route::get('/expense', [ExpenseController::class, 'index'])->name('expense');
    Route::get('/addexpense', [ExpenseController::class, 'create'])->name('addexpense');
    Route::post('/postexpense', [ExpenseController::class, 'store'])->name('postexpense');
    Route::get('/editexpense/{id}', [ExpenseController::class, 'edit'])->name('editexpense');
    Route::put('/expense/{id}/update', [ExpenseController::class, 'update'])->name('updateexpense');
    Route::delete('/expense/{id}/delete', [ExpenseController::class, 'destroy'])->name('delexpense');

    //SETTLEMENT CONTROLLER
    Route::get('/settlement', [SettlementController::class, 'index'])->name('settlement');
    Route::get('/settlement/{id}/show', [SettlementController::class, 'show'])->name('showsettlement');
    Route::delete('/settlement/{id}/delete', [SettlementController::class, 'destroy'])->name('delsettlement');
    Route::get('/addstartamount', [SettlementController::class, 'startamount'])->name('addstartamount');
    Route::get('/addtotalamount', [SettlementController::class, 'totalamount'])->name('addtotalamount');
    Route::post('/createstart', [SettlementController::class, 'poststart'])->name('poststart');
    Route::post('/createtotal', [SettlementController::class, 'posttotal'])->name('posttotal');

    //WITHDRAW CONTROLLER
    Route::get('/withdraw', [WithdrawController::class, 'index'])->name('withdraw');
    Route::get('/addwithdraw', [WithdrawController::class, 'create'])->name('addwithdraw');
    Route::post('/postwithdraw', [WithdrawController::class, 'store'])->name('postwithdraw');
    Route::delete('/expense/{id}/show', [WithdrawController::class, 'show'])->name('showwithdraw');

    //QR CONTROLLER
    Route::get('/login/qr/{id}', [QrController::class, 'LoginQr'])->name('login-qr');

    //CONSULT
    Route::get('/bot', [ChatController::class, 'bot'])->name('bot');
    Route::post('/gen', [ChatController::class, 'gen'])->name('gen');
});

Route::middleware('auth:chair')->group(function () {

    //CUSTOMER
    Route::get('/user/home', [CustomerController::class, 'home'])->name('user-home');
    Route::get('/user/product', [CustomerController::class, 'product'])->name('user-product');
    Route::get('/user/cart', [CustomerController::class, 'cart'])->name('user-cart');
    Route::get('/user/payment', [CustomerController::class, 'payment'])->name('user-payment');
    Route::get('/user/product/{id}', [CustomerController::class, 'show'])->name('user-show');
    Route::post('/user/postcart', [CustomerController::class, 'postcart'])->name('user-postcart');
    Route::delete('/user/cart/{id}/delete', [CustomerController::class, 'removecart'])->name('user-removecart');
    Route::post('/user/serve/dineIn', [CustomerController::class, 'postdineIn'])->name('user-postdineIn');
    Route::post('/user/serve/takeAway', [CustomerController::class, 'posttakeAway'])->name('user-posttakeAway');
    Route::post('/user/postorder', [CustomerController::class, 'postorder'])->name('user-postorder');
    Route::get('/user/serve', [CustomerController::class, 'serve'])->name('user-serve');
    Route::get('/user/antrian', [CustomerController::class, 'antrian'])->name('user-antrian');
    Route::get('/user/game', [CustomerController::class, 'game'])->name('user-game');
    Route::get('/user/akun', [CustomerController::class, 'akun'])->name('user-akun');
});
