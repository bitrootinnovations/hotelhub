<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\ClientMaster;
use Illuminate\Http\Request;

class AppSettingsController extends Controller
{
    public function upi()
    {
        $settings = [
            'upi_id'        => AppSetting::get('upi_id',            ''),
            'plan_name'     => AppSetting::get('upi_plan_name',     'Basic Plan'),
            'business_name' => AppSetting::get('upi_business_name', 'HotelHub'),
            'note'          => AppSetting::get('upi_note',          'Monthly subscription'),
        ];

        $clients = ClientMaster::orderBy('client_name')
            ->get(['client_id', 'client_name', 'subscription_price']);

        return view('settings.upi', compact('settings', 'clients'));
    }

    public function updateUpi(Request $request)
    {
        $request->validate([
            'upi_id'        => 'required|string|max:100',
            'plan_name'     => 'nullable|string|max:100',
            'business_name' => 'nullable|string|max:100',
            'note'          => 'nullable|string|max:200',
        ]);

        $adminId = auth()->id();

        AppSetting::set('upi_id',            $request->upi_id,                                          $adminId);
        AppSetting::set('upi_plan_name',     $request->input('plan_name',     'Basic Plan'),             $adminId);
        AppSetting::set('upi_business_name', $request->input('business_name', 'HotelHub'),               $adminId);
        AppSetting::set('upi_note',          $request->input('note',          'Monthly subscription'),   $adminId);

        return redirect()->route('settings.upi')->with('success', 'UPI settings updated successfully.');
    }

    public function updateClientAmount(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:client_masters,client_id',
            'amount'    => 'required|numeric|min:0',
        ]);

        ClientMaster::where('client_id', $request->client_id)
            ->update(['subscription_price' => $request->amount]);

        return response()->json(['success' => true, 'message' => 'Amount updated']);
    }
}
