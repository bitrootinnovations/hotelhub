<?php

namespace App\Http\Controllers\Api;

use App\Models\AppSetting;
use App\Models\ClientMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpiSettingsController extends BaseApiController
{
    /**
     * GET /api/v1/settings/upi
     *
     * Returns UPI ID + this client's plan amount so the mobile app
     * can generate a payment QR code.
     */
    public function show(Request $request)
    {
        $clientId = $request->attributes->get('client_id');
        $client   = ClientMaster::find($clientId);
        $amount   = $client && $client->subscription_price > 0
            ? $client->subscription_price
            : 0;

        return $this->success($this->upiData($amount), 'UPI settings fetched');
    }

    /**
     * GET /api/v1/admin/settings/upi  (admin session / web auth)
     */
    public function adminShow()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->upiData(),
        ]);
    }

    /**
     * PUT /api/v1/admin/settings/upi  (admin session / web auth)
     */
    public function adminUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'upi_id'        => 'required|string|max:100',
            'plan_name'     => 'nullable|string|max:100',
            'business_name' => 'nullable|string|max:100',
            'note'          => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $adminId = auth()->id();

        AppSetting::set('upi_id',            $request->upi_id,                                          $adminId);
        AppSetting::set('upi_plan_name',     $request->input('plan_name',     'Basic Plan'),             $adminId);
        AppSetting::set('upi_business_name', $request->input('business_name', 'HotelHub'),               $adminId);
        AppSetting::set('upi_note',          $request->input('note',          'Monthly subscription'),   $adminId);

        return response()->json([
            'success' => true,
            'message' => 'UPI settings updated successfully',
            'data'    => $this->upiData(),
        ]);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────────

    private function upiData($amount = null): array
    {
        $upiId    = AppSetting::get('upi_id',            '');
        $planName = AppSetting::get('upi_plan_name',     'Basic Plan');
        $bizName  = AppSetting::get('upi_business_name', 'HotelHub');
        $note     = AppSetting::get('upi_note',          'Monthly subscription');

        if ($amount === null) {
            $amount = 0;
        }

        return [
            'upi_id'        => $upiId,
            'amount'        => (float) $amount,
            'plan_name'     => $planName,
            'business_name' => $bizName,
            'note'          => $note,
            'upi_string'    => $upiId
                ? "upi://pay?pa={$upiId}&pn=" . urlencode($bizName) . "&am={$amount}&cu=INR&tn=" . urlencode($note)
                : null,
        ];
    }
}
