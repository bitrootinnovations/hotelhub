@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Add Employee</h4>
                <h6>Fill in employee details below</h6>
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
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                <i data-feather="arrow-left" class="me-2"></i>Back to Employees
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="accordion" id="employeeAccordion">

            {{-- Basic Info --}}
            <div class="accordion-item card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic">
                        <i class="ti ti-user me-2 text-primary"></i>Basic Information
                    </button>
                </h2>
                <div id="collapseBasic" class="accordion-collapse collapse show" data-bs-parent="#employeeAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee Name <span class="text-danger">*</span></label>
                                <input type="text" name="employee_name" class="form-control" value="{{ old('employee_name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email ID <span class="text-danger">*</span></label>
                                <input type="email" name="email_id" class="form-control" value="{{ old('email_id') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" maxlength="15" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select" required>
                                    <option value="">-- Select Role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
                                            {{ $role->role_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control" value="{{ old('designation') }}" maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control" value="{{ old('department') }}" maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status_id" class="form-select">
                                    <option value="1" {{ old('status_id', 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status_id') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Login Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter password" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="accordion-item card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAddress">
                        <i class="ti ti-map-pin me-2 text-success"></i>Address
                    </button>
                </h2>
                <div id="collapseAddress" class="accordion-collapse collapse" data-bs-parent="#employeeAccordion">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Profile Image --}}
            <div class="accordion-item card mb-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseImage">
                        <i class="ti ti-photo me-2 text-warning"></i>Profile Image
                    </button>
                </h2>
                <div id="collapseImage" class="accordion-collapse collapse" data-bs-parent="#employeeAccordion">
                    <div class="accordion-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label">Upload Photo</label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*"
                                    onchange="previewImage(this, 'profilePreview')">
                            </div>
                            <div class="col-md-6">
                                <img id="profilePreview" src="#" alt="Preview"
                                    class="img-thumbnail d-none" style="max-height:150px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-12 mt-2">
            <div class="d-flex align-items-center justify-content-end mb-4">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i>Save Employee
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
