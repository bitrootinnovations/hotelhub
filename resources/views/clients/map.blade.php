@extends('layouts.app')
@section('content')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
/* Map height inside card */
#client-map {
    width: 100%;
    height: 560px;
}

/* Pin marker (50px — 30% bigger than original 38px) */
.pin-wrap {
    width: 50px; height: 50px;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    border: 3px solid #fff;
    box-shadow: 0 4px 14px rgba(0,0,0,0.35);
    overflow: hidden;
    display: flex; align-items: center; justify-content: center;
}
.pin-wrap img       { transform: rotate(45deg); width:100%; height:100%; object-fit:cover; }
.pin-letter         { transform: rotate(45deg); color:#fff; font-weight:700; font-size:20px; line-height:1; }

/* Popup */
.client-popup       { min-width: 240px; font-family: inherit; }
.cp-header          { display:flex; align-items:center; gap:12px; padding-bottom:10px; margin-bottom:10px; border-bottom:2px solid #f0f0f0; }
.cp-logo            { width:48px; height:48px; border-radius:10px; object-fit:cover; border:1px solid #eee; flex-shrink:0; }
.cp-logo-placeholder{ width:48px; height:48px; border-radius:10px; flex-shrink:0; background:#4f46e5; color:#fff; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700; }
.cp-header h6       { margin:0 0 3px; font-size:15px; font-weight:700; }
.cp-city            { font-size:12px; color:#888; margin:0; }
.cp-row             { display:flex; justify-content:space-between; align-items:center; padding:5px 0; font-size:12px; border-bottom:1px solid #f5f5f5; }
.cp-row:last-child  { border-bottom:none; }
.cp-key             { color:#777; }
.cp-val             { font-weight:600; color:#222; }
.cp-val.green       { color:#16a34a !important; }
.cp-val.amber       { color:#d97706 !important; }
.cp-val.blue        { color:#2563eb !important; }
.cp-badge           { display:inline-block; padding:2px 9px; border-radius:20px; font-size:11px; font-weight:600; }
.cp-badge.active    { background:#dcfce7; color:#16a34a; }
.cp-badge.inactive  { background:#fee2e2; color:#dc2626; }
.cp-badge.expired   { background:#fef9c3; color:#b45309; }
</style>

<div class="content">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Client Locations</h4>
                <h6>View all client outlets on map</h6>
            </div>
        </div>
    </div>

    {{-- Stat Cards — same sale-widget style as dashboard --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-primary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-primary">
                        <i class="ti ti-building-store fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Total Clients</p>
                        <h4 class="text-white" id="val-clients">—</h4>
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
                        <p class="text-white mb-1">Total Sales</p>
                        <h4 class="text-white" id="val-sales">—</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-info sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-info">
                        <i class="ti ti-chart-bar fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Today's Sales</p>
                        <h4 class="text-white" id="val-today">—</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-secondary sale-widget flex-fill">
                <div class="card-body d-flex align-items-center">
                    <span class="sale-icon bg-white text-secondary">
                        <i class="ti ti-clock-exclamation fs-24"></i>
                    </span>
                    <div class="ms-2">
                        <p class="text-white mb-1">Expired Plans</p>
                        <h4 class="text-white" id="val-expired">—</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Map Card --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 d-flex align-items-center gap-2">
                <i class="ti ti-map-pin text-primary"></i> Outlet Map
            </h5>
            <span class="text-muted fs-13" id="map-subtitle">Loading pins…</span>
        </div>
        <div class="card-body p-0">
            <div id="client-map"></div>
        </div>
    </div>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var map = L.map('client-map').setView([20.5937, 78.9629], 5);

    // ── Tile layers ────────────────────────────────────────────────────────
    var street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors', maxZoom: 19
    });
    var satellite = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        { attribution: 'Tiles © Esri', maxZoom: 19 }
    );
    var labels = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
        { attribution: '', maxZoom: 19, opacity: 0.8 }
    );

    street.addTo(map);

    L.control.layers(
        { 'Street': street, 'Satellite': satellite },
        { 'Labels': labels },
        { collapsed: false, position: 'topright' }
    ).addTo(map);

    // ── Client pins ────────────────────────────────────────────────────────
    fetch('{{ route('clients.map.data') }}')
        .then(function(r) { return r.json(); })
        .then(function(json) {
            if (!json.success || !json.data.length) {
                document.getElementById('map-subtitle').textContent = 'No clients with location data.';
                return;
            }

            var clients    = json.data;
            var totalSales = 0, todaySales = 0, expiredCnt = 0, bounds = [];

            clients.forEach(function(c) {
                totalSales += parseFloat(String(c.total_sales).replace(/,/g, '')) || 0;
                todaySales += parseFloat(String(c.today_sales).replace(/,/g, '')) || 0;
                if (c.is_expired) expiredCnt++;
                bounds.push([c.lat, c.lng]);

                var initial  = c.name.charAt(0).toUpperCase();
                var pinColor = c.is_expired ? '#d97706' : (c.status === 'Active' ? '#16a34a' : '#dc2626');
                var pinInner = c.logo
                    ? '<img src="' + c.logo + '" onerror="this.style.display=\'none\'">'
                    : '<span class="pin-letter">' + initial + '</span>';

                var icon = L.divIcon({
                    className: '',
                    html: '<div class="pin-wrap" style="background:' + pinColor + ';">' + pinInner + '</div>',
                    iconSize:    [50, 50],
                    iconAnchor:  [25, 50],
                    popupAnchor: [0, -54],
                });

                var marker = L.marker([c.lat, c.lng], { icon: icon }).addTo(map);

                var badgeClass   = c.status === 'Active' ? 'active' : 'inactive';
                var expiredExtra = c.is_expired ? ' <span class="cp-badge expired">Expired</span>' : '';
                var expiryColor  = c.is_expired ? 'amber' : 'green';
                var logoHtml     = c.logo
                    ? '<img src="' + c.logo + '" class="cp-logo">'
                    : '<div class="cp-logo-placeholder">' + initial + '</div>';

                var popup =
                    '<div class="client-popup">'
                  + '<div class="cp-header">' + logoHtml
                  + '<div><h6>' + c.name + '</h6><p class="cp-city">' + (c.city || '') + '</p></div></div>'
                  + '<div class="cp-row"><span class="cp-key">Status</span><span><span class="cp-badge ' + badgeClass + '">' + c.status + '</span>' + expiredExtra + '</span></div>'
                  + '<div class="cp-row"><span class="cp-key">Total Sales</span><span class="cp-val green">₹' + c.total_sales + '</span></div>'
                  + '<div class="cp-row"><span class="cp-key">Today\'s Sales</span><span class="cp-val blue">₹' + c.today_sales + '</span></div>'
                  + '<div class="cp-row"><span class="cp-key">Total Orders</span><span class="cp-val">' + c.total_orders + '</span></div>'
                  + '<div class="cp-row"><span class="cp-key">Plan</span><span class="cp-val">' + c.plan + ' — ₹' + c.plan_amount + '</span></div>'
                  + '<div class="cp-row"><span class="cp-key">Expiry Date</span><span class="cp-val ' + expiryColor + '">' + c.expiry_date + '</span></div>'
                  + '<div class="cp-row"><span class="cp-key">Contact</span><span class="cp-val">' + (c.contact || '—') + '</span></div>'
                  + '</div>';

                marker.bindPopup(popup, { maxWidth: 290 });
            });

            document.getElementById('val-clients').textContent = clients.length;
            document.getElementById('val-sales').textContent   = '₹' + totalSales.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            document.getElementById('val-today').textContent   = '₹' + todaySales.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            document.getElementById('val-expired').textContent = expiredCnt;
            document.getElementById('map-subtitle').textContent = clients.length + ' outlet' + (clients.length !== 1 ? 's' : '') + ' pinned';

            if (bounds.length === 1) {
                map.setView(bounds[0], 14);
            } else if (bounds.length > 1) {
                map.fitBounds(bounds, { padding: [60, 60] });
            }
        })
        .catch(function(e) {
            document.getElementById('map-subtitle').textContent = 'Failed to load map data.';
            console.error(e);
        });
})();
</script>
@endsection
