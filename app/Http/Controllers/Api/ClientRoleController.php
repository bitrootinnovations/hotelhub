<?php

namespace App\Http\Controllers\Api;

use App\Models\ClientRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientRoleController extends BaseApiController
{
    private function clientId(Request $request): int
    {
        return $request->attributes->get('client_id');
    }

    private function mustBeClient(Request $request)
    {
        if ($request->attributes->get('user_type') !== 'client') {
            return $this->error('Only the client account can manage roles', 403);
        }
        return null;
    }

    /**
     * GET /api/v1/client/roles
     */
    public function index(Request $request)
    {
        $roles = ClientRole::where('client_id', $this->clientId($request))
            ->where('status_id', 1)
            ->whereNull('deleted_at')
            ->select('id', 'role_name', 'status_id', 'created_at')
            ->orderBy('role_name')
            ->get();

        return $this->success($roles, 'Roles fetched');
    }

    /**
     * POST /api/v1/client/roles
     */
    public function store(Request $request)
    {
        if ($err = $this->mustBeClient($request)) return $err;

        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:100',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $clientId = $this->clientId($request);

        if (ClientRole::where('client_id', $clientId)
                      ->where('role_name', $request->role_name)
                      ->whereNull('deleted_at')
                      ->exists()) {
            return $this->error('A role with this name already exists', 422);
        }

        $role = ClientRole::create([
            'client_id' => $clientId,
            'role_name' => $request->role_name,
            'status_id' => 1,
        ]);

        return $this->success([
            'id'        => $role->id,
            'role_name' => $role->role_name,
        ], 'Role created', 201);
    }

    /**
     * PUT /api/v1/client/roles/{id}
     */
    public function update(Request $request, int $id)
    {
        if ($err = $this->mustBeClient($request)) return $err;

        $role = ClientRole::where('id', $id)
                          ->where('client_id', $this->clientId($request))
                          ->whereNull('deleted_at')
                          ->first();
        if (!$role) return $this->error('Role not found', 404);

        $validator = Validator::make($request->all(), [
            'role_name' => 'sometimes|string|max:100',
            'status_id' => 'sometimes|integer|in:0,1',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $role->update($request->only('role_name', 'status_id'));

        return $this->success(['id' => $role->id, 'role_name' => $role->role_name], 'Role updated');
    }

    /**
     * DELETE /api/v1/client/roles/{id}
     */
    public function destroy(Request $request, int $id)
    {
        if ($err = $this->mustBeClient($request)) return $err;

        $role = ClientRole::where('id', $id)
                          ->where('client_id', $this->clientId($request))
                          ->whereNull('deleted_at')
                          ->first();
        if (!$role) return $this->error('Role not found', 404);

        $role->delete();
        return $this->success(null, 'Role deleted');
    }
}
