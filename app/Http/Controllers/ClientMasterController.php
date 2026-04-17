<?php

namespace App\Http\Controllers;

use App\Models\ClientMaster;
use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ClientMasterController extends Controller
{
    public function index()
    {
        return view('clients.all');
    }

    public function allData()
    {
        $clients = ClientMaster::orderBy('created_at', 'desc')->get()->map(fn($c) => [
            'client_id'               => $c->client_id,
            'client_name'             => $c->client_name,
            'image'                   => $c->image,
            'contact_number'          => $c->contact_number,
            'email_id'                => $c->email_id,
            'city'                    => $c->city,
            'state'                   => $c->state,
            'status_id'               => $c->status_id,
            'subscription_type'       => $c->subscription_type ?? '-',
            'subscription_price'      => $c->subscription_price ? number_format($c->subscription_price, 2) : '-',
            'subscription_start_date' => $c->subscription_start_date?->format('d M Y') ?? '-',
            'subscription_end_date'   => $c->subscription_end_date?->format('d M Y') ?? '-',
            'subscription_status'     => $c->subscription_status,
            'plan_type'               => $c->plan_type ?? 'Basic',
            'created_at'              => $c->created_at,
        ]);
        return response()->json(['data' => $clients]);
    }

    public function create()
    {
        return view('clients.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name'                   => 'required|string|max:150',
            'image'                         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address'                       => 'required|string',
            'city'                          => 'nullable|string|max:100',
            'state'                         => 'nullable|string|max:100',
            'pincode'                       => 'nullable|string|max:10',
            'latitude'                      => 'nullable|numeric|between:-90,90',
            'longitude'                     => 'nullable|numeric|between:-180,180',
            'contact_number'                => 'required|string|max:15',
            'email_id'                      => 'required|email|max:150|unique:client_masters,email_id',
            'aadhar_image'                  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gst_number'                    => 'nullable|string|max:20',
            'upi_id'                        => 'nullable|string|max:100',
            'status_id'                     => 'nullable|integer|in:1,2,3,4',
            'subscription_type'             => 'nullable|in:Monthly,Quarterly,Yearly',
            'subscription_price'            => 'nullable|numeric|min:0',
            'subscription_start_date'       => 'nullable|date',
            'plan_type'                     => 'nullable|in:Basic,Premium',
            'password'                      => 'nullable|string|min:6|confirmed',
            'printers'                      => 'nullable|array',
            'printers.*.printer_id'         => 'required_with:printers|string|max:100',
            'printers.*.device_name'        => 'nullable|string|max:100',
            'printers.*.printer_type'       => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'client_name', 'address', 'city', 'state', 'pincode',
            'latitude', 'longitude', 'contact_number', 'email_id',
            'gst_number', 'upi_id', 'status_id',
            'subscription_type', 'subscription_price', 'subscription_start_date', 'plan_type',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $data['status_id'] = $data['status_id'] ?? 1;
        $data['subscription_end_date'] = $this->calcEndDate(
            $request->subscription_type,
            $request->subscription_start_date
        );

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('clients/profile', 'public');
        }

        if ($request->hasFile('aadhar_image')) {
            $data['aadhar_image'] = $request->file('aadhar_image')->store('clients/aadhar', 'public');
        }

        $client = ClientMaster::create($data);

        // Save printers
        if ($request->filled('printers')) {
            foreach ($request->printers as $p) {
                if (empty($p['printer_id'])) continue;
                Printer::create([
                    'client_id'    => $client->client_id,
                    'mac_address'  => $p['printer_id'],
                    'device_name'  => $p['device_name'] ?? null,
                    'printer_type' => $p['printer_type'] ?? null,
                    'status'       => 'offline',
                ]);
            }
        }

        return redirect()->route('clients.index')->with('success', 'Client registered successfully.');
    }

    public function edit($id)
    {
        $client   = ClientMaster::findOrFail($id);
        $printers = Printer::where('client_id', $id)->orderBy('id')->get();
        return view('clients.edit', ['client' => $client, 'id' => $id, 'printers' => $printers]);
    }

    public function update(Request $request, $id)
    {
        $client = ClientMaster::findOrFail($id);

        $request->validate([
            'client_name'                   => 'required|string|max:150',
            'image'                         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address'                       => 'required|string',
            'city'                          => 'nullable|string|max:100',
            'state'                         => 'nullable|string|max:100',
            'pincode'                       => 'nullable|string|max:10',
            'latitude'                      => 'nullable|numeric|between:-90,90',
            'longitude'                     => 'nullable|numeric|between:-180,180',
            'contact_number'                => 'required|string|max:15',
            'email_id'                      => ['required', 'email', 'max:150', Rule::unique('client_masters', 'email_id')->ignore($client->client_id, 'client_id')],
            'aadhar_image'                  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'gst_number'                    => 'nullable|string|max:20',
            'upi_id'                        => 'nullable|string|max:100',
            'status_id'                     => 'nullable|integer|in:1,2,3,4',
            'subscription_type'             => 'nullable|in:Monthly,Quarterly,Yearly',
            'subscription_price'            => 'nullable|numeric|min:0',
            'subscription_start_date'       => 'nullable|date',
            'plan_type'                     => 'nullable|in:Basic,Premium',
            'printers'                      => 'nullable|array',
            'printers.*.id'                 => 'nullable|integer',
            'printers.*.printer_id'         => 'required_with:printers|string|max:100',
            'printers.*.device_name'        => 'nullable|string|max:100',
            'printers.*.printer_type'       => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'client_name', 'address', 'city', 'state', 'pincode',
            'latitude', 'longitude', 'contact_number', 'email_id',
            'gst_number', 'upi_id', 'status_id',
            'subscription_type', 'subscription_price', 'subscription_start_date', 'plan_type',
        ]);
        $data['subscription_end_date'] = $this->calcEndDate(
            $request->subscription_type,
            $request->subscription_start_date
        );

        if ($request->hasFile('image')) {
            if ($client->image) {
                Storage::disk('public')->delete($client->image);
            }
            $data['image'] = $request->file('image')->store('clients/profile', 'public');
        }

        if ($request->hasFile('aadhar_image')) {
            if ($client->aadhar_image) {
                Storage::disk('public')->delete($client->aadhar_image);
            }
            $data['aadhar_image'] = $request->file('aadhar_image')->store('clients/aadhar', 'public');
        }

        $client->update($data);

        // Sync printers: keep submitted IDs, delete removed, add/update rest
        $submittedIds = [];
        foreach ($request->printers ?? [] as $p) {
            if (empty($p['printer_id'])) continue;

            if (!empty($p['id'])) {
                // Update existing
                Printer::where('id', $p['id'])->where('client_id', $client->client_id)->update([
                    'mac_address'  => $p['printer_id'],
                    'device_name'  => $p['device_name'] ?? null,
                    'printer_type' => $p['printer_type'] ?? null,
                ]);
                $submittedIds[] = $p['id'];
            } else {
                // Create new
                $newPrinter = Printer::create([
                    'client_id'    => $client->client_id,
                    'mac_address'  => $p['printer_id'],
                    'device_name'  => $p['device_name'] ?? null,
                    'printer_type' => $p['printer_type'] ?? null,
                    'status'       => 'offline',
                ]);
                $submittedIds[] = $newPrinter->id;
            }
        }

        // Delete printers that were removed from the form
        Printer::where('client_id', $client->client_id)
            ->when(!empty($submittedIds), fn($q) => $q->whereNotIn('id', $submittedIds))
            ->when(empty($submittedIds), fn($q) => $q)
            ->delete();

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        $client = ClientMaster::findOrFail($request->client_id);
        $client->status_id = $request->status;
        $client->save();

        return response()->json(['success' => true]);
    }

    private function calcEndDate(?string $type, ?string $startDate): ?string
    {
        if (!$type || !$startDate) return null;
        $start = Carbon::parse($startDate);
        return match ($type) {
            'Monthly'   => $start->addMonth()->toDateString(),
            'Quarterly' => $start->addMonths(3)->toDateString(),
            'Yearly'    => $start->addYear()->toDateString(),
            default     => null,
        };
    }

    public function destroy($id)
    {
        $client = ClientMaster::findOrFail($id);

        if ($client->image) {
            Storage::disk('public')->delete($client->image);
        }
        if ($client->aadhar_image) {
            Storage::disk('public')->delete($client->aadhar_image);
        }

        $client->delete();

        return response()->json(['success' => true]);
    }
}
