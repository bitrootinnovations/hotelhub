<?php

namespace App\Http\Controllers\Api;

use App\Models\ClientEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientEmployeeController extends BaseApiController
{
    private function clientId(Request $request): int
    {
        return $request->attributes->get('client_id');
    }

    private function mustBeClient(Request $request)
    {
        if ($request->attributes->get('user_type') !== 'client') {
            return $this->error('Only the client account can manage employees', 403);
        }
        return null;
    }

    private function formatEmployee(ClientEmployee $e): array
    {
        return [
            'id'           => $e->id,
            'name'         => $e->name,
            'email'        => $e->email,
            'phone'        => $e->phone,
            'role_id'      => $e->client_role_id,
            'role_name'    => optional($e->role)->role_name,
            'status_id'    => $e->status_id,
            'profile_image'=> $e->profile_image
                ? config('app.url') . '/storage/' . $e->profile_image
                : null,
            'created_at'   => $e->created_at->format('d M Y'),
        ];
    }

    /**
     * GET /api/v1/client/employees
     */
    public function index(Request $request)
    {
        $employees = ClientEmployee::with('role:id,role_name')
            ->where('client_id', $this->clientId($request))
            ->whereNull('deleted_at')
            ->select('id', 'name', 'email', 'phone', 'client_role_id', 'status_id', 'profile_image', 'created_at')
            ->orderBy('name')
            ->get()
            ->map(fn($e) => $this->formatEmployee($e));

        return $this->success($employees, 'Employees fetched');
    }

    /**
     * POST /api/v1/client/employees
     * Only client account can register employees
     */
    public function store(Request $request)
    {
        if ($err = $this->mustBeClient($request)) return $err;

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:150',
            'email'          => 'required|email|unique:client_employees,email',
            'password'       => 'required|string|min:6|confirmed',
            'phone'          => 'nullable|string|max:15',
            'client_role_id' => 'nullable|integer|exists:client_roles,id',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $employee = ClientEmployee::create([
            'client_id'      => $this->clientId($request),
            'client_role_id' => $request->client_role_id,
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'phone'          => $request->phone,
            'status_id'      => 1,
        ]);

        $employee->load('role:id,role_name');

        return $this->success($this->formatEmployee($employee), 'Employee registered', 201);
    }

    /**
     * GET /api/v1/client/employees/{id}
     */
    public function show(Request $request, int $id)
    {
        $employee = ClientEmployee::with('role:id,role_name')
            ->where('id', $id)
            ->where('client_id', $this->clientId($request))
            ->whereNull('deleted_at')
            ->first();

        if (!$employee) return $this->error('Employee not found', 404);

        return $this->success($this->formatEmployee($employee));
    }

    /**
     * PUT /api/v1/client/employees/{id}
     * Only client can update employee details
     */
    public function update(Request $request, int $id)
    {
        if ($err = $this->mustBeClient($request)) return $err;

        $employee = ClientEmployee::where('id', $id)
            ->where('client_id', $this->clientId($request))
            ->whereNull('deleted_at')
            ->first();
        if (!$employee) return $this->error('Employee not found', 404);

        $validator = Validator::make($request->all(), [
            'name'           => 'sometimes|string|max:150',
            'phone'          => 'sometimes|nullable|string|max:15',
            'client_role_id' => 'sometimes|nullable|integer|exists:client_roles,id',
            'status_id'      => 'sometimes|integer|in:0,1',
            'password'       => 'sometimes|string|min:6|confirmed',
        ]);
        if ($validator->fails()) return $this->error('Validation failed', 422, $validator->errors());

        $data = $request->only('name', 'phone', 'client_role_id', 'status_id');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $employee->update($data);
        $employee->load('role:id,role_name');

        return $this->success($this->formatEmployee($employee), 'Employee updated');
    }

    /**
     * DELETE /api/v1/client/employees/{id}
     */
    public function destroy(Request $request, int $id)
    {
        if ($err = $this->mustBeClient($request)) return $err;

        $employee = ClientEmployee::where('id', $id)
            ->where('client_id', $this->clientId($request))
            ->whereNull('deleted_at')
            ->first();
        if (!$employee) return $this->error('Employee not found', 404);

        $employee->delete();
        return $this->success(null, 'Employee removed');
    }
}
