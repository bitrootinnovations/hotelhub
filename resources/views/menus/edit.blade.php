@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Menu Item</h4>
                <h6>Update menu item details</h6>
            </div>
        </div>
        <div class="page-btn mt-0">
            <a href="{{ route('menus.index') }}" class="btn btn-secondary">
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

    <form method="POST" action="{{ route('menus.update', $id) }}" enctype="multipart/form-data">
        @csrf

        <div class="accordion-item border mb-4">
            <h2 class="accordion-header">
                <div class="accordion-button bg-white">
                    <h5 class="d-flex align-items-center"><i data-feather="book-open" class="text-primary me-2"></i>Menu Details</h5>
                </div>
            </h2>
            <div class="accordion-body border-top">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Menu Name <span class="text-danger">*</span></label>
                            <input type="text" name="menu_name" class="form-control" value="{{ old('menu_name', $menu->menu_name) }}" required>
                        </div>
                    </div>
                    @if($isClientUser)
                    <input type="hidden" name="client_id" id="client_id" value="{{ Auth::user()->client_id }}">
                    @else
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Client <span class="text-danger">*</span></label>
                            <select name="client_id" id="client_id" class="form-select" required>
                                <option value="">-- Select Client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->client_id }}" {{ old('client_id', $menu->client_id) == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">-- Select Category --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Food Type <span class="text-danger">*</span></label>
                            <select name="food_type" class="form-select" required>
                                <option value="1" {{ old('food_type', $menu->food_type) == 1 ? 'selected' : '' }}>🟢 Veg</option>
                                <option value="2" {{ old('food_type', $menu->food_type) == 2 ? 'selected' : '' }}>🔴 Non-Veg</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', $menu->price) }}" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">GST % <span class="text-danger">*</span></label>
                            <input type="number" name="gst_percentage" class="form-control" value="{{ old('gst_percentage', $menu->gst_percentage) }}" step="0.01" min="0" max="100" required>
                        </div>
                    </div>
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">Stock Type <span class="text-danger">*</span></label>
                            <select name="stock_type" class="form-select" required>
                                <option value="Unit" {{ old('stock_type', $menu->stock_type) == 'Unit' ? 'selected' : '' }}>Unit</option>
                                <option value="Kg"   {{ old('stock_type', $menu->stock_type) == 'Kg'   ? 'selected' : '' }}>Kg</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">Quantity <span class="text-muted fs-12">(optional)</span></label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $menu->quantity) }}" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status_id" class="form-select">
                                <option value="1" {{ old('status_id', $menu->status_id) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status_id', $menu->status_id) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="accordion-item border mb-4">
            <h2 class="accordion-header">
                <div class="accordion-button collapsed bg-white" data-bs-toggle="collapse" data-bs-target="#collapseImg">
                    <h5 class="d-flex align-items-center"><i data-feather="image" class="text-warning me-2"></i>Menu Image</h5>
                </div>
            </h2>
            <div id="collapseImg" class="accordion-collapse collapse">
                <div class="accordion-body border-top">
                    @if($menu->image)
                        <div class="mb-3">
                            <label class="form-label">Current Image</label><br>
                            <img src="{{ $base_url }}/storage/app/public/{{ $menu->image }}" class="img-thumbnail" style="max-height:150px;">
                        </div>
                    @endif
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label">{{ $menu->image ? 'Replace Image' : 'Upload Image' }}</label>
                            <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this,'imgPrev')">
                        </div>
                        <div class="col-md-6">
                            <img id="imgPrev" src="#" class="img-thumbnail d-none" style="max-height:150px;" alt="Preview">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('menus.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Update Menu Item</button>
        </div>
    </form>
</div>
<script>
function previewImage(input, id) {
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => { const p = document.getElementById(id); p.src = e.target.result; p.classList.remove('d-none'); };
        r.readAsDataURL(input.files[0]);
    }
}

const allCategories  = @json($categories);
const currentClient  = '{{ old('client_id', $menu->client_id) }}';
const currentCatId   = '{{ old('category_id', $menu->category_id) }}';

function filterCategories(clientId) {
    const select = document.getElementById('category_id');
    select.innerHTML = '<option value="">-- Select Category --</option>';
    if (!clientId) return;

    const filtered = allCategories.filter(c => c.client_id == clientId || c.client_id === null);
    filtered.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.category_id;
        opt.text  = c.category_name;
        if (currentCatId == c.category_id) opt.selected = true;
        select.appendChild(opt);
    });
}

const clientEl = document.getElementById('client_id');
if (clientEl.tagName === 'SELECT') {
    clientEl.addEventListener('change', function () { filterCategories(this.value); });
}

filterCategories(currentClient);
</script>
@endsection
