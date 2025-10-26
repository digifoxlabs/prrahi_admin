<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminAuthController extends Controller
{
    //Show Login Form
    public function showLoginForm(){

        return view('admin.login');
    }

    //Login Post
    public function login(Request $request){

          $credentials = $request->only('email', 'password');
    
            if (Auth::guard('admin')->attempt($credentials)) {
                return redirect()->route('admin.dashboard');
            }

          return back()->withErrors(['email' => 'Invalid credentials']);

    }

    //Handle Logout

        public function logout(Request $request) {

            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        
            return redirect()->route('admin.login');
           // return redirect('/admin/login');
        }




}
