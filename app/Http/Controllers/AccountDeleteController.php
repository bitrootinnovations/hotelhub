<?php

namespace App\Http\Controllers;

use App\Models\AccountDeleteRequest;
use Illuminate\Http\Request;

class AccountDeleteController extends Controller
{
    public function show()
    {
        return view('account-delete');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'full_name'  => 'required|string|max:150',
            'email'      => 'required|email|max:150',
            'phone'      => 'nullable|string|max:20',
            'reason'     => 'required|in:no_longer_using,privacy_concerns,switching_service,data_concerns,other',
            'notes'      => 'nullable|string|max:1000',
            'confirm'    => 'accepted',
        ], [
            'confirm.accepted' => 'You must confirm that you understand account deletion is permanent.',
        ]);

        AccountDeleteRequest::create($request->only('full_name', 'email', 'phone', 'reason', 'notes'));

        return redirect()->route('account-delete')
            ->with('success', true)
            ->with('submitted_email', $request->email);
    }
}
