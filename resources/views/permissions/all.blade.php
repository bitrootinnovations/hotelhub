@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Permission Master</h4>
                <h6>Assign permissions to each role</h6>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mb-3">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->role_id }}</td>
                            <td>
                                {{ $role->role_name }}
                                @if($role->role_id == 1)
                                    <span class="badge bg-primary ms-1">Admin</span>
                                @endif
                            </td>
                            <td>{{ $role->description ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $role->status_id == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $role->status_id == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @if($role->role_id == 1)
                                    <a href="{{ url('permissions/' . $role->role_id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-eye me-1"></i>View Permissions
                                    </a>
                                @else
                                    <a href="{{ url('permissions/' . $role->role_id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="ti ti-shield-check me-1"></i>Assign Permissions
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
