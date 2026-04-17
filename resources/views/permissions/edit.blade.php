@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">
                    {{ $isAdmin ? 'View Permissions' : 'Assign Permissions' }}
                    — {{ $role->role_name }}
                </h4>
                <h6>{{ $isAdmin ? 'Admin has full access to all modules' : 'Set module-level access for this role' }}</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header">
                    <i class="ti ti-chevron-up"></i>
                </a>
            </li>
        </ul>
        <div class="page-btn mt-0">
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                <i data-feather="arrow-left" class="me-2"></i>Back to Permissions
            </a>
        </div>
    </div>

    @if($isAdmin)
        <div class="alert alert-primary mb-4 d-flex align-items-center gap-2">
            <i class="ti ti-shield-check fs-20"></i>
            <div>
                <strong>Admin Role</strong> — Full access to all modules is always granted.
                These permissions cannot be modified.
            </div>
        </div>
    @endif

    <form method="POST" action="{{ url('permissions/' . $role->role_id) }}">
        @csrf
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="ti ti-table me-2"></i>Module Permissions
                </h5>
                @if(!$isAdmin)
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success" id="selectAll">
                        <i class="ti ti-check me-1"></i>Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="clearAll">
                        <i class="ti ti-x me-1"></i>Clear All
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:35%">Module / Menu</th>
                                <th class="text-center" style="width:16%">
                                    <i class="ti ti-eye me-1 text-info"></i>View
                                </th>
                                <th class="text-center" style="width:16%">
                                    <i class="ti ti-plus me-1 text-success"></i>Add
                                </th>
                                <th class="text-center" style="width:16%">
                                    <i class="ti ti-edit me-1 text-warning"></i>Edit
                                </th>
                                <th class="text-center" style="width:16%">
                                    <i class="ti ti-trash me-1 text-danger"></i>Delete
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module)
                                @php
                                    $perm = $existing->get($module);
                                    $view   = $isAdmin ? 1 : ($perm ? $perm->can_view   : 0);
                                    $add    = $isAdmin ? 1 : ($perm ? $perm->can_add    : 0);
                                    $edit   = $isAdmin ? 1 : ($perm ? $perm->can_edit   : 0);
                                    $delete = $isAdmin ? 1 : ($perm ? $perm->can_delete : 0);
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $module }}</td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="permissions[{{ $module }}][can_view]"
                                            class="form-check-input perm-checkbox"
                                            value="1"
                                            {{ $view   ? 'checked' : '' }}
                                            {{ $isAdmin ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="permissions[{{ $module }}][can_add]"
                                            class="form-check-input perm-checkbox"
                                            value="1"
                                            {{ $add    ? 'checked' : '' }}
                                            {{ $isAdmin ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="permissions[{{ $module }}][can_edit]"
                                            class="form-check-input perm-checkbox"
                                            value="1"
                                            {{ $edit   ? 'checked' : '' }}
                                            {{ $isAdmin ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="permissions[{{ $module }}][can_delete]"
                                            class="form-check-input perm-checkbox"
                                            value="1"
                                            {{ $delete ? 'checked' : '' }}
                                            {{ $isAdmin ? 'disabled' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(!$isAdmin)
        <div class="col-lg-12 mt-3">
            <div class="d-flex align-items-center justify-content-end mb-4">
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>Save Permissions
                </button>
            </div>
        </div>
        @endif
    </form>
</div>

<script>
document.getElementById('selectAll') && document.getElementById('selectAll').addEventListener('click', function () {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
});

document.getElementById('clearAll') && document.getElementById('clearAll').addEventListener('click', function () {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
});
</script>
@endsection
