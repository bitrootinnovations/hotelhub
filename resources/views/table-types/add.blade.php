@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Add Table Type</h4>
                <h6>Create a new table type (Indoor, Outdoor, Rooftop)</h6>
            </div>
        </div>
        <div class="page-btn mt-0">
            <a href="{{ route('table-types.index') }}" class="btn btn-secondary">
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

    <form method="POST" action="{{ route('table-types.store') }}">
        @csrf
        <div class="accordion-item border mb-4">
            <h2 class="accordion-header">
                <div class="accordion-button bg-white">
                    <h5 class="d-flex align-items-center"><i data-feather="layers" class="text-primary me-2"></i>Table Type Information</h5>
                </div>
            </h2>
            <div class="accordion-body border-top">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="type_name" class="form-control" value="{{ old('type_name') }}" placeholder="e.g. Indoor, Outdoor, Rooftop" required>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Client <span class="text-muted fs-12">(leave blank for global)</span></label>
                            <select name="client_id" class="form-select">
                                <option value="">-- Global (All Clients) --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->client_id }}" {{ old('client_id') == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_id" class="form-select">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('table-types.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Save Table Type</button>
        </div>
    </form>
</div>
@endsection
