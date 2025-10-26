<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
         $title ='Settings';   
        // Group settings
        $productSettings = Setting::where('group', 'products')->pluck('value', 'key');
        $distributorSettings = Setting::where('group', 'distributors')->pluck('value', 'key');
        $orderSettings = Setting::where('group', 'orders')->pluck('value', 'key');

        return view('admin.pages.settings', compact('productSettings', 'distributorSettings','orderSettings','title'));
    }

    public function update(Request $request)
    {
        foreach ($request->input('settings', []) as $group => $settings) {
            foreach ($settings as $key => $value) {
                Setting::set($group, $key, $value);
            }
        }

        return back()->with('success', 'Settings updated.');
    }
}
