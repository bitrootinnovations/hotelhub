<?php

namespace App\Http\Controllers\Api;

use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrinterController extends BaseApiController
{
    /**
     * POST /api/v1/printer/status
     *
     * Called by the mobile app whenever a BLE printer connects or disconnects.
     * Upserts by (client_id + mac_address).
     *
     * Body: { mac_address, device_name, status: "online"|"offline" }
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_address'  => 'required|string|max:50',
            'device_name'  => 'nullable|string|max:100',
            'status'       => 'required|in:online,offline',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $clientId = $request->attributes->get('client_id');

        $printer = Printer::updateOrCreate(
            [
                'client_id'   => $clientId,
                'mac_address' => strtoupper($request->mac_address),
            ],
            [
                'device_name' => $request->device_name,
                'status'      => $request->status,
                'last_seen_at'=> now(),
            ]
        );

        return $this->success([
            'printer_id'   => $printer->id,
            'mac_address'  => $printer->mac_address,
            'device_name'  => $printer->device_name,
            'status'       => $printer->status,
            'last_seen_at' => $printer->last_seen_at?->toIso8601String(),
        ], 'Printer status updated');
    }
}
