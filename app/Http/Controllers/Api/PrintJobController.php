<?php

namespace App\Http\Controllers\Api;

use App\Models\AppSetting;
use App\Models\ClientMaster;
use App\Models\PrintJob;
use Illuminate\Http\Request;

class PrintJobController extends BaseApiController
{
    /**
     * GET /api/v1/print/jobs/pending
     *
     * Mobile app polls this endpoint (when printer is connected).
     * Returns all pending print jobs for the authenticated client.
     * Marks them as picked so they aren't served twice.
     */
    public function pending(Request $request)
    {
        $clientId = $request->attributes->get('client_id');

        $jobs = PrintJob::where('client_id', $clientId)
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();

        if ($jobs->isEmpty()) {
            return $this->success([], 'No pending print jobs');
        }

        // Mark picked_at so we know the app fetched them
        PrintJob::whereIn('id', $jobs->pluck('id'))
            ->update(['picked_at' => now()]);

        return $this->success(
            $jobs->map(fn($j) => [
                'id'      => $j->id,
                'type'    => $j->type,
                'payload' => $j->payload,
            ])->values(),
            'Print jobs fetched'
        );
    }

    /**
     * PUT /api/v1/print/jobs/{id}/done
     *
     * Mobile app calls this after successfully printing (or on failure).
     * Body: { "status": "done" | "failed" }
     */
    public function markDone(Request $request, $id)
    {
        $clientId = $request->attributes->get('client_id');

        $job = PrintJob::where('id', $id)
            ->where('client_id', $clientId)
            ->first();

        if (!$job) {
            return $this->error('Print job not found', 404);
        }

        $status = in_array($request->status, ['done', 'failed']) ? $request->status : 'done';

        $job->update([
            'status'  => $status,
            'done_at' => now(),
        ]);

        return $this->success(null, 'Print job marked as ' . $status);
    }

    /**
     * POST /api/v1/admin/print/upi-qr   (web session auth — called from admin panel)
     *
     * Creates a UPI QR print job for the given client.
     * Body: { client_id }
     */
    public function createUpiQrJob(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:client_masters,client_id',
        ]);

        $client = ClientMaster::findOrFail($request->client_id);

        $upiId    = AppSetting::get('upi_id', '');
        $bizName  = AppSetting::get('upi_business_name', 'HotelHub');
        $planName = AppSetting::get('upi_plan_name', 'Basic Plan');
        $note     = AppSetting::get('upi_note', 'Monthly subscription');
        $amount   = $client->subscription_price ?? 0;

        $upiString = $upiId
            ? "upi://pay?pa={$upiId}&pn=" . urlencode($bizName) . "&am={$amount}&cu=INR&tn=" . urlencode($note)
            : null;

        if (!$upiString) {
            return response()->json(['success' => false, 'message' => 'UPI ID is not set. Please configure it in UPI Settings first.'], 422);
        }

        $job = PrintJob::create([
            'client_id' => $client->client_id,
            'type'      => 'upi_qr',
            'status'    => 'pending',
            'payload'   => [
                'upi_id'        => $upiId,
                'amount'        => (float) $amount,
                'plan_name'     => $planName,
                'business_name' => $bizName,
                'client_name'   => $client->client_name,
                'note'          => $note,
                'upi_string'    => $upiString,
            ],
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Print job created. The mobile app will print when it polls.',
            'job_id'   => $job->id,
            'client'   => $client->client_name,
        ]);
    }
}
