@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>UPI Settings</h4>
                <h6>Global UPI details and per-client plan amounts</h6>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Global UPI Settings --}}
    <form method="POST" action="{{ route('settings.upi.update') }}">
        @csrf
        <div class="accordion-item border mb-4">
            <h2 class="accordion-header">
                <div class="accordion-button bg-white">
                    <h5 class="d-flex align-items-center">
                        <i data-feather="credit-card" class="text-primary me-2"></i>Global UPI Details
                    </h5>
                </div>
            </h2>
            <div class="accordion-body border-top">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">UPI ID <span class="text-danger">*</span></label>
                            <input type="text" name="upi_id" class="form-control"
                                   value="{{ old('upi_id', $settings['upi_id']) }}"
                                   placeholder="e.g. business@upi" required>
                            <div class="form-text">Same UPI ID used across all clients.</div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Business Name</label>
                            <input type="text" name="business_name" class="form-control"
                                   value="{{ old('business_name', $settings['business_name']) }}"
                                   placeholder="HotelHub">
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Plan Name</label>
                            <input type="text" name="plan_name" class="form-control"
                                   value="{{ old('plan_name', $settings['plan_name']) }}"
                                   placeholder="Basic Plan">
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Payment Note</label>
                            <input type="text" name="note" class="form-control"
                                   value="{{ old('note', $settings['note']) }}"
                                   placeholder="Monthly subscription">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy me-1"></i>Save UPI Settings
            </button>
        </div>
    </form>

    {{-- Per-Client Plan Amounts + Print QR --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 d-flex align-items-center">
                <i data-feather="users" class="text-success me-2"></i>Client-wise Plan Amount & QR Print
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Client Name</th>
                            <th>Plan Amount (₹)</th>
                            <th>UPI QR String</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $i => $client)
                        @php
                            $amount    = $client->subscription_price ?? 0;
                            $upiId     = $settings['upi_id'];
                            $bizName   = $settings['business_name'];
                            $note      = $settings['note'];
                            $upiString = $upiId
                                ? 'upi://pay?pa=' . $upiId . '&pn=' . urlencode($bizName) . '&am=' . $amount . '&cu=INR&tn=' . urlencode($note)
                                : '';
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>{{ $client->client_name }}</strong></td>
                            <td style="width:180px;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control amount-input"
                                           id="amount_{{ $client->client_id }}"
                                           value="{{ $amount }}" min="0" step="0.01"
                                           data-client="{{ $client->client_id }}">
                                    <button class="btn btn-outline-primary save-amount-btn"
                                            data-client="{{ $client->client_id }}"
                                            title="Save Amount">
                                        <i class="ti ti-check"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                @if($upiId)
                                    <code class="small text-truncate d-inline-block" style="max-width:280px;" title="{{ $upiString }}">{{ $upiString }}</code>
                                @else
                                    <span class="text-muted small">Set UPI ID first</span>
                                @endif
                            </td>
                            <td>
                                @if($upiId)
                                    <button class="btn btn-sm btn-success print-qr-btn"
                                            data-client="{{ $client->client_id }}"
                                            data-name="{{ $client->client_name }}">
                                        <i class="ti ti-printer me-1"></i>Send to Mobile
                                    </button>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No clients found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ── Save per-client amount ────────────────────────────────────────────────────
document.querySelectorAll('.save-amount-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const clientId = this.dataset.client;
        const amount   = document.getElementById('amount_' + clientId).value;
        const self     = this;
        fetch('{{ route('settings.upi.client-amount') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ client_id: clientId, amount: amount })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                self.classList.replace('btn-outline-primary', 'btn-success');
                setTimeout(() => self.classList.replace('btn-success', 'btn-outline-primary'), 1500);
            }
        });
    });
});

// ── Send print job to client's mobile app ────────────────────────────────────
document.querySelectorAll('.print-qr-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const clientId   = this.dataset.client;
        const clientName = this.dataset.name;
        const self       = this;

        self.disabled = true;
        self.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';

        fetch('{{ url('api/v1/admin/print/upi-qr') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ client_id: clientId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                self.innerHTML = '<i class="ti ti-check me-1"></i>Sent!';
                self.classList.replace('btn-success', 'btn-info');
                setTimeout(() => {
                    self.innerHTML = '<i class="ti ti-printer me-1"></i>Send to Mobile';
                    self.classList.replace('btn-info', 'btn-success');
                    self.disabled = false;
                }, 3000);
            } else {
                alert(data.message || 'Failed to create print job.');
                self.innerHTML = '<i class="ti ti-printer me-1"></i>Send to Mobile';
                self.disabled = false;
            }
        })
        .catch(() => {
            alert('Network error. Please try again.');
            self.innerHTML = '<i class="ti ti-printer me-1"></i>Send to Mobile';
            self.disabled = false;
        });
    });
});
</script>
@endsection
