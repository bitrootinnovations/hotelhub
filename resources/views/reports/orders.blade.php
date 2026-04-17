@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Order Report</h4>
                <h6>View and export order data across all clients</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a id="custom-excel-export" data-bs-toggle="tooltip" data-bs-placement="top" title="Export to Excel" style="cursor:pointer">
                    <img src="{{ $base_url }}/assets/img/icons/excel.svg" alt="Excel Icon">
                </a>
            </li>
        </ul>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">From Date</label>
                    <input type="date" id="from_date" class="form-control" value="{{ $fromDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">To Date</label>
                    <input type="date" id="to_date" class="form-control" value="{{ $toDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Client</label>
                    <select id="client_id" class="form-select">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->client_id }}" {{ $clientId == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending"   {{ $status === 'pending'   ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="served"    {{ $status === 'served'    ? 'selected' : '' }}>Served</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="btn-filter" class="btn btn-primary me-2">
                        <i class="ti ti-search me-1"></i>Apply Filter
                    </button>
                    <button id="btn-reset" class="btn btn-secondary">
                        <i class="ti ti-refresh me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-3" id="summary-cards">
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small">Total Orders</p>
                    <h4 class="fw-bold mb-0 text-primary" id="sum-total">-</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small">Total Revenue</p>
                    <h4 class="fw-bold mb-0 text-success" id="sum-revenue">-</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small">Paid Amount</p>
                    <h4 class="fw-bold mb-0 text-info" id="sum-paid">-</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-3">
                    <p class="text-muted mb-1 small">Cancelled Orders</p>
                    <h4 class="fw-bold mb-0 text-danger" id="sum-cancelled">-</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
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
                <table id="orders-table" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Order No.</th>
                            <th>Client</th>
                            <th>Table</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Subtotal (₹)</th>
                            <th>GST (₹)</th>
                            <th>Total (₹)</th>
                            <th>Payment</th>
                            <th>Pay Status</th>
                            <th>Checked Out</th>
                            <th>Ordered At</th>
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
let table;

const statusBadge = {
    pending:   'bg-warning text-dark',
    confirmed: 'bg-primary',
    served:    'bg-success',
    cancelled: 'bg-danger',
};
const payBadge = {
    pending:   'bg-warning text-dark',
    paid:      'bg-success',
    refunded:  'bg-secondary',
};

function loadReport() {
    const params = {
        from_date: $('#from_date').val(),
        to_date:   $('#to_date').val(),
        client_id: $('#client_id').val(),
        status:    $('#status').val(),
    };

    $.get('{{ route("reports.orders.data") }}', params, function (res) {
        // Summary
        $('#sum-total').text(res.summary.total_orders);
        $('#sum-revenue').text('₹' + res.summary.total_revenue);
        $('#sum-paid').text('₹' + res.summary.paid_amount);
        $('#sum-cancelled').text(res.summary.cancelled_orders);

        // Rebuild DataTable
        if (table) { table.destroy(); $('#orders-table tbody').empty(); }

        table = $('#orders-table').DataTable({
            data: res.data,
            columns: [
                { data: 'order_id' },
                { data: 'order_number', render: d => `<span class="fw-semibold">${d}</span>` },
                { data: 'client_name' },
                { data: 'table_name' },
                { data: 'order_type', render: d => d === 'dine_in' ? 'Dine In' : 'Takeaway' },
                { data: 'status', render: d => `<span class="badge ${statusBadge[d] || 'bg-secondary'}">${d.charAt(0).toUpperCase()+d.slice(1)}</span>` },
                { data: 'subtotal' },
                { data: 'gst_amount' },
                { data: 'total_amount', render: d => `<strong>₹${d}</strong>` },
                { data: 'payment_type' },
                { data: 'payment_status', render: d => `<span class="badge ${payBadge[d] || 'bg-secondary'}">${d.charAt(0).toUpperCase()+d.slice(1)}</span>` },
                { data: 'checked_out_at' },
                { data: 'created_at' },
            ],
            bFilter: true,
            sDom: 'fBtlpi',
            ordering: true,
            language: {
                search: ' ',
                sLengthMenu: '_MENU_',
                searchPlaceholder: 'Search...',
                info: '_START_ - _END_ of _TOTAL_ items',
                paginate: {
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>',
                },
                emptyTable: 'No orders found for the selected filters.',
            },
            initComplete: function () {
                $('.dataTables_filter').appendTo('.search-input');
            },
        });
    });
}

$(document).ready(function () {
    loadReport();

    $('#btn-filter').on('click', loadReport);

    $('#btn-reset').on('click', function () {
        $('#from_date').val('{{ now()->toDateString() }}');
        $('#to_date').val('{{ now()->toDateString() }}');
        $('#client_id').val('');
        $('#status').val('');
        loadReport();
    });
});

// Excel export
document.getElementById('custom-excel-export').addEventListener('click', function () {
    var workbook = XLSX.utils.table_to_book(document.getElementById('orders-table'), { sheet: 'Orders' });
    var from = document.getElementById('from_date').value;
    var to   = document.getElementById('to_date').value;
    XLSX.writeFile(workbook, `Order-Report-${from}-to-${to}.xlsx`);
});
</script>
@endsection
