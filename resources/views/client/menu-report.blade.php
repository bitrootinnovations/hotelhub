@extends('layouts.app')
@section('content')
<div class="content">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold">Menu-wise Order Report</h4>
            <h6>Analyse sales performance by menu item</h6>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Filters</h5></div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" id="from_date" class="form-control" value="{{ $today }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" id="to_date" class="form-control" value="{{ $today }}">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100" id="btn-filter">
                        <i class="ti ti-filter me-1"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4" id="summary-cards">
        <div class="col-md-4 d-flex">
            <div class="card bg-primary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-primary">
                        <i class="ti ti-list fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Unique Menu Items</p>
                        <h4 class="text-white" id="stat-unique-menus">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex">
            <div class="card bg-teal sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-teal">
                        <i class="ti ti-shopping-cart fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Orders</p>
                        <h4 class="text-white" id="stat-total-orders">0</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex">
            <div class="card bg-info sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-info">
                        <i class="ti ti-currency-rupee fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Revenue</p>
                        <h4 class="text-white" id="stat-total-revenue">&#8377;0.00</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Menu-wise Breakdown</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="menu-report-table" class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Menu Name</th>
                            <th>Category</th>
                            <th>Veg / Non-Veg</th>
                            <th>Orders</th>
                            <th>Qty</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody id="menu-report-body">
                        <tr><td colspan="7" class="text-center text-muted py-3">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top 10 Selling Items --}}
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Top 10 Selling Items</h5></div>
        <div class="card-body">
            <ol class="mb-0" id="top-selling-list">
                <li class="text-muted">Loading...</li>
            </ol>
        </div>
    </div>

</div>

<script src="{{ $base_url }}/assets/js/jquery-3.7.1.min.js"></script>
<script src="{{ $base_url }}/assets/js/dataTables.bootstrap5.min.js"></script>

<script>
var menuTable = null;

function formatCurrency(val) {
    return '\u20B9' + parseFloat(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function loadData() {
    var fromDate = $('#from_date').val();
    var toDate   = $('#to_date').val();

    // Load main report data
    fetch('/client/menu-report/data?from_date=' + fromDate + '&to_date=' + toDate)
        .then(function(res) { return res.json(); })
        .then(function(json) {
            var rows    = json.data || [];
            var summary = json.summary || {};

            // Update summary cards
            $('#stat-unique-menus').text(rows.length);
            $('#stat-total-orders').text(summary.total_orders || 0);
            $('#stat-total-revenue').html(formatCurrency(summary.total_revenue));

            // Rebuild table body
            var tbody = '';
            if (rows.length === 0) {
                tbody = '<tr><td colspan="7" class="text-center text-muted py-3">No data found for selected dates.</td></tr>';
            } else {
                rows.forEach(function(row, idx) {
                    var vegBadge = row.food_type == 1
                        ? '<span class="badge bg-success">Veg</span>'
                        : '<span class="badge bg-danger">Non-Veg</span>';
                    tbody += '<tr>'
                        + '<td>' + (idx + 1) + '</td>'
                        + '<td>' + row.menu_name + '</td>'
                        + '<td>' + (row.category_name || '-') + '</td>'
                        + '<td>' + vegBadge + '</td>'
                        + '<td>' + row.total_orders + '</td>'
                        + '<td>' + row.total_qty + '</td>'
                        + '<td>' + formatCurrency(row.total_revenue) + '</td>'
                        + '</tr>';
                });
            }
            $('#menu-report-body').html(tbody);

            // Re-init DataTable
            if (menuTable) {
                menuTable.destroy();
                menuTable = null;
            }
            if (rows.length > 0) {
                menuTable = $('#menu-report-table').DataTable({
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
                    }
                });
            }
        })
        .catch(function(err) {
            console.error('Error loading report data:', err);
        });

    // Load top selling
    fetch('/client/menu-report/top-selling?from_date=' + fromDate + '&to_date=' + toDate)
        .then(function(res) { return res.json(); })
        .then(function(json) {
            var items = json.data || [];
            var html  = '';
            if (items.length === 0) {
                html = '<li class="text-muted">No data found.</li>';
            } else {
                items.forEach(function(item) {
                    html += '<li class="mb-2">'
                        + '<strong>' + item.menu_name + '</strong>'
                        + ' &mdash; Qty: <span class="text-primary fw-semibold">' + item.total_qty + '</span>'
                        + ' | Revenue: <span class="text-success fw-semibold">' + formatCurrency(item.total_revenue) + '</span>'
                        + '</li>';
                });
            }
            $('#top-selling-list').html(html);
        })
        .catch(function(err) {
            console.error('Error loading top-selling data:', err);
        });
}

$(document).ready(function () {
    loadData();

    $('#btn-filter').on('click', function () {
        loadData();
    });
});
</script>
@endsection
