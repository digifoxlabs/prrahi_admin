<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SalesPerson;
use Spatie\Permission\Models\Role;

class SalesAuthController extends Controller
{
    
    //Show Login Form
        public function showLoginForm(){

            return view('sales.login');
        }

    //Login Post
    public function login(Request $request){

          $credentials = $request->only('login_id', 'password');
    
            if (Auth::guard('sales')->attempt($credentials)) {
                return redirect()->route('sales.dashboard');
            }

          return back()->withErrors(['login_id' => 'Invalid credentials']);

    }

    //Handle Logout

        public function logout(Request $request) {

            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        
            return redirect()->route('sales.login');
           // return redirect('/admin/login');
        }



}
