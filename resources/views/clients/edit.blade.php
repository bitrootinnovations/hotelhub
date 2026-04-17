@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Client</h4>
                <h6>Update hotel client details</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh"><i class="ti ti-refresh"></i></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header">
                    <i class="ti ti-chevron-up"></i>
                </a>
            </li>
        </ul>
        <div class="page-btn mt-0">
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                <i data-feather="arrow-left" class="me-2"></i>Back to Clients
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ $id }}" class="add-product-form" enctype="multipart/form-data">
        @csrf
        <div class="add-product">
            <div class="accordions-items-seperate" id="accordionSpacingExample">

                {{-- Basic Information --}}
                <div class="accordion-item border mb-4">
                    <h2 class="accordion-header" id="headingBasic">
                        <div class="accordion-button collapsed bg-white" data-bs-toggle="collapse"
                            data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseBasic">
                            <div class="d-flex align-items-center justify-content-between flex-fill">
                                <h5 class="d-flex align-items-center">
                                    <i data-feather="info" class="text-primary me-2"></i>
                                    <span>Basic Information</span>
                                </h5>
                            </div>
                        </div>
                    </h2>
                    <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingBasic">
                        <div class="accordion-body border-top">
                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Client Name <span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="client_name"
                                            value="{{ old('client_name', $client->client_name) }}" placeholder="Enter client name">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Email ID <span class="text-danger ms-1">*</span></label>
                                        <input type="email" class="form-control" name="email_id"
                                            value="{{ old('email_id', $client->email_id) }}" placeholder="Enter email address">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Contact Number <span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="contact_number"
                                            value="{{ old('contact_number', $client->contact_number) }}" placeholder="Enter contact number">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">GST Number</label>
                                        <input type="text" class="form-control" name="gst_number"
                                            value="{{ old('gst_number', $client->gst_number) }}" placeholder="Enter GST number">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">UPI ID</label>
                                        <input type="text" class="form-control" name="upi_id"
                                            value="{{ old('upi_id', $client->upi_id) }}" placeholder="e.g. navalerestro@upi">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="select form-select" name="status_id">
                                            <option value="1" {{ old('status_id', $client->status_id) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="2" {{ old('status_id', $client->status_id) == 2 ? 'selected' : '' }}>Inactive</option>
                                            <option value="3" {{ old('status_id', $client->status_id) == 3 ? 'selected' : '' }}>Suspended</option>
                                            <option value="4" {{ old('status_id', $client->status_id) == 4 ? 'selected' : '' }}>Trial</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Address Information --}}
                <div class="accordion-item border mb-4">
                    <h2 class="accordion-header" id="headingAddress">
                        <div class="accordion-button collapsed bg-white" data-bs-toggle="collapse"
                            data-bs-target="#collapseAddress" aria-expanded="true" aria-controls="collapseAddress">
                            <div class="d-flex align-items-center justify-content-between flex-fill">
                                <h5 class="d-flex align-items-center">
                                    <i data-feather="map-pin" class="text-primary me-2"></i>
                                    <span>Address & Location</span>
                                </h5>
                            </div>
                        </div>
                    </h2>
                    <div id="collapseAddress" class="accordion-collapse collapse show" aria-labelledby="headingAddress">
                        <div class="accordion-body border-top">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Address <span class="text-danger ms-1">*</span></label>
                                        <textarea class="form-control" name="address" rows="2"
                                            placeholder="Enter full address">{{ old('address', $client->address) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city"
                                            value="{{ old('city', $client->city) }}" placeholder="City">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control" name="state"
                                            value="{{ old('state', $client->state) }}" placeholder="State">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Pincode</label>
                                        <input type="text" class="form-control" name="pincode"
                                            value="{{ old('pincode', $client->pincode) }}" placeholder="Pincode">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Latitude</label>
                                        <input type="text" class="form-control" name="latitude"
                                            value="{{ old('latitude', $client->latitude) }}" placeholder="e.g. 18.5204">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Longitude</label>
                                        <input type="text" class="form-control" name="longitude"
                                            value="{{ old('longitude', $client->longitude) }}" placeholder="e.g. 73.8567">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Profile Image --}}
                <div class="accordion-item border mb-4">
                    <h2 class="accordion-header" id="headingImage">
                        <div class="accordion-button collapsed bg-white" data-bs-toggle="collapse"
                            data-bs-target="#collapseImage" aria-expanded="true" aria-controls="collapseImage">
                            <div class="d-flex align-items-center justify-content-between flex-fill">
                                <h5 class="d-flex align-items-center">
                                    <i data-feather="image" class="text-primary me-2"></i>
                                    <span>Client Profile Image</span>
                                </h5>
                            </div>
                        </div>
                    </h2>
                    <div id="collapseImage" class="accordion-collapse collapse show" aria-labelledby="headingImage">
                        <div class="accordion-body border-top">
                            <div class="col-lg-12">
                                <div class="add-choosen">
                                    @if($client->image)
                                        <div class="phone-img-container mb-2">
                                            <div class="phone-img">
                                                <img src="{{ $base_url }}/storage/app/public/{{ $client->image }}"
                                                    alt="Current Profile" style="max-height:100px;">
                                                <p class="fs-12 text-muted mt-1">Current image</p>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <div class="image-upload image-upload-two">
                                            <input type="file" id="profileImageInput" accept="image/*" name="image">
                                            <div class="image-uploads">
                                                <i data-feather="plus-circle" class="plus-down-add me-0"></i>
                                                <h4>{{ $client->image ? 'Change Profile Image' : 'Upload Profile Image' }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="profilePreviewContainer" class="phone-img-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subscription Details --}}
                <div class="accordion-item border mb-4">
                    <h2 class="accordion-header" id="headingSubscription">
                        <div class="accordion-button collapsed bg-white" data-bs-toggle="collapse"
                            data-bs-target="#collapseSubscription" aria-expanded="true" aria-controls="collapseSubscription">
                            <div class="d-flex align-items-center justify-content-between flex-fill">
                                <h5 class="d-flex align-items-center">
                                    <i data-feather="refresh-cw" class="text-primary me-2"></i>
                                    <span>Subscription Details</span>
                                </h5>
                            </div>
                        </div>
                    </h2>
                    <div id="collapseSubscription" class="accordion-collapse collapse show" aria-labelledby="headingSubscription">
                        <div class="accordion-body border-top">
                            <div class="row">
                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Subscription Type</label>
                                        <select class="form-select" name="subscription_type" id="subscriptionType">
                                            <option value="">-- No Subscription --</option>
                                            <option value="Monthly"   {{ old('subscription_type', $client->subscription_type) == 'Monthly'   ? 'selected' : '' }}>Monthly</option>
                                            <option value="Quarterly" {{ old('subscription_type', $client->subscription_type) == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                            <option value="Yearly"    {{ old('subscription_type', $client->subscription_type) == 'Yearly'    ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Subscription Price (₹)</label>
                                        <input type="number" step="0.01" min="0" class="form-control"
                                            name="subscription_price"
                                            value="{{ old('subscription_price', $client->subscription_price) }}"
                                            placeholder="e.g. 999.00">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="subscription_start_date"
                                            id="subscriptionStartDate"
                                            value="{{ old('subscription_start_date', $client->subscription_start_date?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">End Date <small class="text-muted">(auto-calculated)</small></label>
                                        <input type="text" class="form-control bg-light" id="subscriptionEndDatePreview"
                                            value="{{ $client->subscription_end_date?->format('d M Y') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Plan Type</label>
                                        <select class="form-select" name="plan_type">
                                            <option value="Basic"   {{ old('plan_type', $client->plan_type ?? 'Basic') == 'Basic'   ? 'selected' : '' }}>Basic (Mobile App Only)</option>
                                            <option value="Premium" {{ old('plan_type', $client->plan_type) == 'Premium' ? 'selected' : '' }}>Premium (Mobile + Web Portal)</option>
                                        </select>
                                        <small class="text-muted">Premium clients can access the web portal.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aadhar Image --}}
                <div class="accordion-item border mb-4">
                    <h2 class="accordion-header" id="headingAadhar">
                        <div class="accordion-button collapsed bg-white" data-bs-toggle="collapse"
                            data-bs-target="#collapseAadhar" aria-expanded="true" aria-controls="collapseAadhar">
                            <div class="d-flex align-items-center justify-content-between flex-fill">
                                <h5 class="d-flex align-items-center">
                                    <i data-feather="credit-card" class="text-primary me-2"></i>
                                    <span>Aadhar Card Image</span>
                                </h5>
                            </div>
                        </div>
                    </h2>
                    <div id="collapseAadhar" class="accordion-collapse collapse show" aria-labelledby="headingAadhar">
                        <div class="accordion-body border-top">
                            <div class="col-lg-12">
                                <div class="add-choosen">
                                    @if($client->aadhar_image)
                                        <div class="phone-img-container mb-2">
                                            <div class="phone-img">
                                                <img src="{{ $base_url }}/storage/app/public/{{ $client->aadhar_image }}"
                                                    alt="Current Aadhar" style="max-height:100px;">
                                                <p class="fs-12 text-muted mt-1">Current aadhar image</p>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <div class="image-upload image-upload-two">
                                            <input type="file" id="aadharImageInput" accept="image/*" name="aadhar_image">
                                            <div class="image-uploads">
                                                <i data-feather="plus-circle" class="plus-down-add me-0"></i>
                                                <h4>{{ $client->aadhar_image ? 'Change Aadhar Image' : 'Upload Aadhar Image' }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="aadharPreviewContainer" class="phone-img-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Printers --}}
                <div class="accordion-item border mb-4">
                    <h2 class="accordion-header" id="headingPrinters">
                        <div class="accordion-button collapsed bg-white" data-bs-toggle="collapse"
                            data-bs-target="#collapsePrinters" aria-expanded="true" aria-controls="collapsePrinters">
                            <div class="d-flex align-items-center justify-content-between flex-fill">
                                <h5 class="d-flex align-items-center">
                                    <i data-feather="printer" class="text-primary me-2"></i>
                                    <span>Printers
                                        @if($printers->count())
                                            <span class="badge bg-primary ms-1">{{ $printers->count() }}</span>
                                        @endif
                                    </span>
                                </h5>
                            </div>
                        </div>
                    </h2>
                    <div id="collapsePrinters" class="accordion-collapse collapse show" aria-labelledby="headingPrinters">
                        <div class="accordion-body border-top">
                            <div id="printerRows">
                                @foreach($printers as $printer)
                                <div class="row g-2 mb-2 printer-row align-items-end">
                                    <input type="hidden" name="printers[{{ $loop->index }}][id]" value="{{ $printer->id }}">
                                    <div class="col-sm-4 col-12">
                                        <label class="form-label fs-12 mb-1">Printer ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm"
                                            name="printers[{{ $loop->index }}][printer_id]"
                                            value="{{ old('printers.'.$loop->index.'.printer_id', $printer->mac_address) }}"
                                            placeholder="e.g. AA:BB:CC:DD:EE:FF">
                                    </div>
                                    <div class="col-sm-4 col-12">
                                        <label class="form-label fs-12 mb-1">Device Name</label>
                                        <input type="text" class="form-control form-control-sm"
                                            name="printers[{{ $loop->index }}][device_name]"
                                            value="{{ old('printers.'.$loop->index.'.device_name', $printer->device_name) }}"
                                            placeholder="e.g. Kitchen Printer">
                                    </div>
                                    <div class="col-sm-3 col-12">
                                        <label class="form-label fs-12 mb-1">Type</label>
                                        <select class="form-select form-select-sm" name="printers[{{ $loop->index }}][printer_type]">
                                            <option value="">-- Select --</option>
                                            @foreach(['Bluetooth','WiFi','USB','Network'] as $pt)
                                            <option value="{{ $pt }}" {{ old('printers.'.$loop->index.'.printer_type', $printer->printer_type) == $pt ? 'selected' : '' }}>{{ $pt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-1 col-12">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100"
                                            onclick="this.closest('.printer-row').remove()" title="Remove">
                                            <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                                        </button>
                                    </div>
                                    @if($printer->status === 'online')
                                    <div class="col-12">
                                        <span class="badge bg-success"><i class="ti ti-wifi me-1"></i>Online · Last seen {{ $printer->last_seen_at?->diffForHumans() }}</span>
                                    </div>
                                    @else
                                    <div class="col-12">
                                        <span class="badge bg-secondary">Offline</span>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="addPrinterRow()">
                                <i data-feather="plus" class="me-1" style="width:14px;height:14px;"></i>Add Printer
                            </button>
                            <p class="text-muted fs-12 mt-2 mb-0">Enter the Printer ID printed on the device label (MAC address or unique ID).</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-12">
            <div class="d-flex align-items-center justify-content-end mb-4">
                <a href="{{ route('clients.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Client</button>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(inputId, containerId) {
    document.getElementById(inputId).addEventListener('change', function (event) {
        let container = document.getElementById(containerId);
        container.innerHTML = '';
        let file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let imgWrapper = document.createElement('div');
                imgWrapper.classList.add('phone-img');
                let img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview';
                let removeBtn = document.createElement('a');
                removeBtn.href = 'javascript:void(0);';
                removeBtn.innerHTML = '<i data-feather="x" class="x-square-add remove-product"></i>';
                removeBtn.onclick = function () { imgWrapper.remove(); };
                imgWrapper.appendChild(img);
                imgWrapper.appendChild(removeBtn);
                container.appendChild(imgWrapper);
                if (typeof feather !== 'undefined') feather.replace();
            };
            reader.readAsDataURL(file);
        }
    });
}
previewImage('profileImageInput', 'profilePreviewContainer');
previewImage('aadharImageInput', 'aadharPreviewContainer');

function updateEndDatePreview() {
    var type  = document.getElementById('subscriptionType').value;
    var start = document.getElementById('subscriptionStartDate').value;
    var preview = document.getElementById('subscriptionEndDatePreview');
    if (!type || !start) { preview.value = ''; return; }
    var d = new Date(start);
    if (type === 'Monthly')   d.setMonth(d.getMonth() + 1);
    if (type === 'Quarterly') d.setMonth(d.getMonth() + 3);
    if (type === 'Yearly')    d.setFullYear(d.getFullYear() + 1);
    preview.value = d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
}
document.getElementById('subscriptionType').addEventListener('change', updateEndDatePreview);
document.getElementById('subscriptionStartDate').addEventListener('change', updateEndDatePreview);

// ── Printer rows ───────────────────────────────────────────────────────────────
var printerIndex = {{ $printers->count() }};
function addPrinterRow(id, printerId, deviceName, printerType) {
    var idx = printerIndex++;
    var types = ['Bluetooth','WiFi','USB','Network'];
    var opts = '<option value="">-- Select --</option>' +
        types.map(function(t){ return '<option value="'+t+'"'+(printerType===t?' selected':'')+'>'+t+'</option>'; }).join('');
    var row = document.createElement('div');
    row.className = 'row g-2 mb-2 printer-row align-items-end';
    row.innerHTML =
        (id ? '<input type="hidden" name="printers['+idx+'][id]" value="'+id+'">' : '') +
        '<div class="col-sm-4 col-12">' +
            '<label class="form-label fs-12 mb-1">Printer ID <span class="text-danger">*</span></label>' +
            '<input type="text" class="form-control form-control-sm" name="printers['+idx+'][printer_id]"' +
            ' placeholder="e.g. AA:BB:CC:DD:EE:FF" value="'+(printerId||'')+'">' +
        '</div>' +
        '<div class="col-sm-4 col-12">' +
            '<label class="form-label fs-12 mb-1">Device Name</label>' +
            '<input type="text" class="form-control form-control-sm" name="printers['+idx+'][device_name]"' +
            ' placeholder="e.g. Kitchen Printer" value="'+(deviceName||'')+'">' +
        '</div>' +
        '<div class="col-sm-3 col-12">' +
            '<label class="form-label fs-12 mb-1">Type</label>' +
            '<select class="form-select form-select-sm" name="printers['+idx+'][printer_type]">'+opts+'</select>' +
        '</div>' +
        '<div class="col-sm-1 col-12">' +
            '<button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest(\'.printer-row\').remove()" title="Remove">' +
                '<i data-feather="trash-2" style="width:14px;height:14px;"></i>' +
            '</button>' +
        '</div>';
    document.getElementById('printerRows').appendChild(row);
    if (typeof feather !== 'undefined') feather.replace();
}
</script>
@endsection
