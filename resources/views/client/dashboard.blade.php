@extends('layouts.app')
@section('content')
<div class="content">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-title">
            <h4>Client Dashboard</h4>
            <h6>Welcome, {{ $client->client_name }}</h6>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-primary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-primary">
                        <i class="ti ti-shopping-cart fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Today's Orders</p>
                        <h4 class="text-white">{{ $todayOrders }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-teal sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-teal">
                        <i class="ti ti-currency-rupee fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Today's Revenue</p>
                        <h4 class="text-white">&#8377;{{ number_format($todayRevenue, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-info sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-info">
                        <i class="ti ti-flame fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Active KOTs</p>
                        <h4 class="text-white">{{ $activeKots }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-secondary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-secondary">
                        <i class="ti ti-books fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Menu Items</p>
                        <h4 class="text-white">{{ $totalMenuItems }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Status --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Table Status</h5>
            <div class="d-flex gap-2">
                <span class="badge bg-success">Available</span>
                <span class="badge bg-danger">Occupied</span>
                <span class="badge bg-secondary">Inactive</span>
            </div>
        </div>
        <div class="card-body">
            @if($tables->isEmpty())
                <p class="text-muted text-center mb-0">No tables found.</p>
            @else
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-3">
                @foreach($tables as $table)
                    @php
                        if ($table->status_id == 0) {
                            $borderClass = 'border-secondary';
                            $badgeClass  = 'bg-secondary';
                            $statusLabel = 'Inactive';
                        } elseif ($occupiedTableIds->contains($table->table_id)) {
                            $borderClass = 'border-danger';
                            $badgeClass  = 'bg-danger';
                            $statusLabel = 'Occupied';
                        } else {
                            $borderClass = 'border-success';
                            $badgeClass  = 'bg-success';
                            $statusLabel = 'Available';
                        }
                    @endphp
                <div class="col">
                    <div class="card text-center h-100 border-2 {{ $borderClass }}">
                        <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                            <div class="fw-bold fs-18">{{ $table->table_name }}</div>
                            <small class="text-muted">{{ $table->tableType->type_name ?? '-' }}</small>
                            <small>Cap: {{ $table->capacity ?? '?' }}</small>
                            <span class="badge mt-1 {{ $badgeClass }}">{{ $statusLabel }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Orders</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Order #</th>
                            <th>Table</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        @php
                            $statusBadge = match($order->status) {
                                'pending'   => 'bg-warning text-dark',
                                'confirmed' => 'bg-primary',
                                'preparing' => 'bg-info',
                                'served'    => 'bg-success',
                                'cancelled' => 'bg-danger',
                                default     => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td>{{ $order->order_number ?? '#'.$order->order_id }}</td>
                            <td>{{ $order->table_name ?? '-' }}</td>
                            <td>{{ ucfirst($order->order_type ?? '-') }}</td>
                            <td>&#8377;{{ number_format($order->total_amount, 2) }}</td>
                            <td><span class="badge {{ $statusBadge }}">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M, h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No recent orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
