<?php

namespace App\Http\Controllers\Api;

use App\Models\ClientMaster;
use App\Models\ClientEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends BaseApiController
{
    /**
     * POST /api/v1/auth/login
     *
     * Unified login for:
     *   login_type = "client"   → authenticates ClientMaster (email_id + password)
     *   login_type = "employee" → authenticates ClientEmployee (email + password)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'      => 'required|email',
            'password'   => 'required|string|min:6',
            'login_type' => 'required|in:client,employee',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        if ($request->login_type === 'client') {
            return $this->loginClient($request);
        }

        return $this->loginEmployee($request);
    }

    // ── Client Login ───────────────────────────────────────────────────────────
    private function loginClient(Request $request)
    {
        $client = ClientMaster::where('email_id', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            return $this->error('Invalid email or password', 401);
        }

        if ($client->status_id != 1) {
            return $this->error('Your account is inactive or suspended. Contact admin.', 403);
        }

        if (!$client->password) {
            return $this->error('Mobile access not set up. Contact your admin to set a password.', 403);
        }

        try {
            $token = JWTAuth::fromUser($client);
        } catch (JWTException $e) {
            return $this->error('Could not create token', 500);
        }

        return $this->success([
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user_type'  => 'client',
            'user'       => [
                'client_id'   => $client->client_id,
                'name'        => $client->client_name,
                'email'       => $client->email_id,
                'contact'     => $client->contact_number,
                'city'        => $client->city,
                'image'       => $client->image
                    ? config('app.url') . '/storage/' . $client->image
                    : null,
                'subscription' => [
                    'type'       => $client->subscription_type,
                    'status'     => $client->subscription_status,
                    'valid_till' => $client->subscription_end_date?->format('d M Y'),
                ],
            ],
        ], 'Login successful');
    }

    // ── Employee Login ─────────────────────────────────────────────────────────
    private function loginEmployee(Request $request)
    {
        $employee = ClientEmployee::where('email', $request->email)->first();

        if (!$employee || !Hash::check($request->password, $employee->password)) {
            return $this->error('Invalid email or password', 401);
        }

        if ($employee->status_id != 1) {
            return $this->error('Your account is inactive. Contact your manager.', 403);
        }

        try {
            $token = JWTAuth::fromUser($employee);
        } catch (JWTException $e) {
            return $this->error('Could not create token', 500);
        }

        return $this->success([
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user_type'  => 'employee',
            'user'       => [
                'employee_id' => $employee->id,
                'name'        => $employee->name,
                'email'       => $employee->email,
                'phone'       => $employee->phone,
                'client_id'   => $employee->client_id,
                'role_id'     => $employee->client_role_id,
                'role_name'   => optional($employee->role)->role_name,
                'image'       => $employee->profile_image
                    ? config('app.url') . '/storage/' . $employee->profile_image
                    : null,
            ],
        ], 'Login successful');
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->success(null, 'Logged out successfully');
        } catch (JWTException $e) {
            return $this->error('Failed to logout', 500);
        }
    }

    /**
     * POST /api/v1/auth/refresh
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            return $this->success([
                'token'      => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 'Token refreshed');
        } catch (JWTException $e) {
            return $this->error('Token cannot be refreshed, please login again', 401);
        }
    }
}
