<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        $users = User::where('level', 'User')->get();
        
        return view('user', compact('users'));
    }

    public function destroy($id)
    {
        User::destroy($id);

        return redirect(route('user'))->with('success', 'User Berhasil Dihapus !');
    }
}
