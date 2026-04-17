<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends BaseApiController
{
    /**
     * GET /api/v1/profile
     */
    public function show(Request $request)
    {
        $userType = $request->attributes->get('user_type');
        $user     = $request->attributes->get('mobile_user');

        if ($userType === 'client') {
            return $this->success([
                'user_type'  => 'client',
                'client_id'  => $user->client_id,
                'name'       => $user->client_name,
                'email'      => $user->email_id,
                'contact'    => $user->contact_number,
                'address'    => $user->address,
                'city'       => $user->city,
                'state'      => $user->state,
                'upi_id'     => $user->upi_id,
                'image'      => $user->image
                    ? config('app.url') . '/storage/' . $user->image
                    : null,
                'subscription' => [
                    'type'       => $user->subscription_type,
                    'status'     => $user->subscription_status,
                    'valid_till' => $user->subscription_end_date?->format('d M Y'),
                ],
            ]);
        }

        // Employee profile
        $user->load('role:id,role_name');
        return $this->success([
            'user_type'   => 'employee',
            'employee_id' => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'phone'       => $user->phone,
            'client_id'   => $user->client_id,
            'role_id'     => $user->client_role_id,
            'role_name'   => optional($user->role)->role_name,
            'image'       => $user->profile_image
                ? config('app.url') . '/storage/' . $user->profile_image
                : null,
        ]);
    }

    /**
     * PUT /api/v1/profile
     */
    public function update(Request $request)
    {
        $user = $request->attributes->get('mobile_user');

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:150',
            'phone'    => 'sometimes|nullable|string|max:15',
            'upi_id'   => 'sometimes|nullable|string|max:100',
            'password' => 'sometimes|string|min:6|confirmed',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $userType = $request->attributes->get('user_type');

        if ($request->filled('name')) {
            if ($userType === 'client') $user->client_name  = $request->name;
            else                        $user->name         = $request->name;
        }

        if ($request->filled('phone')) {
            if ($userType === 'client') $user->contact_number = $request->phone;
            else                        $user->phone          = $request->phone;
        }

        if ($request->filled('password')) $user->password = Hash::make($request->password);

        // upi_id is client-only field
        if ($request->has('upi_id') && $request->attributes->get('user_type') === 'client') {
            $user->upi_id = $request->upi_id;
        }

        $user->save();
        return $this->success(null, 'Profile updated');
    }
}
