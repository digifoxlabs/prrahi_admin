<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Distributor;
use App\Models\SalesPerson;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
        public function dashboard(){       

         $title ='Dashboard';

        return view('distributor.pages.dashboard', compact('title'));
        // return redirect()->route('admin.profile')->with('success', 'Profile photo updated successfully!');
       
    }
}
