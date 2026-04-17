@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Category Master</h4>
                <h6>Manage food categories (Veg, Non-Veg, Starters, etc.)</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a id="custom-excel-export" data-bs-toggle="tooltip" data-bs-placement="top" title="Export to Excel">
                    <img src="{{ $base_url }}/assets/img/icons/excel.svg" alt="Excel">
                </a>
            </li>
        </ul>
        @if($isAdmin || ($userPermissions->has('Categories') && $userPermissions->get('Categories')->can_add))
        <div class="page-btn">
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="ti ti-circle-plus me-1"></i>Add Category
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
                <table id="categories-table" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                            <th>Client</th>
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
    if ($.fn.DataTable.isDataTable('#categories-table')) {
        $('#categories-table').DataTable().destroy();
    }
    var table = $('#categories-table').DataTable({
        ajax: { url: '{{ route('categories.allData') }}', type: 'GET' },
        columns: [
            { data: 'category_id' },
            { data: 'category_name' },
            { data: 'client_name' },
            {
                data: 'status_id',
                render: function (data, type, row) {
                    let cls = data == 1 ? 'bg-success' : 'bg-danger';
                    let lbl = data == 1 ? 'Active' : 'Inactive';
                    return `<span class="badge ${cls} toggle-status" style="cursor:pointer" data-id="${row.category_id}" data-status="${data}">${lbl}</span>`;
                }
            },
            { data: 'created_at', render: d => d ? new Date(d).toLocaleString() : '-' },
            {
                data: null, orderable: false,
                render: function (data, type, row) {
                    let html = '<div class="edit-delete-action d-flex gap-2">';
                    @if($isAdmin || ($userPermissions->has('Categories') && $userPermissions->get('Categories')->can_edit))
                    html += `<a class="border rounded d-flex align-items-center p-2" href="categories/${row.category_id}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>`;
                    @endif
                    @if($isAdmin || ($userPermissions->has('Categories') && $userPermissions->get('Categories')->can_delete))
                    html += `<a class="border rounded d-flex align-items-center p-2 text-danger delete-btn" href="javascript:void(0);" data-id="${row.category_id}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg></a>`;
                    @endif
                    html += '</div>';
                    return html;
                }
            }
        ],
        bFilter: true, sDom: 'fBtlpi', ordering: true,
        language: { search: ' ', sLengthMenu: '_MENU_', searchPlaceholder: 'Search', info: '_START_ - _END_ of _TOTAL_ items' },
        initComplete: function () { $('.dataTables_filter').appendTo('.search-input'); }
    });

    $(document).on('click', '.toggle-status', function () {
        let el = $(this), id = el.data('id'), cur = parseInt(el.data('status')), ns = cur === 1 ? 0 : 1;
        $.ajax({ url: '{{ route('categories.updateStatus') }}', type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { id, status: ns },
            success: function () {
                el.removeClass('bg-success bg-danger').addClass(ns ? 'bg-success' : 'bg-danger').text(ns ? 'Active' : 'Inactive').data('status', ns);
            }
        });
    });

    $(document).on('click', '.delete-btn', function () {
        if (!confirm('Delete this category?')) return;
        let id = $(this).data('id'), row = $(this).closest('tr');
        $.ajax({ url: 'categories/' + id + '/delete', type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function () { table.row(row).remove().draw(); }
        });
    });
});
document.getElementById('custom-excel-export').addEventListener('click', function () {
    XLSX.writeFile(XLSX.utils.table_to_book(document.getElementById('categories-table'), { sheet: 'Categories' }), 'Categories.xlsx');
});
</script>
@endsection
