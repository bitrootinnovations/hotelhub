@extends('layouts.app')
@section('content')
<div class="content">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-title">
            <h4 class="fw-bold">
                Table {{ $table->table_name }}
                <span class="badge fs-13 ms-2
                    @if($table->status_id == 0) bg-secondary
                    @elseif($activeOrders->isNotEmpty()) bg-danger
                    @else bg-success @endif">
                    @if($table->status_id == 0) Inactive
                    @elseif($activeOrders->isNotEmpty()) Occupied
                    @else Available @endif
                </span>
            </h4>
            <h6 class="text-muted">
                {{ $table->tableType->type_name ?? '' }}
                @if($table->capacity) · Capacity {{ $table->capacity }} @endif
            </h6>
        </div>
        <div class="page-btn mt-0">
            <a href="{{ route('client.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">

        {{-- LEFT: Active Orders / KOTs ──────────────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <h6 class="mb-0 fw-bold">
                        <i class="ti ti-receipt me-1 text-primary"></i>Active Orders
                        @if($activeOrders->isNotEmpty())
                            <span class="badge bg-danger ms-1">{{ $activeOrders->count() }}</span>
                        @endif
                    </h6>
                    <span class="text-muted fs-12">Table {{ $table->table_name }}</span>
                </div>
                <div class="card-body p-0" id="activeOrdersPanel">
                    @if($activeOrders->isEmpty())
                        <div class="text-center py-5 text-muted" id="noOrdersMsg">
                            <i class="ti ti-clipboard-off fs-36 d-block mb-2"></i>
                            No active orders for this table.
                        </div>
                    @else
                        @foreach($activeOrders as $order)
                        <div class="order-block border-bottom p-3" id="order-block-{{ $order->order_id }}">
                            {{-- Order header --}}
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <span class="fw-semibold">{{ $order->order_number }}</span>
                                    <span class="badge ms-2 status-badge-{{ $order->order_id }}
                                        {{ match($order->status) {
                                            'pending'   => 'bg-warning text-dark',
                                            'confirmed' => 'bg-primary',
                                            'preparing' => 'bg-info',
                                            'served'    => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            default     => 'bg-secondary'
                                        } }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                            </div>

                            {{-- Items --}}
                            <table class="table table-sm mb-2">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr id="item-row-{{ $item->item_id }}">
                                        <td>
                                            <span class="@if($item->menu && $item->menu->food_type == 1) text-success @else text-danger @endif me-1">●</span>
                                            {{ $item->menu->menu_name ?? '—' }}
                                        </td>
                                        <td class="text-center">× {{ $item->quantity }}</td>
                                        <td class="text-end">₹{{ number_format($item->total_price, 2) }}</td>
                                        <td class="text-end">
                                            @if(in_array($order->status, ['pending','confirmed','preparing']))
                                            <a href="#" class="text-danger" title="Remove"
                                               onclick="removeItem({{ $item->item_id }}, {{ $order->order_id }}, this); return false;">
                                                <i class="ti ti-trash fs-14"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Totals --}}
                            <div class="d-flex justify-content-between fs-13 text-muted mb-1">
                                <span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->gst_amount > 0)
                            <div class="d-flex justify-content-between fs-13 text-muted mb-1">
                                <span>GST</span><span>₹{{ number_format($order->gst_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between fw-bold fs-15 mb-3">
                                <span>Total</span><span>₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>

                            {{-- Status actions --}}
                            <div class="d-flex gap-1 flex-wrap mb-2">
                                @php
                                    $statusFlow = ['pending'=>'Confirm','confirmed'=>'Start Cooking','preparing'=>'Mark Served'];
                                    $nextLabel  = $statusFlow[$order->status] ?? null;
                                    $nextStatus = match($order->status) {
                                        'pending'   => 'confirmed',
                                        'confirmed' => 'preparing',
                                        'preparing' => 'served',
                                        default     => null,
                                    };
                                @endphp
                                @if($nextStatus)
                                <button class="btn btn-sm btn-outline-primary"
                                    onclick="updateStatus({{ $order->order_id }}, '{{ $nextStatus }}', this)">
                                    <i class="ti ti-chevron-right me-1"></i>{{ $nextLabel }}
                                </button>
                                @endif
                                @if($order->status !== 'cancelled')
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="updateStatus({{ $order->order_id }}, 'cancelled', this)">
                                    <i class="ti ti-x me-1"></i>Cancel
                                </button>
                                @endif
                            </div>

                            {{-- Checkout --}}
                            @if(in_array($order->status, ['pending','confirmed','preparing','served']))
                            <form method="POST" action="{{ route('client.order.checkout', $order->order_id) }}"
                                  class="d-flex gap-2 align-items-center mt-1">
                                @csrf
                                <select name="payment_type" class="form-select form-select-sm" style="max-width:130px;" required>
                                    <option value="">Payment</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Card">Card</option>
                                    <option value="Online">Online</option>
                                    <option value="Other">Other</option>
                                </select>
                                <button type="submit" class="btn btn-success btn-sm flex-fill"
                                    onclick="return confirm('Checkout ₹{{ number_format($order->total_amount,2) }}?')">
                                    <i class="ti ti-cash me-1"></i>Checkout
                                </button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT: Menu — Place New Order ───────────────────────────────────── --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0 fw-bold"><i class="ti ti-menu-2 me-1 text-primary"></i>Menu — Place New Order</h6>
                </div>
                <div class="card-body p-0">

                    {{-- Category tabs --}}
                    @if($categories->isNotEmpty())
                    <div class="px-3 pt-3 pb-1 border-bottom">
                        <div class="d-flex gap-2 flex-wrap" id="categoryTabs">
                            <button class="btn btn-sm btn-primary cat-tab active" data-cat="all">All</button>
                            @foreach($categories as $cat)
                            <button class="btn btn-sm btn-outline-primary cat-tab" data-cat="{{ $cat->category_id }}">
                                {{ $cat->category_name }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Menu items grid --}}
                    <div class="p-3" style="max-height: 480px; overflow-y: auto;" id="menuGrid">
                        @foreach($categories as $cat)
                        <div class="mb-3 cat-section" data-cat="{{ $cat->category_id }}">
                            <div class="fw-semibold text-muted fs-12 text-uppercase mb-2 border-bottom pb-1">
                                {{ $cat->category_name }}
                            </div>
                            <div class="row g-2">
                                @foreach($cat->menus as $menu)
                                <div class="col-6 col-xl-4 menu-item-col" data-cat="{{ $cat->category_id }}">
                                    <div class="card border h-100 menu-item-card" id="card-{{ $menu->menu_id }}">
                                        <div class="card-body p-2 d-flex flex-column">
                                            <div class="d-flex align-items-start justify-content-between mb-1">
                                                <span class="fs-13 fw-semibold lh-sm flex-fill me-1">{{ $menu->menu_name }}</span>
                                                <span class="badge {{ $menu->food_type == 1 ? 'bg-success' : 'bg-danger' }} flex-shrink-0" style="font-size:10px;">
                                                    {{ $menu->food_type == 1 ? 'V' : 'N' }}
                                                </span>
                                            </div>
                                            <div class="fw-bold text-primary mb-2">₹{{ number_format($menu->price, 2) }}</div>
                                            {{-- Qty control --}}
                                            <div class="d-flex align-items-center gap-1 mt-auto qty-control" id="qty-ctrl-{{ $menu->menu_id }}" style="display:none!important;">
                                                <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeQty({{ $menu->menu_id }}, -1)">−</button>
                                                <input type="number" class="form-control form-control-sm text-center px-1 qty-input"
                                                    id="qty-{{ $menu->menu_id }}" value="0" min="0" max="99"
                                                    style="width:46px;"
                                                    data-menu="{{ $menu->menu_id }}"
                                                    data-name="{{ $menu->menu_name }}"
                                                    data-price="{{ $menu->price }}"
                                                    onchange="onQtyChange({{ $menu->menu_id }})">
                                                <button class="btn btn-sm btn-outline-primary px-2" onclick="changeQty({{ $menu->menu_id }}, 1)">+</button>
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary w-100 mt-auto add-btn"
                                                id="add-btn-{{ $menu->menu_id }}"
                                                onclick="initItem({{ $menu->menu_id }})">
                                                <i class="ti ti-plus me-1"></i>Add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Order summary bar --}}
                    <div class="border-top p-3 bg-light" id="orderSummaryBar" style="display:none;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="fw-semibold" id="summaryCount">0 item(s)</span>
                                <span class="text-muted ms-2">·</span>
                                <span class="fw-bold text-primary ms-2" id="summaryTotal">₹0.00</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="clearAll()">
                                <i class="ti ti-trash me-1"></i>Clear
                            </button>
                        </div>
                        <form method="POST" action="{{ route('client.table.placeOrder', $table->table_id) }}" id="placeOrderForm">
                            @csrf
                            <div id="hiddenItemInputs"></div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="ti ti-check me-1"></i>Place Order (KOT)
                            </button>
                        </form>
                    </div>

                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="ti ti-books-off fs-36 d-block mb-2"></i>
                            No menu items found. <a href="{{ route('menus.create') }}">Add menu items</a> first.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- /row --}}
</div>

<script>
// ── Category filter ────────────────────────────────────────────────────────────
document.querySelectorAll('.cat-tab').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.cat-tab').forEach(b => {
            b.classList.remove('btn-primary', 'active');
            b.classList.add('btn-outline-primary');
        });
        this.classList.add('btn-primary', 'active');
        this.classList.remove('btn-outline-primary');

        const cat = this.dataset.cat;
        document.querySelectorAll('.cat-section').forEach(sec => {
            sec.style.display = (cat === 'all' || sec.dataset.cat == cat) ? '' : 'none';
        });
    });
});

// ── Cart state ─────────────────────────────────────────────────────────────────
const cart = {};

function initItem(menuId) {
    document.getElementById('add-btn-' + menuId).style.display = 'none';
    const ctrl = document.getElementById('qty-ctrl-' + menuId);
    ctrl.style.display = 'flex';
    ctrl.style.removeProperty('display'); // override !important
    ctrl.style.cssText = 'display:flex!important;';
    setQty(menuId, 1);
}

function changeQty(menuId, delta) {
    const input = document.getElementById('qty-' + menuId);
    const newVal = Math.max(0, parseInt(input.value || 0) + delta);
    input.value = newVal;
    onQtyChange(menuId);
}

function onQtyChange(menuId) {
    const input  = document.getElementById('qty-' + menuId);
    const qty    = parseInt(input.value) || 0;
    const price  = parseFloat(input.dataset.price);
    const name   = input.dataset.name;

    if (qty === 0) {
        delete cart[menuId];
        document.getElementById('add-btn-' + menuId).style.display = '';
        document.getElementById('qty-ctrl-' + menuId).style.cssText = 'display:none!important;';
        document.getElementById('card-' + menuId).classList.remove('border-primary', 'bg-primary-subtle');
    } else {
        cart[menuId] = { qty, price, name, menuId };
        document.getElementById('card-' + menuId).classList.add('border-primary', 'bg-primary-subtle');
    }
    refreshSummary();
}

function setQty(menuId, qty) {
    document.getElementById('qty-' + menuId).value = qty;
    onQtyChange(menuId);
}

function refreshSummary() {
    const items = Object.values(cart);
    const bar   = document.getElementById('orderSummaryBar');

    if (items.length === 0) {
        bar.style.display = 'none';
        return;
    }

    bar.style.display = '';
    const totalQty   = items.reduce((s, i) => s + i.qty, 0);
    const totalPrice = items.reduce((s, i) => s + (i.qty * i.price), 0);
    document.getElementById('summaryCount').textContent = totalQty + ' item(s)';
    document.getElementById('summaryTotal').textContent = '₹' + totalPrice.toFixed(2);

    // Rebuild hidden inputs
    const container = document.getElementById('hiddenItemInputs');
    container.innerHTML = '';
    let idx = 0;
    items.forEach(item => {
        const mi = document.createElement('input');
        mi.type  = 'hidden'; mi.name = 'items[' + idx + '][menu_id]'; mi.value = item.menuId;
        const qi = document.createElement('input');
        qi.type  = 'hidden'; qi.name = 'items[' + idx + '][qty]'; qi.value = item.qty;
        container.appendChild(mi);
        container.appendChild(qi);
        idx++;
    });
}

function clearAll() {
    Object.keys(cart).forEach(menuId => setQty(menuId, 0));
}

// ── Update order status via AJAX ───────────────────────────────────────────────
function updateStatus(orderId, status, btn) {
    btn.disabled = true;
    fetch('{{ url("client/table/order") }}/' + orderId + '/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to update status.');
            btn.disabled = false;
        }
    })
    .catch(() => { btn.disabled = false; alert('Network error.'); });
}

// ── Remove item via AJAX ───────────────────────────────────────────────────────
function removeItem(itemId, orderId, link) {
    if (!confirm('Remove this item?')) return;
    link.style.opacity = '0.4';
    fetch('{{ url("client/table/item") }}/' + itemId + '/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.order_deleted) {
                document.getElementById('order-block-' + orderId)?.remove();
                const remaining = document.querySelectorAll('.order-block').length;
                if (remaining === 0) location.reload();
            } else {
                document.getElementById('item-row-' + itemId)?.remove();
            }
        } else {
            link.style.opacity = '';
            alert('Could not remove item.');
        }
    })
    .catch(() => { link.style.opacity = ''; alert('Network error.'); });
}
</script>
@endsection
