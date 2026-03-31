<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_settings')->only(['index']);
        $this->middleware('permission:edit_settings')->only(['update']);
        $this->middleware('permission:create_settings')->only(['store']);
        $this->middleware('permission:delete_settings')->only(['destroy']);
    }

    public function index()
    {
         $title ='Settings';
        $distributorOrderSettings = Setting::where('group', 'distributor_orders')->pluck('value', 'key');
        $retailOrderSettings = Setting::where('group', 'retail_orders')->pluck('value', 'key');
        $invoiceSettings = Setting::where('group', 'invoice_template')->pluck('value', 'key');

        return view('admin.pages.settings', compact(
            'distributorOrderSettings',
            'retailOrderSettings',
            'invoiceSettings',
            'title'
        ));
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
