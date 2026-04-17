@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Role Master</h4>
                <h6>Manage System Roles</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a id="custom-excel-export" data-bs-toggle="tooltip" data-bs-placement="top" title="Export to Excel">
                    <img src="{{ $base_url }}/assets/img/icons/excel.svg" alt="Excel Icon">
                </a>
            </li>
        </ul>
        <div class="page-btn">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="ti ti-circle-plus me-1"></i>Add Role
            </a>
        </div>
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
                <table id="roles-table" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
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
    if ($.fn.DataTable.isDataTable('#roles-table')) {
        $('#roles-table').DataTable().destroy();
    }

    $('#roles-table').DataTable({
        "ajax": {
            "url": "{{ route('roles.allData') }}",
            "type": "GET"
        },
        "columns": [
            { "data": "role_id" },
            {
                "data": "role_name",
                "render": function(data, type, row) {
                    return row.role_id == 1
                        ? `${data} <span class="badge bg-primary ms-1">Admin</span>`
                        : data;
                }
            },
            {
                "data": "description",
                "render": function(data) { return data ? data : '<span class="text-muted">-</span>'; }
            },
            {
                "data": "status_id",
                "render": function(data, type, row) {
                    if (row.role_id == 1) {
                        return `<span class="badge bg-success">Active</span>`;
                    }
                    let cls  = data == 1 ? 'bg-success' : 'bg-danger';
                    let text = data == 1 ? 'Active' : 'Inactive';
                    return `<span class="badge ${cls} toggle-status" style="cursor:pointer"
                                data-id="${row.role_id}" data-status="${data}">${text}</span>`;
                }
            },
            {
                "data": "created_at",
                "render": function(data) { return data ? new Date(data).toLocaleString() : '-'; }
            },
            {
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    let editBtn = `
                        <a class="border rounded d-flex align-items-center p-2" href="roles/${row.role_id}" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-edit">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>`;

                    let permBtn = `
                        <a class="border rounded d-flex align-items-center p-2 text-primary"
                            href="permissions/${row.role_id}" title="Assign Permissions">
                            <i class="ti ti-shield-check fs-16"></i>
                        </a>`;

                    let deleteBtn = row.role_id != 1 ? `
                        <a class="border rounded d-flex align-items-center p-2 text-danger delete-btn"
                            href="javascript:void(0);" data-id="${row.role_id}" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-trash-2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                <path d="M10 11v6"></path><path d="M14 11v6"></path>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                            </svg>
                        </a>` : '';

                    return `<div class="edit-delete-action d-flex gap-2">${editBtn}${permBtn}${deleteBtn}</div>`;
                }
            }
        ],
        "bFilter": true,
        "sDom": 'fBtlpi',
        "ordering": true,
        "language": {
            search: ' ',
            sLengthMenu: '_MENU_',
            searchPlaceholder: "Search",
            info: "_START_ - _END_ of _TOTAL_ items",
            paginate: {
                next: '<i class="fa fa-angle-right"></i>',
                previous: '<i class="fa fa-angle-left"></i>'
            }
        },
        "initComplete": function() {
            $('.dataTables_filter').appendTo('.search-input');
        }
    });
});

// Toggle status
$(document).on('click', '.toggle-status', function () {
    let el        = $(this);
    let roleId    = el.data('id');
    let current   = parseInt(el.data('status'));
    let newStatus = current === 1 ? 0 : 1;

    $.ajax({
        url: "{{ route('roles.updateStatus') }}",
        type: "POST",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { role_id: roleId, status: newStatus },
        success: function (res) {
            if (res.success) {
                let cls  = newStatus === 1 ? 'bg-success' : 'bg-danger';
                let text = newStatus === 1 ? 'Active' : 'Inactive';
                el.removeClass('bg-success bg-danger').addClass(cls).text(text);
                el.data('status', newStatus);
            } else {
                alert(res.message || 'Could not update status.');
            }
        },
        error: function () { alert('Error updating status.'); }
    });
});

// Delete role
$(document).on('click', '.delete-btn', function () {
    if (!confirm('Are you sure you want to delete this role? All associated permissions will also be removed.')) return;

    let roleId = $(this).data('id');
    let row    = $(this).closest('tr');

    $.ajax({
        url: 'roles/' + roleId + '/delete',
        type: "POST",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (res) {
            if (res.success) {
                $('#roles-table').DataTable().row(row).remove().draw();
            } else {
                alert(res.message || 'Could not delete role.');
            }
        },
        error: function () { alert('Error deleting role.'); }
    });
});

// Excel export
document.getElementById('custom-excel-export').addEventListener('click', function () {
    var workbook = XLSX.utils.table_to_book(document.getElementById('roles-table'), { sheet: "Roles" });
    XLSX.writeFile(workbook, "Roles-report.xlsx");
});
</script>
@endsection
