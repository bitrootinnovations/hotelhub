<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\ClientMaster;
use App\Models\ClientEmployee;

/**
 * Resolves either a ClientMaster or ClientEmployee from the JWT token.
 * Sets $request->mobile_user  → the authenticated model
 *      $request->client_id    → always the client's ID
 *      $request->user_type    → 'client' | 'employee'
 */
class MobileAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token   = JWTAuth::parseToken();
            $payload = $token->getPayload();
        } catch (TokenExpiredException $e) {
            return $this->fail('Token has expired', 401);
        } catch (TokenInvalidException $e) {
            return $this->fail('Token is invalid', 401);
        } catch (JWTException $e) {
            return $this->fail('Token not provided', 401);
        }

        $userType = $payload->get('user_type');
        $clientId = $payload->get('client_id');

        if ($userType === 'client') {
            $user = ClientMaster::where('client_id', $clientId)
                                ->where('status_id', 1)
                                ->first();
            if (!$user) return $this->fail('Client account not found or inactive', 401);

        } elseif ($userType === 'employee') {
            $employeeId = $payload->get('employee_id');
            $user = ClientEmployee::where('id', $employeeId)
                                  ->where('client_id', $clientId)
                                  ->where('status_id', 1)
                                  ->first();
            if (!$user) return $this->fail('Employee account not found or inactive', 401);

        } else {
            return $this->fail('Invalid token type', 401);
        }

        // Attach resolved user to request
        $request->merge(['_mobile_user' => $user]);
        $request->attributes->set('mobile_user', $user);
        $request->attributes->set('client_id',   $clientId);
        $request->attributes->set('user_type',   $userType);

        return $next($request);
    }

    private function fail(string $message, int $code = 401)
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }
}
