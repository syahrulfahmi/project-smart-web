<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use function Laravel\Prompts\error;

class AuthController extends Controller
{
    //

    public function index(){
        return view('auth.login');
    }

    public function postLogin(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //try authentication

        if (Auth::attempt($request->only('email','password'))) {
            $user = Auth::user();
            if ($user->role == 'admin') {
                # code...
                return redirect('/admin');
            }
            return redirect('/user'); // mengarahkan ke user
            # code...
        }
        return redirect('/login')->with('error','wrong email or password');
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect('/');
    }

}
