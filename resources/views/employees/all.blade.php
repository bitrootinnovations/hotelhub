@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Employee Master</h4>
                <h6>Manage system employees</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a id="custom-excel-export" data-bs-toggle="tooltip" data-bs-placement="top" title="Export to Excel">
                    <img src="{{ $base_url }}/assets/img/icons/excel.svg" alt="Excel Icon">
                </a>
            </li>
            <li>
                <a id="collapse-header" data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse">
                    <i class="ti ti-chevron-up"></i>
                </a>
            </li>
        </ul>
        @if($isAdmin || ($userPermissions->has('Employees') && $userPermissions->get('Employees')->can_add))
        <div class="page-btn">
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="ti ti-circle-plus me-1"></i>Add Employee
            </a>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <div class="search-set">
                <div class="search-input">
                    <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="employees-table" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Profile</th>
                            <th>Employee Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="{{ $base_url }}/assets/js/jquery-3.7.1.min.js"></script>
<script src="{{ $base_url }}/assets/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#employees-table')) {
        $('#employees-table').DataTable().destroy();
    }

    var table = $('#employees-table').DataTable({
        ajax: {
            url: '{{ route('employees.allData') }}',
            type: 'GET'
        },
        columns: [
            { data: 'employee_id' },
            {
                data: 'profile_image',
                orderable: false,
                render: function (data) {
                    if (data) {
                        return `<img src="{{ $base_url }}/storage/app/public/${data}" class="avatar avatar-md rounded-circle" style="object-fit:cover;">`;
                    }
                    return `<span class="avatar avatar-md bg-light d-flex align-items-center justify-content-center rounded-circle"><i class="ti ti-user fs-20 text-muted"></i></span>`;
                }
            },
            { data: 'employee_name' },
            { data: 'email_id' },
            { data: 'contact_number' },
            { data: 'role_name' },
            { data: 'designation' },
            { data: 'department' },
            {
                data: 'status_id',
                render: function (data, type, row) {
                    let cls   = data == 1 ? 'bg-success' : 'bg-danger';
                    let label = data == 1 ? 'Active' : 'Inactive';
                    return `<span class="badge ${cls} toggle-status" style="cursor:pointer"
                                data-id="${row.employee_id}" data-status="${data}">${label}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    let html = '<div class="edit-delete-action d-flex gap-2">';
                    @if($isAdmin || ($userPermissions->has('Employees') && $userPermissions->get('Employees')->can_edit))
                    html += `<a class="border rounded d-flex align-items-center p-2" href="employees/${row.employee_id}" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-edit">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>`;
                    @endif
                    @if($isAdmin || ($userPermissions->has('Employees') && $userPermissions->get('Employees')->can_delete))
                    html += `<a class="border rounded d-flex align-items-center p-2 text-danger delete-btn"
                            href="javascript:void(0);" data-id="${row.employee_id}" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-trash-2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                <path d="M10 11v6"></path><path d="M14 11v6"></path>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                            </svg>
                        </a>`;
                    @endif
                    html += '</div>';
                    return html;
                }
            }
        ],
        bFilter: true,
        sDom: 'fBtlpi',
        ordering: true,
        language: {
            search: ' ',
            sLengthMenu: '_MENU_',
            searchPlaceholder: 'Search',
            info: '_START_ - _END_ of _TOTAL_ items',
            paginate: {
                next: '<i class="fa fa-angle-right"></i>',
                previous: '<i class="fa fa-angle-left"></i>'
            }
        },
        initComplete: function () {
            $('.dataTables_filter').appendTo('.search-input');
        }
    });

    // Toggle status
    $(document).on('click', '.toggle-status', function () {
        let el        = $(this);
        let empId     = el.data('id');
        let current   = parseInt(el.data('status'));
        let newStatus = current === 1 ? 0 : 1;

        $.ajax({
            url: '{{ route('employees.updateStatus') }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { id: empId, status: newStatus },
            success: function (res) {
                if (res.success) {
                    let cls   = newStatus === 1 ? 'bg-success' : 'bg-danger';
                    let label = newStatus === 1 ? 'Active' : 'Inactive';
                    el.removeClass('bg-success bg-danger').addClass(cls).text(label);
                    el.data('status', newStatus);
                }
            },
            error: function () { alert('Error updating status.'); }
        });
    });

    // Delete
    $(document).on('click', '.delete-btn', function () {
        if (!confirm('Are you sure you want to delete this employee?')) return;

        let empId = $(this).data('id');
        let row   = $(this).closest('tr');

        $.ajax({
            url: empId + '/delete',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.success) {
                    $('#employees-table').DataTable().row(row).remove().draw();
                }
            },
            error: function () { alert('Error deleting employee.'); }
        });
    });
});

// Excel export
document.getElementById('custom-excel-export').addEventListener('click', function () {
    var workbook = XLSX.utils.table_to_book(document.getElementById('employees-table'), { sheet: 'Employees' });
    XLSX.writeFile(workbook, 'Employees-report.xlsx');
});
</script>
@endsection
