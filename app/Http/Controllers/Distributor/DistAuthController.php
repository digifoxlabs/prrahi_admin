<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Distributor;
use Spatie\Permission\Models\Role;

class DistAuthController extends Controller
{
    //Show Login Form
    public function showLoginForm(){

        return view('distributor.login');
    }

    //Login Post
    public function login(Request $request){

          $credentials = $request->only('login_id', 'password');
    
            if (Auth::guard('distributor')->attempt($credentials)) {
                return redirect()->route('distributor.dashboard');
            }

          return back()->withErrors(['login_id' => 'Invalid credentials1']);

    }

    //Handle Logout

        public function logout(Request $request) {

            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        
            return redirect()->route('distributor.login');
           // return redirect('/admin/login');
        }


}
