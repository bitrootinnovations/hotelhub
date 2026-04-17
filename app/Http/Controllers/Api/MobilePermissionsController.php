<?php

namespace App\Http\Controllers\Api;

use App\Models\ClientRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MobilePermissionsController extends BaseApiController
{
    /**
     * GET /api/v1/permissions
     *
     * Returns the current user's permissions.
     * - Client owner → all permissions true
     * - Employee     → permissions from their assigned role
     */
    public function myPermissions(Request $request)
    {
        $userType = $request->attributes->get('user_type');

        if ($userType === 'client') {
            return $this->success([
                'user_type'           => 'client',
                'role_name'           => 'Owner',
                'can_take_orders'     => true,
                'can_checkout'        => true,
                'can_manage_menu'     => true,
                'can_manage_employees'=> true,
                'can_view_reports'    => true,
            ], 'Permissions fetched');
        }

        $employee = $request->attributes->get('mobile_user');
        $role = $employee->client_role_id
            ? ClientRole::find($employee->client_role_id)
            : null;

        return $this->success([
            'user_type'           => 'employee',
            'role_id'             => optional($role)->id,
            'role_name'           => optional($role)->role_name ?? 'No Role',
            'can_take_orders'     => (bool) optional($role)->can_take_orders,
            'can_checkout'        => (bool) optional($role)->can_checkout,
            'can_manage_menu'     => (bool) optional($role)->can_manage_menu,
            'can_manage_employees'=> (bool) optional($role)->can_manage_employees,
            'can_view_reports'    => (bool) optional($role)->can_view_reports,
        ], 'Permissions fetched');
    }

    /**
     * GET /api/v1/client/roles/{id}/permissions
     * Client only — view permissions for a role
     */
    public function show(Request $request, int $id)
    {
        $role = ClientRole::where('id', $id)
                          ->where('client_id', $request->attributes->get('client_id'))
                          ->whereNull('deleted_at')
                          ->first();
        if (!$role) return $this->error('Role not found', 404);

        return $this->success($this->formatRole($role));
    }

    /**
     * PUT /api/v1/client/roles/{id}/permissions
     * Client only — update permissions for a role
     */
    public function update(Request $request, int $id)
    {
        if ($request->attributes->get('user_type') !== 'client') {
            return $this->error('Only the client owner can update role permissions', 403);
        }

        $role = ClientRole::where('id', $id)
                          ->where('client_id', $request->attributes->get('client_id'))
                          ->whereNull('deleted_at')
                          ->first();
        if (!$role) return $this->error('Role not found', 404);

        $validator = Validator::make($request->all(), [
            'can_take_orders'     => 'boolean',
            'can_checkout'        => 'boolean',
            'can_manage_menu'     => 'boolean',
            'can_manage_employees'=> 'boolean',
            'can_view_reports'    => 'boolean',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $role->update($request->only([
            'can_take_orders', 'can_checkout',
            'can_manage_menu', 'can_manage_employees', 'can_view_reports',
        ]));

        return $this->success($this->formatRole($role), 'Permissions updated');
    }

    private function formatRole(ClientRole $role): array
    {
        return [
            'id'                  => $role->id,
            'role_name'           => $role->role_name,
            'can_take_orders'     => (bool) $role->can_take_orders,
            'can_checkout'        => (bool) $role->can_checkout,
            'can_manage_menu'     => (bool) $role->can_manage_menu,
            'can_manage_employees'=> (bool) $role->can_manage_employees,
            'can_view_reports'    => (bool) $role->can_view_reports,
        ];
    }
}
