@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Table</h4>
                <h6>Update table details</h6>
            </div>
        </div>
        <div class="page-btn mt-0">
            <a href="{{ route('tables.index') }}" class="btn btn-secondary">
                <i data-feather="arrow-left" class="me-2"></i>Back
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('tables.update', $id) }}">
        @csrf
        <div class="accordion-item border mb-4">
            <h2 class="accordion-header">
                <div class="accordion-button bg-white">
                    <h5 class="d-flex align-items-center"><i data-feather="grid" class="text-primary me-2"></i>Table Information</h5>
                </div>
            </h2>
            <div class="accordion-body border-top">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Table Name <span class="text-danger">*</span></label>
                            <input type="text" name="seat_label" class="form-control" value="{{ old('seat_label', $table->table_name) }}" required>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Client <span class="text-danger">*</span></label>
                            <select name="client_id" id="client_id" class="form-select" required>
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->client_id }}" {{ old('client_id', $table->client_id) == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Table Type <span class="text-danger">*</span></label>
                            <select name="seat_type" id="seat_type" class="form-select" required>
                                <option value="">-- Select Table Type --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Seating Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $table->capacity) }}" min="1">
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_id" class="form-select">
                                <option value="1" {{ old('status_id', $table->status_id) == 1 ? 'selected' : '' }}>Available</option>
                                <option value="0" {{ old('status_id', $table->status_id) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('tables.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Update Table</button>
        </div>
    </form>
</div>

<script>
const allTableTypes  = @json($tableTypes);
const currentClient  = '{{ old('client_id', $table->client_id) }}';
const currentTypeId  = '{{ old('seat_type', $table->table_type_id) }}';

function filterTableTypes(clientId) {
    const select = document.getElementById('seat_type');
    select.innerHTML = '<option value="">-- Select Table Type --</option>';

    if (!clientId) return;

    const filtered = allTableTypes.filter(t => t.client_id == clientId || t.client_id === null);

    filtered.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t.table_type_id;
        opt.text  = t.type_name;
        if (currentTypeId == t.table_type_id) opt.selected = true;
        select.appendChild(opt);
    });
}

document.getElementById('client_id').addEventListener('change', function () {
    filterTableTypes(this.value);
});

filterTableTypes(currentClient);
</script>
@endsection
