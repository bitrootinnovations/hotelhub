@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Printer Status</h4>
                <h6>BLE thermal printers connected per outlet</h6>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Outlet (Client)</th>
                            <th>Device Name</th>
                            <th>MAC Address</th>
                            <th>Status</th>
                            <th>Last Seen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($printers as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->client?->client_name ?? '-' }}</td>
                            <td>{{ $p->device_name ?? '-' }}</td>
                            <td><code>{{ $p->mac_address }}</code></td>
                            <td>
                                @if($p->status === 'online')
                                    <span class="badge bg-success">Online</span>
                                @else
                                    <span class="badge bg-danger">Offline</span>
                                @endif
                            </td>
                            <td>{{ $p->last_seen_at ? $p->last_seen_at->format('d M Y, h:i A') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No printers registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
