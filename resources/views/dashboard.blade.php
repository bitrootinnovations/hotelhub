@extends('layouts.app')
@section('content')
<div class="content">

				<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-2">
					<div class="mb-3">
						<h1 class="mb-1">Welcome, Company Admin</h1>
						<p class="fw-medium">You have <span class="text-primary fw-bold">{{ $todayOrders }}</span> Hotel Orders, Today</p>
					</div>
					<div class="input-icon-start position-relative mb-3">
						<span class="input-icon-addon fs-16 text-gray-9">
							<i class="ti ti-calendar"></i>
						</span>
						<input type="text" class="form-control date-range bookingrange" placeholder="Search Product">
					</div>
		</div>

		@if($expiringClients->isNotEmpty())
		@php $firstExpiring = $expiringClients->first(); @endphp
		<div class="alert bg-orange-transparent alert-dismissible fade show mb-4">
			<div>
				<span><i class="ti ti-info-circle fs-14 text-orange me-2"></i>Hotel Client </span>
				<span class="text-orange fw-semibold"> {{ $firstExpiring->client_name }}'s subscription is expiring soon. </span>
				Renew before it expires.
			</div>
			<button type="button" class="btn-close text-gray-9 fs-14" data-bs-dismiss="alert" aria-label="Close"><i class="ti ti-x"></i></button>
		</div>
		@endif

		<div class="row">
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card bg-primary sale-widget flex-fill">
					<div class="card-body d-flex align-items-center">
						<span class="sale-icon bg-white text-primary">
							<i class="ti ti-file-text fs-24"></i>
						</span>
						<div class="ms-2">
							<p class="text-white mb-1">Total Platform Revenue</p>
							<div class="d-inline-flex align-items-center flex-wrap gap-2">
								<h4 class="text-white">₹{{ number_format($totalRevenue, 2) }}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card bg-secondary sale-widget flex-fill">
					<div class="card-body d-flex align-items-center">
						<span class="sale-icon bg-white text-secondary">
							<i class="ti ti-repeat fs-24"></i>
						</span>
						<div class="ms-2">
							<p class="text-white mb-1">Total Refunds</p>
							<div class="d-inline-flex align-items-center flex-wrap gap-2">
								<h4 class="text-white">₹16,478,145</h4>
								<span class="badge badge-soft-danger"><i class="ti ti-arrow-down me-1"></i>-22%</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card bg-teal sale-widget flex-fill">
					<div class="card-body d-flex align-items-center">
						<span class="sale-icon bg-white text-teal">
							<i class="ti ti-gift fs-24"></i>
						</span>
						<div class="ms-2">
							<p class="text-white mb-1">Total Subscriptions</p>
							<div class="d-inline-flex align-items-center flex-wrap gap-2">
								<h4 class="text-white">₹{{ number_format($totalSubscription, 2) }}</h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card bg-info sale-widget flex-fill">
					<div class="card-body d-flex align-items-center">
						<span class="sale-icon bg-white text-info">
							<i class="ti ti-brand-pocket fs-24"></i>
						</span>
						<div class="ms-2">
							<p class="text-white mb-1">Subscription Cancellations</p>
							<div class="d-inline-flex align-items-center flex-wrap gap-2">
								<h4 class="text-white">₹18,458,747</h4>
								<span class="badge badge-soft-success"><i class="ti ti-arrow-up me-1"></i>+22%</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		
		<div class="row">

			<!-- Profit -->
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card revenue-widget flex-fill">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
							<div>
								<h4 class="mb-1">₹8,458,798</h4>
								<p>Net Platform Profit</p>
							</div>
							<span class="revenue-icon bg-cyan-transparent text-cyan">
								<i class="fa-solid fa-layer-group fs-16"></i>
							</span>
						</div>
						<div class="d-flex align-items-center justify-content-between">
							<p class="mb-0"><span class="fs-13 fw-bold text-success">+35%</span> vs Last Month</p>
							<a href="profit-and-loss.html" class="text-decoration-underline fs-13 fw-medium">View All</a>
						</div>
					</div>
				</div>
			</div>
			<!-- /Profit -->

			<!-- Invoice -->
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card revenue-widget flex-fill">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
							<div>
								<h4 class="mb-1">₹48,988,78</h4>
								<p>Pending Hotel Invoices</p>
							</div>
							<span class="revenue-icon bg-teal-transparent text-teal">
								<i class="ti ti-chart-pie fs-16"></i>
							</span>
						</div>
						<div class="d-flex align-items-center justify-content-between">
							<p class="mb-0"><span class="fs-13 fw-bold text-success">+35%</span> vs Last Month</p>
							<a href="invoice-report.html" class="text-decoration-underline fs-13 fw-medium">View All</a>
						</div>
					</div>
				</div>
			</div>
			<!-- /Invoice -->

			<!-- Expenses -->
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card revenue-widget flex-fill">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
							<div>
								<h4 class="mb-1">₹8,980,097</h4>
								<p>Platform Expenses</p>
							</div>
							<span class="revenue-icon bg-orange-transparent text-orange">
								<i class="ti ti-lifebuoy fs-16"></i>
							</span>
						</div>
						<div class="d-flex align-items-center justify-content-between">
							<p class="mb-0"><span class="fs-13 fw-bold text-success">+41%</span> vs Last Month</p>
							<a href="expense-list.html" class="text-decoration-underline fs-13 fw-medium">View All</a>
						</div>
					</div>
				</div>
			</div>
			<!-- /Expenses -->

			<!-- Returns -->
			<div class="col-xl-3 col-sm-6 col-12 d-flex">
				<div class="card revenue-widget flex-fill">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
							<div>
								<h4 class="mb-1">₹78,458,798</h4>
								<p>Total Refunds Processed</p>
							</div>
							<span class="revenue-icon bg-indigo-transparent text-indigo">
								<i class="ti ti-hash fs-16"></i>
							</span>
						</div>
						<div class="d-flex align-items-center justify-content-between">
							<p class="mb-0"><span class="fs-13 fw-bold text-danger">-20%</span> vs Last Month</p>
							<a href="sales-report.html" class="text-decoration-underline fs-13 fw-medium">View All</a>
						</div>
					</div>
				</div>
			</div>
			<!-- /Returns -->

		</div>

		<div class="row">

			<!-- Sales & Purchase -->
			<div class="col-xxl-8 col-xl-7 col-sm-12 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-primary fs-16 me-2"><i class="ti ti-shopping-cart"></i></span>
							<h5 class="card-title mb-0">Revenue & Orders</h5>
						</div>
						<ul class="nav btn-group custom-btn-group">
							<a class="btn btn-outline-light" href="javascript:void(0);">1D</a>
							<a class="btn btn-outline-light" href="javascript:void(0);">1W</a>
							<a class="btn btn-outline-light" href="javascript:void(0);">1M</a>
							<a class="btn btn-outline-light" href="javascript:void(0);">3M</a>
							<a class="btn btn-outline-light" href="javascript:void(0);">6M</a>
							<a class="btn btn-outline-light active" href="javascript:void(0);">1Y</a>
						</ul>
					</div>
					<div class="card-body pb-0">
						<div>
							<div class="d-flex align-items-center gap-2">
								<div class="border p-2 br-8">
									<p class="d-inline-flex align-items-center mb-1"><i class="ti ti-circle-filled fs-8 text-primary-300 me-1"></i>Total Orders</p>
									<h4>{{ $totalOrders }}</h4>
								</div>
								<div class="border p-2 br-8">
									<p class="d-inline-flex align-items-center mb-1"><i class="ti ti-circle-filled fs-8 text-primary me-1"></i>Total Revenue</p>
									<h4>₹{{ number_format($totalRevenue, 0) }}</h4>
								</div>
							</div>
							<div id="sales-daychart"></div>
						</div>
					</div>
				</div>
			</div>
			<!-- /Sales & Purchase -->

			<!-- Top Selling Products -->
			<div class="col-xxl-4 col-xl-5 d-flex">
				<div class="card flex-fill">
					<div class="card-header">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-info fs-16 me-2"><i class="ti ti-info-circle"></i></span>
							<h5 class="card-title mb-0">Platform Overview</h5>
						</div>
					</div>
					<div class="card-body">
						<div class="row g-3">
							<div class="col-md-4">
								<div class="info-item border bg-light p-3 text-center">
									<div class="mb-2 text-info fs-24">
										<i class="ti ti-user-check"></i>
									</div>
									<p class="mb-1">Hotel Clients</p>
									<h5>{{ $activeClients }}</h5>
								</div>
							</div>
							<div class="col-md-4">
								<div class="info-item border bg-light p-3 text-center">
									<div class="mb-2 text-orange fs-24">
										<i class="ti ti-users"></i>
									</div>
									<p class="mb-1">Restaurant Staff</p>
									<h5>{{ $totalEmployees }}</h5>
								</div>
							</div>
							<div class="col-md-4">
								<div class="info-item border bg-light p-3 text-center">
									<div class="mb-2 text-teal fs-24">
										<i class="ti ti-shopping-cart"></i>
									</div>
									<p class="mb-1">Active KOTs</p>
									<h5>{{ $activeOrders }}</h5>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer pb-sm-0">
						<div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
							<h5>Clients Overview</h5>
							<div class="dropdown dropdown-wraper">
								<a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white"  data-bs-toggle="dropdown" aria-expanded="false">
									<i class="ti ti-calendar me-1"></i>Today
								</a>
								<ul class="dropdown-menu p-3">
									<li>
										<a href="javascript:void(0);" class="dropdown-item">Today</a>
									</li>
									<li>
										<a href="javascript:void(0);" class="dropdown-item">Weekly</a>
									</li>
									<li>
										<a href="javascript:void(0);" class="dropdown-item">Monthly</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="row align-items-center">
							<div class="col-sm-5">
								<div id="customer-chart"></div>
							</div>
							<div class="col-sm-7">
								<div class="row gx-0">
									<div class="col-sm-6">
										<div class="text-center border-end">
											<h2 class="mb-1">5.5K</h2>
											<p class="text-orange mb-2">New Clients</p>
											<span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-arrow-up-left me-1"></i>25%</span>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="text-center">
											<h2 class="mb-1">3.5K</h2>
											<p class="text-teal mb-2">Retained Clients</p>
											<span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-arrow-up-left me-1"></i>21%</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">

			<!-- Top Selling Products -->
			<div class="col-xxl-4 col-md-6 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-pink fs-16 me-2"><i class="ti ti-box"></i></span>
							<h5 class="card-title mb-0">Top Performing Hotels</h5>
						</div>
						<div class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="ti ti-calendar me-1"></i>Today
							</a>
							<ul class="dropdown-menu p-3">
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Today</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Weekly</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Monthly</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="card-body sell-product">
						@forelse($topClients as $client)
						<div class="d-flex align-items-center justify-content-between {{ $loop->last ? '' : 'border-bottom' }}">
							<div class="d-flex align-items-center">
								<a href="{{ route('clients.edit', $client->client_id) }}" class="avatar avatar-lg">
									@if($client->image)
										<img src="{{ config('app.url') }}/storage/app/public/{{ $client->image }}" alt="img" style="object-fit:cover;width:100%;height:100%">
									@else
										<img src="{{ $base_url }}/assets/img/products/product-01.jpg" alt="img">
									@endif
								</a>
								<div class="ms-2">
									<h6 class="fw-bold mb-1"><a href="{{ route('clients.edit', $client->client_id) }}">{{ $client->client_name }}</a></h6>
									<div class="d-flex align-items-center item-list">
										<p>₹{{ number_format($client->total_revenue, 2) }}</p>
										<p>{{ $client->order_count }} Orders</p>
									</div>
								</div>
							</div>
							<span class="badge bg-outline-success badge-xs d-inline-flex align-items-center"><i class="ti ti-arrow-up-left me-1"></i>Top</span>
						</div>
						@empty
						<p class="text-muted text-center py-3">No client data yet.</p>
						@endforelse
					</div>
				</div>
			</div>
			<!-- /Top Selling Products -->

			<!-- Low Stock Products -->
			<div class="col-xxl-4 col-md-6 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-danger fs-16 me-2"><i class="ti ti-alert-triangle"></i></span>
							<h5 class="card-title mb-0">Expiring Subscriptions</h5>
						</div>								
						<a href="low-stocks.html" class="fs-13 fw-medium text-decoration-underline">View All</a>
					</div>
					<div class="card-body">
						@forelse($expiringClients as $ec)
						@php $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($ec->subscription_end_date)); @endphp
						<div class="d-flex align-items-center justify-content-between {{ $loop->last ? 'mb-0' : 'mb-4' }}">
							<div class="d-flex align-items-center">
								<a href="{{ route('clients.edit', $ec->client_id) }}" class="avatar avatar-lg">
									@if($ec->image)
										<img src="{{ config('app.url') }}/storage/app/public/{{ $ec->image }}" alt="img" style="object-fit:cover;width:100%;height:100%">
									@else
										<img src="{{ $base_url }}/assets/img/products/product-06.jpg" alt="img">
									@endif
								</a>
								<div class="ms-2">
									<h6 class="fw-bold mb-1"><a href="{{ route('clients.edit', $ec->client_id) }}">{{ $ec->client_name }}</a></h6>
									<p class="fs-13">ID : #{{ $ec->client_id }}</p>
								</div>
							</div>
							<div class="text-end">
								<p class="fs-13 mb-1">Expires In</p>
								<h6 class="text-orange fw-medium">{{ $daysLeft }} days</h6>
							</div>
						</div>
						@empty
						<p class="text-muted text-center py-3">No subscriptions expiring soon.</p>
						@endforelse
					</div>
				</div>
			</div>
			<!-- /Low Stock Products -->

			<!-- Recent Sales -->
			<div class="col-xxl-4 col-md-12 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-pink fs-16 me-2"><i class="ti ti-box"></i></span>
							<h5 class="card-title mb-0">Recent Hotel Orders</h5>
						</div>
						<div class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white"  data-bs-toggle="dropdown" aria-expanded="false">
								<i class="ti ti-calendar me-1"></i>Weekly
							</a>
							<ul class="dropdown-menu p-3">
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Today</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Weekly</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Monthly</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="card-body">
						@php
						$orderStatusBadge = [
							'pending'   => 'bg-warning text-dark',
							'confirmed' => 'bg-primary',
							'served'    => 'bg-success',
							'cancelled' => 'bg-danger',
						];
						@endphp
						@forelse($recentOrders as $order)
						<div class="d-flex align-items-center justify-content-between {{ $loop->last ? 'mb-0' : 'mb-4' }}">
							<div class="d-flex align-items-center">
								<a href="javascript:void(0);" class="avatar avatar-lg">
									@if($order->client_image)
										<img src="{{ config('app.url') }}/storage/app/public/{{ $order->client_image }}" alt="img" style="object-fit:cover;width:100%;height:100%">
									@else
										<img src="{{ $base_url }}/assets/img/products/product-11.jpg" alt="img">
									@endif
								</a>
								<div class="ms-2">
									<h6 class="fw-bold mb-1"><a href="javascript:void(0);">{{ $order->client_name ?? 'Unknown' }}</a></h6>
									<div class="d-flex align-items-center item-list">
										<p>{{ $order->order_type === 'dine_in' ? 'Dine In' : 'Takeaway' }}</p>
										<p class="text-gray-9">₹{{ number_format($order->total_amount, 2) }}</p>
									</div>
									@php $items = $menuNamesByOrder->get($order->order_id); @endphp
									@if($items && $items->count())
									<p class="text-muted fs-12 mb-0">{{ $items->take(2)->pluck('menu_name')->implode(', ') }}{{ $items->count() > 2 ? ' +'.($items->count()-2).' more' : '' }}</p>
									@endif
								</div>
							</div>
							<div class="text-end">
								<p class="fs-13 mb-1">{{ $order->created_at->format('d M Y') }}</p>
								<span class="badge badge-xs d-inline-flex align-items-center {{ $orderStatusBadge[$order->status] ?? 'bg-secondary' }}">
									<i class="ti ti-circle-filled fs-5 me-1"></i>{{ ucfirst($order->status) }}
								</span>
							</div>
						</div>
						@empty
						<p class="text-muted text-center py-3">No recent orders.</p>
						@endforelse
					</div>
				</div>
			</div>
			<!-- /Recent Sales -->

		</div>

		<div class="row">

			<!-- Sales Statics -->
			<div class="col-xl-6 col-sm-12 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-danger fs-16 me-2"><i class="ti ti-alert-triangle"></i></span>
							<h5 class="card-title mb-0">Revenue Statistics</h5>
						</div>
						<div class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white"  data-bs-toggle="dropdown" aria-expanded="false">
								<i class="ti ti-calendar me-1"></i>2025
							</a>
							<ul class="dropdown-menu p-3">
								<li>
									<a href="javascript:void(0);" class="dropdown-item">2025</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">2022</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">2021</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="card-body pb-0">
						<div class="d-flex align-items-center flex-wrap gap-2">
							<div class="border p-2 br-8">
								<h5 class="d-inline-flex align-items-center text-teal">₹{{ number_format($todayRevenue, 2) }}</h5>
								<p>Today's Revenue</p>
							</div>
							<div class="border p-2 br-8">
								<h5 class="d-inline-flex align-items-center text-orange">₹{{ number_format($totalRevenue, 2) }}</h5>
								<p>Total Revenue</p>
							</div>
						</div>
						<div id="sales-statistics"></div>
					</div>
				</div>
			</div>
			<!-- /Sales Statics -->

			<!-- Recent Transactions -->
			<div class="col-xl-6 col-sm-12 col-12 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-orange fs-16 me-2"><i class="ti ti-flag"></i></span>
							<h5 class="card-title mb-0">Recent Hotel Transactions</h5>
						</div>
						<a href="online-orders.html" class="fs-13 fw-medium text-decoration-underline">View All</a>
					</div>
					<div class="card-body p-0">
						<ul class="nav nav-tabs nav-justified transaction-tab">
							<li class="nav-item"><a class="nav-link active" href="#sale" data-bs-toggle="tab">Hotel Orders</a></li>
							<li class="nav-item"><a class="nav-link" href="#purchase-transaction" data-bs-toggle="tab">Subscriptions</a></li>
							<li class="nav-item"><a class="nav-link" href="#quotation" data-bs-toggle="tab">Proposals</a></li>
							<li class="nav-item"><a class="nav-link" href="#expenses" data-bs-toggle="tab">Expenses</a></li>
							<li class="nav-item"><a class="nav-link" href="#invoices" data-bs-toggle="tab">Invoices</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane show active" id="sale">
								<div class="table-responsive">
									<table class="table table-borderless custom-table">
										<thead class="thead-light">
											<tr>
												<th>Date</th>
												<th>Hotel Client</th>
												<th>Status</th>
												<th>Total</th>
											</tr>
										</thead>
										<tbody>
											@foreach($recentOrders as $order)
											<tr>
												<td>{{ $order->created_at->format('d M Y') }}</td>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															@if($order->client_image)
																<img src="{{ config('app.url') }}/storage/app/public/{{ $order->client_image }}" class="img-fluid" alt="img" style="object-fit:cover;width:100%;height:100%">
															@else
																<img src="{{ $base_url }}/assets/img/customer/customer16.jpg" class="img-fluid" alt="img">
															@endif
														</a>
														<div class="ms-2">
															<h6><a href="javascript:void(0);" class="fw-bold">{{ $order->client_name ?? 'Unknown' }}</a></h6>
															<span class="fs-13 text-orange">#{{ $order->order_number }}</span>
															@php $items = $menuNamesByOrder->get($order->order_id); @endphp
															@if($items && $items->count())
															<p class="text-muted fs-12 mb-0">{{ $items->take(2)->pluck('menu_name')->implode(', ') }}{{ $items->count() > 2 ? ' +'.($items->count()-2).' more' : '' }}</p>
															@endif
														</div>
													</div>
												</td>
												<td><span class="badge badge-xs d-inline-flex align-items-center {{ $orderStatusBadge[$order->status] ?? 'bg-secondary' }}"><i class="ti ti-circle-filled fs-5 me-1"></i>{{ ucfirst($order->status) }}</span></td>
												<td class="fs-16 fw-bold text-gray-9">₹{{ number_format($order->total_amount, 2) }}</td>
											</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane fade" id="purchase-transaction">
								<div class="table-responsive">
									<table class="table table-borderless custom-table">
										<thead class="thead-light">
											<tr>
												<th>Date</th>
												<th>Hotel / Vendor</th>
												<th>Status</th>
												<th>Total</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>24 May 2025</td>
												<td>
													<a href="javascript:void(0);" class="fw-semibold">Grand Hyatt Mumbai</a>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Completed</span></td>
												<td class="text-gray-9">₹1000</td>
											</tr>
											<tr>
												<td>23 May 2025</td>
												<td>
													<a href="javascript:void(0);" class="fw-semibold">Marriott Pune</a>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Completed</span></td>
												<td class="text-gray-9">₹1500</td>
											</tr>
											<tr>
												<td>22 May 2025</td>
												<td>
													<a href="javascript:void(0);" class="fw-semibold">Hilton Bangalore</a>
												</td>
												<td><span class="badge badge-cyan badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Pending</span></td>
												<td class="text-gray-9">₹2000</td>
											</tr>
											<tr>
												<td>21 May 2025</td>
												<td>
													<a href="javascript:void(0);" class="fw-semibold">Taj Hotels Delhi</a>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Completed</span></td>
												<td class="text-gray-9">₹1200</td>
											</tr>
											<tr>
												<td>21 May 2025</td>
												<td>
													<a href="javascript:void(0);" class="fw-semibold">Radisson Blu Goa</a>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Completed</span></td>
												<td class="text-gray-9">₹1300</td>
											</tr>
											<tr>
												<td>28 May 2025</td>
												<td>
													<a href="javascript:void(0);" class="fw-semibold">Novotel Chennai</a>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Completed</span></td>
												<td class="text-gray-9">₹1600</td>
											</tr>
											<tr>
												<td>26 May 2025</td>
												<td>
													<a href="javascript:void(0);" class="fw-semibold">OYO Premium Jaipur</a>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Completed</span></td>
												<td class="text-gray-9">₹1100</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane" id="quotation">
								<div class="table-responsive">
									<table class="table table-borderless custom-table">
										<thead class="thead-light">
											<tr>
												<th>Date</th>
												<th>Hotel Client</th>
												<th>Status</th>
												<th>Total</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>24 May 2025</td>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer16.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-medium"><a href="javascript:void(0);">Andrea Willer</a></h6>
															<span class="fs-13 text-orange">#114589</span>
														</div>
													</div>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Sent</span></td>
												<td class="text-gray-9">₹4,560</td>
											</tr>
											<tr>
												<td>23 May 2025</td>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer17.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-medium"><a href="javascript:void(0);">Timothy Sandsr</a></h6>
															<span class="fs-13 text-orange">#114589</span>
														</div>
													</div>
												</td>
												<td><span class="badge badge-warning badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Ordered</span></td>
												<td class="text-gray-9">₹3,569</td>
											</tr>
											<tr>
												<td>22 May 2025</td>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer18.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-medium"><a href="javascript:void(0);">Bonnie Rodrigues</a></h6>
															<span class="fs-13 text-orange">#114589</span>
														</div>
													</div>
												</td>
												<td><span class="badge badge-cyan badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Pending</span></td>
												<td class="text-gray-9">₹4,560</td>
											</tr>
											<tr>
												<td>21 May 2025</td>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer15.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-medium"><a href="javascript:void(0);">Randy McCree</a></h6>
															<span class="fs-13 text-orange">#114589</span>
														</div>
													</div>
												</td>
												<td><span class="badge badge-warning badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Ordered</span></td>
												<td class="text-gray-9">₹2,155</td>
											</tr>
											<tr>
												<td>21 May 2025</td>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer13.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-medium"><a href="javascript:void(0);">Dennis Anderson</a></h6>
															<span class="fs-13 text-orange">#114589</span>
														</div>
													</div>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Sent</span></td>
												<td class="text-gray-9">₹5,123</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane fade" id="expenses">
								<div class="table-responsive">
									<table class="table table-borderless custom-table">
										<thead class="thead-light">
											<tr>
												<th>Date</th>
												<th>Expenses</th>
												<th>Status</th>
												<th>Total</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>24 May 2025</td>
												<td>
													<h6 class="fw-medium"><a href="javascript:void(0);">Electricity Payment</a></h6>
													<span class="fs-13 text-orange">#EX849</span>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Approved</span></td>
												<td class="text-gray-9">₹200</td>
											</tr>
											<tr>
												<td>23 May 2025</td>
												<td>
													<h6 class="fw-medium"><a href="javascript:void(0);">Electricity Payment</a></h6>
													<span class="fs-13 text-orange">#EX849</span>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Approved</span></td>
												<td class="text-gray-9">₹200</td>
											</tr>
											<tr>
												<td>22 May 2025</td>
												<td>
													<h6 class="fw-medium"><a href="javascript:void(0);">Stationery Purchase</a></h6>
													<span class="fs-13 text-orange">#EX848</span>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Approved</span></td>
												<td class="text-gray-9">₹50</td>
											</tr>
											<tr>
												<td>21 May 2025</td>
												<td>
													<h6 class="fw-medium"><a href="javascript:void(0);">AC Repair Service</a></h6>
													<span class="fs-13 text-orange">#EX847</span>
												</td>
												<td><span class="badge badge-cyan badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Pending</span></td>
												<td class="text-gray-9">₹800</td>
											</tr>
											<tr>
												<td>21 May 2025</td>
												<td>
													<h6 class="fw-medium"><a href="javascript:void(0);">Client Meeting</a></h6>
													<span class="fs-13 text-orange">#EX846</span>
												</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Approved</span></td>
												<td class="text-gray-9">₹100</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane" id="invoices">
								<div class="table-responsive">
									<table class="table table-borderless custom-table">
										<thead class="thead-light">
											<tr>
												<th>Customer</th>
												<th>Due Date</th>
												<th>Status</th>
												<th>Amount</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer16.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-bold"><a href="javascript:void(0);">Andrea Willer</a></h6>
															<span class="fs-13 text-orange">#INV005</span>
														</div>
													</div>
												</td>
												<td>24 May 2025</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Paid</span></td>
												<td class="text-gray-9">₹1300</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer17.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-bold"><a href="javascript:void(0);">Timothy Sandsr</a></h6>
															<span class="fs-13 text-orange">#INV004</span>
														</div>
													</div>
												</td>
												<td>23 May 2025</td>
												<td><span class="badge badge-warning badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Overdue</span></td>
												<td class="text-gray-9">₹1250</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer18.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-bold"><a href="javascript:void(0);">Bonnie Rodrigues</a></h6>
															<span class="fs-13 text-orange">#INV003</span>
														</div>
													</div>
												</td>
												<td>22 May 2025</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Paid</span></td>
												<td class="text-gray-9">₹1700</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer15.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-bold"><a href="javascript:void(0);">Randy McCree</a></h6>
															<span class="fs-13 text-orange">#INV002</span>
														</div>
													</div>
												</td>
												<td>21 May 2025</td>
												<td><span class="badge badge-danger badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Unpaid</span></td>
												<td class="text-gray-9">₹1500</td>
											</tr>
											<tr>
												<td>
													<div class="d-flex align-items-center file-name-icon">
														<a href="javascript:void(0);" class="avatar avatar-md">
															<img src="assets/img/customer/customer13.jpg" class="img-fluid" alt="img">
														</a>
														<div class="ms-2">
															<h6 class="fw-bold"><a href="javascript:void(0);">Dennis Anderson</a></h6>
															<span class="fs-13 text-orange">#INV001</span>
														</div>
													</div>
												</td>
												<td>21 May 2025</td>
												<td><span class="badge badge-success badge-xs d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-5 me-1"></i>Paid</span></td>
												<td class="text-gray-9">₹1000</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>						
					</div>
				</div>
			</div>
			<!-- /Recent Transactions -->

		</div>

		<div class="row">

			<!-- Top Customers -->
			<div class="col-xxl-4 col-md-6 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-orange fs-16 me-2"><i class="ti ti-users"></i></span>
							<h5 class="card-title mb-0">Top Hotel Clients</h5>
						</div>								
						<a href="{{ route('clients.index') }}" class="fs-13 fw-medium text-decoration-underline">View All</a>
					</div>
					<div class="card-body">
						@forelse($topClients as $client)
						<div class="d-flex align-items-center justify-content-between {{ $loop->last ? ''  : 'border-bottom mb-3 pb-3' }} flex-wrap gap-2">
							<div class="d-flex align-items-center">
								<a href="{{ route('clients.edit', $client->client_id) }}" class="avatar avatar-lg flex-shrink-0">
									@if($client->image)
										<img src="{{ config('app.url') }}/storage/app/public/{{ $client->image }}" alt="img" style="object-fit:cover;width:100%;height:100%">
									@else
										<img src="{{ $base_url }}/assets/img/customer/customer11.jpg" alt="img">
									@endif
								</a>
								<div class="ms-2">
									<h6 class="fs-14 fw-bold mb-1"><a href="{{ route('clients.edit', $client->client_id) }}">{{ $client->client_name }}</a></h6>
									<div class="d-flex align-items-center item-list">
										<p>{{ $client->order_count }} Orders</p>
									</div>
								</div>
							</div>
							<div class="text-end">
								<h5>₹{{ number_format($client->total_revenue, 2) }}</h5>
							</div>
						</div>
						@empty
						<p class="text-muted text-center py-3">No client data yet.</p>
						@endforelse
					</div>
				</div>
			</div>	
			<!-- /Top Customers -->

			<!-- Top Categories -->
			<div class="col-xxl-4 col-md-6 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-orange fs-16 me-2"><i class="ti ti-users"></i></span>
							<h5 class="card-title mb-0">Top Hotel Services</h5>
						</div>
						<div class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="ti ti-calendar me-1"></i>Weekly
							</a>
							<ul class="dropdown-menu p-3">
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Today</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Weekly</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Monthly</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between flex-wrap gap-4 mb-4">
							<div>
								<canvas id="top-category" height="230" width="200"></canvas>
							</div>
							<div>
								<div class="category-item category-primary">
									<p class="fs-13 mb-1">Room Service</p>
									<h2 class="d-flex align-items-center">698<span class="fs-13 fw-normal text-default ms-1">Orders</span></h2>
								</div>
								<div class="category-item category-orange">
									<p class="fs-13 mb-1">Restaurant</p>
									<h2 class="d-flex align-items-center">545<span class="fs-13 fw-normal text-default ms-1">Orders</span></h2>
								</div>
								<div class="category-item category-secondary">
									<p class="fs-13 mb-1">Banquet</p>
									<h2 class="d-flex align-items-center">456<span class="fs-13 fw-normal text-default ms-1">Orders</span></h2>
								</div>
							</div>
						</div>
						<h6 class="mb-2">Service Statistics</h6>
						<div class="border br-8">
							<div class="d-flex align-items-center justify-content-between border-bottom p-2">
								<p class="d-inline-flex align-items-center mb-0"><i class="ti ti-square-rounded-filled text-indigo fs-8 me-2"></i>Total Number Of Orders</p>
								<h5>{{ $totalOrders }}</h5>
							</div>
							<div class="d-flex align-items-center justify-content-between p-2">
								<p class="d-inline-flex align-items-center mb-0"><i class="ti ti-square-rounded-filled text-orange fs-8 me-2"></i>Total Number Of Menu Items</p>
								<h5>{{ $totalMenuItems }}</h5>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /Top Categories -->

			<!-- Order Statistics -->
			<div class="col-xxl-4 col-md-12 d-flex">
				<div class="card flex-fill">
					<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
						<div class="d-inline-flex align-items-center">
							<span class="title-icon bg-soft-indigo fs-16 me-2"><i class="ti ti-package"></i></span>
							<h5 class="card-title mb-0">Order Statistics</h5>
						</div>
						<div class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle btn btn-sm btn-white" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="ti ti-calendar me-1"></i>Weekly
							</a>
							<ul class="dropdown-menu p-3">
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Today</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Weekly</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="dropdown-item">Monthly</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="card-body pb-0">
						<div id="heat_chart"></div>
					</div>
				</div>
			</div>
			<!-- /Order Statistics -->

		</div>
		

			<script>
			window.dashboardChartLabels  = @json($chartLabels);
			window.dashboardChartOrders  = @json($chartOrders);
			window.dashboardChartRevenue = @json($chartRevenue);
			</script>
			</div>
@endsection
