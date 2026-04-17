@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Client Master</h4>
                <h6>Manage Your Hotel Clients</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a id="custom-excel-export" data-bs-toggle="tooltip" data-bs-placement="top" title="Export to Excel">
                    <img src="{{ $base_url }}/assets/img/icons/excel.svg" alt="Excel Icon">
                </a>
            </li>
        </ul>
        @if($isAdmin || ($userPermissions->has('Client Registration') && $userPermissions->get('Client Registration')->can_add))
        <div class="page-btn">
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="ti ti-circle-plus me-1"></i>Add Client
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
                <table id="clients-table" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Client Name</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>City</th>
                            <th>Subscription</th>
                            <th>Price (₹)</th>
                            <th>Valid Till</th>
                            <th>Sub. Status</th>
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
    if ($.fn.DataTable.isDataTable('#clients-table')) {
        $('#clients-table').DataTable().destroy();
    }

    $('#clients-table').DataTable({
        "ajax": {
            "url": "{{ route('clients.allData') }}",
            "type": "GET"
        },
        "columns": [
            { "data": "client_id" },
            {
                "data": "image",
                "render": function(data) {
                    return data
                        ? `<img src="{{ $base_url }}/storage/app/public/${data}" class="avatar avatar-md rounded">`
                        : `<span class="avatar avatar-md bg-light d-flex align-items-center justify-content-center"><i class="ti ti-building-store fs-20 text-muted"></i></span>`;
                }
            },
            { "data": "client_name" },
            { "data": "contact_number" },
            { "data": "email_id" },
            {
                "data": null,
                "render": function(data, type, row) {
                    return row.city ? row.city + (row.state ? ', ' + row.state : '') : '-';
                }
            },
            {
                "data": "subscription_type",
                "render": function(data) {
                    if (!data || data === '-') return '<span class="text-muted">-</span>';
                    let map = { Monthly: 'bg-primary', Quarterly: 'bg-purple', Yearly: 'bg-success' };
                    return `<span class="badge ${map[data] || 'bg-secondary'}">${data}</span>`;
                }
            },
            { "data": "subscription_price" },
            { "data": "subscription_end_date" },
            {
                "data": "subscription_status",
                "render": function(data) {
                    if (data === 'None') return '<span class="text-muted">-</span>';
                    let cls = data === 'Active' ? 'bg-success' : 'bg-danger';
                    return `<span class="badge ${cls}">${data}</span>`;
                }
            },
            {
                "data": "status_id",
                "render": function(data, type, row) {
                    let map = { 1: ['bg-success','Active'], 2: ['bg-danger','Inactive'], 3: ['bg-warning','Suspended'], 4: ['bg-info','Trial'] };
                    let [cls, label] = map[data] || ['bg-secondary','Unknown'];
                    return `<span class="badge ${cls} toggle-status" style="cursor:pointer"
                                data-id="${row.client_id}" data-status="${data}">${label}</span>`;
                }
            },
            {
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    let html = '<div class="edit-delete-action d-flex gap-2">';
                    @if($isAdmin || ($userPermissions->has('Client Registration') && $userPermissions->get('Client Registration')->can_edit))
                    html += `<a class="border rounded d-flex align-items-center p-2" href="clients/${row.client_id}" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-edit">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>`;
                    @endif
                    @if($isAdmin || ($userPermissions->has('Client Registration') && $userPermissions->get('Client Registration')->can_delete))
                    html += `<a class="border rounded d-flex align-items-center p-2 text-danger delete-btn"
                                href="javascript:void(0);" data-id="${row.client_id}" title="Delete">
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
    let el = $(this);
    let clientId = el.data('id');
    let current  = parseInt(el.data('status'));
    let newStatus = current === 1 ? 2 : 1;

    $.ajax({
        url: "{{ route('clients.updateStatus') }}",
        type: "POST",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: { client_id: clientId, status: newStatus },
        success: function (res) {
            if (res.success) {
                let map = { 1: ['bg-success','Active'], 2: ['bg-danger','Inactive'], 3: ['bg-warning','Suspended'], 4: ['bg-info','Trial'] };
                let [cls, label] = map[newStatus];
                el.removeClass('bg-success bg-danger bg-warning bg-info').addClass(cls).text(label);
                el.data('status', newStatus);
            }
        },
        error: function () { alert('Error updating status.'); }
    });
});

// Delete client
$(document).on('click', '.delete-btn', function () {
    if (!confirm('Are you sure you want to delete this client?')) return;

    let clientId = $(this).data('id');
    let row = $(this).closest('tr');

    $.ajax({
        url: clientId + '/delete',
        type: "POST",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (res) {
            if (res.success) {
                $('#clients-table').DataTable().row(row).remove().draw();
            }
        },
        error: function () { alert('Error deleting client.'); }
    });
});

// Excel export
document.getElementById('custom-excel-export').addEventListener('click', function () {
    var workbook = XLSX.utils.table_to_book(document.getElementById('clients-table'), { sheet: "Clients" });
    XLSX.writeFile(workbook, "Clients-report.xlsx");
});
</script>
@endsection
