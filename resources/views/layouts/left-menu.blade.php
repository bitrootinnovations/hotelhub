<div class="sidebar" id="sidebar">
	<!-- Logo -->
	<div class="sidebar-logo active">
		<a href="{{ route('dashboard') }}" class="logo logo-normal">
			<img src="{{ $base_url }}/assets/img/logo.svg" alt="Img">
		</a>
		<a href="{{ route('dashboard') }}" class="logo logo-white">
			<img src="{{ $base_url }}/assets/img/logo-white.svg" alt="Img">
		</a>
		<a href="{{ route('dashboard') }}" class="logo-small">
			<img src="{{ $base_url }}/assets/img/logo-small.png" alt="Img">
		</a>
		<a id="toggle_btn" href="javascript:void(0);">
			<i data-feather="chevrons-left" class="feather-16"></i>
		</a>
	</div>
	<!-- /Logo -->
	<div class="modern-profile p-3 pb-0">
		<div class="text-center rounded bg-light p-3 mb-4 user-profile">
			<div class="avatar avatar-lg online mb-3">
				<img src="{{ $base_url }}/assets/img/customer/customer15.jpg" alt="Img" class="img-fluid rounded-circle">
			</div>
			<h6 class="fs-14 fw-bold mb-1">{{ Auth::user()->name ?? 'User' }}</h6>
			<p class="fs-12 mb-0">{{ Auth::user()->role->role_name ?? '' }}</p>
		</div>
	</div>
	<div class="sidebar-header p-3 pb-0 pt-2">
		<div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
			<div class="avatar avatar-md onlin">
				<img src="{{ $base_url }}/assets/img/customer/customer15.jpg" alt="Img" class="img-fluid rounded-circle">
			</div>
			<div class="text-start sidebar-profile-info ms-2">
				<h6 class="fs-14 fw-bold mb-1">{{ Auth::user()->name ?? 'User' }}</h6>
				<p class="fs-12">{{ Auth::user()->role->role_name ?? '' }}</p>
			</div>
		</div>
	</div>
	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">
			<ul>
				<!-- Main — admin only -->
				@if(!$isClientUser && ($isAdmin || ($userPermissions->has('Dashboard') && $userPermissions->get('Dashboard')->can_view)))
				<li class="submenu-open">
					<h6 class="submenu-hdr">Main</h6>
					<ul>
						<li>
							<a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
								<i class="ti ti-layout-grid fs-16 me-2"></i><span>Dashboard</span>
							</a>
						</li>
					</ul>
				</li>
				@endif

				<!-- Restaurant Management — admin/non-client only -->
				@php
					$canViewCategories = !$isClientUser && ($isAdmin || ($userPermissions->has('Categories')        && $userPermissions->get('Categories')->can_view));
					$canViewTableTypes = !$isClientUser && ($isAdmin || ($userPermissions->has('Table Type Master')  && $userPermissions->get('Table Type Master')->can_view));
					$canViewTables     = !$isClientUser && ($isAdmin || ($userPermissions->has('Table Master')       && $userPermissions->get('Table Master')->can_view));
					$canViewMenus      = !$isClientUser && ($isAdmin || ($userPermissions->has('Menu Master')        && $userPermissions->get('Menu Master')->can_view));
				@endphp
				@if($canViewCategories || $canViewTableTypes || $canViewTables || $canViewMenus)
				<li class="submenu-open">
					<h6 class="submenu-hdr">Restaurant Management</h6>
					<ul>
						@if($canViewCategories)
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('categories*') ? 'subdrop active' : '' }}">
								<i class="ti ti-category fs-16 me-2"></i><span>Category Master</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.index') ? 'active' : '' }}">All Categories</a></li>
								@if($isAdmin || ($userPermissions->has('Categories') && $userPermissions->get('Categories')->can_add))
								<li><a href="{{ route('categories.create') }}" class="{{ request()->routeIs('categories.create') ? 'active' : '' }}">Add Category</a></li>
								@endif
							</ul>
						</li>
						@endif
						@if($canViewTableTypes)
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('table-types*') ? 'subdrop active' : '' }}">
								<i class="ti ti-layout-rows fs-16 me-2"></i><span>Table Type Master</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('table-types.index') }}" class="{{ request()->routeIs('table-types.index') ? 'active' : '' }}">All Table Types</a></li>
								@if($isAdmin || ($userPermissions->has('Table Type Master') && $userPermissions->get('Table Type Master')->can_add))
								<li><a href="{{ route('table-types.create') }}" class="{{ request()->routeIs('table-types.create') ? 'active' : '' }}">Add Table Type</a></li>
								@endif
							</ul>
						</li>
						@endif
						@if($canViewTables)
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('seating*') ? 'subdrop active' : '' }}">
								<i class="ti ti-table fs-16 me-2"></i><span>Table Master</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('tables.index') }}" class="{{ request()->routeIs('tables.index') ? 'active' : '' }}">All Tables</a></li>
								@if($isAdmin || ($userPermissions->has('Table Master') && $userPermissions->get('Table Master')->can_add))
								<li><a href="{{ route('tables.create') }}" class="{{ request()->routeIs('tables.create') ? 'active' : '' }}">Add Table</a></li>
								@endif
							</ul>
						</li>
						@endif
						@if($canViewMenus)
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('menus*') ? 'subdrop active' : '' }}">
								<i class="ti ti-books fs-16 me-2"></i><span>Menu Master</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('menus.index') }}" class="{{ request()->routeIs('menus.index') ? 'active' : '' }}">All Menu Items</a></li>
								@if($isAdmin || ($userPermissions->has('Menu Master') && $userPermissions->get('Menu Master')->can_add))
								<li><a href="{{ route('menus.create') }}" class="{{ request()->routeIs('menus.create') ? 'active' : '' }}">Add Menu Item</a></li>
								@endif
							</ul>
						</li>
						@endif
					</ul>
				</li>
				@endif

				<!-- Client Management — admin only -->
				@if(!$isClientUser && ($isAdmin || ($userPermissions->has('Client Registration') && $userPermissions->get('Client Registration')->can_view)))
				<li class="submenu-open">
					<h6 class="submenu-hdr">Client Management</h6>
					<ul>
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('clients*') ? 'subdrop active' : '' }}">
								<i class="ti ti-building-store fs-16 me-2"></i><span>Client Registration</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.index') ? 'active' : '' }}">All Clients</a></li>
								@if($isAdmin || ($userPermissions->has('Client Registration') && $userPermissions->get('Client Registration')->can_add))
								<li><a href="{{ route('clients.create') }}" class="{{ request()->routeIs('clients.create') ? 'active' : '' }}">Add Client</a></li>
								@endif
							</ul>
						</li>
					</ul>
				</li>
				@endif

				<!-- Employee Management — admin only -->
				@if(!$isClientUser && ($isAdmin || ($userPermissions->has('Employees') && $userPermissions->get('Employees')->can_view)))
				<li class="submenu-open">
					<h6 class="submenu-hdr">Employee Management</h6>
					<ul>
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('employees*') ? 'subdrop active' : '' }}">
								<i class="ti ti-users fs-16 me-2"></i><span>Employee Master</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.index') ? 'active' : '' }}">All Employees</a></li>
								@if($isAdmin || ($userPermissions->has('Employees') && $userPermissions->get('Employees')->can_add))
								<li><a href="{{ route('employees.create') }}" class="{{ request()->routeIs('employees.create') ? 'active' : '' }}">Add Employee</a></li>
								@endif
							</ul>
						</li>
					</ul>
				</li>
				@endif

				<!-- Client Portal -->
				@if($isClientUser)
				<li class="submenu-open">
					<h6 class="submenu-hdr">Client Portal</h6>
					<ul>
						<li>
							<a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
								<i class="ti ti-layout-grid fs-16 me-2"></i><span>Dashboard</span>
							</a>
						</li>
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('categories*') ? 'subdrop active' : '' }}">
								<i class="ti ti-category fs-16 me-2"></i><span>Categories</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.index') ? 'active' : '' }}">All Categories</a></li>
								<li><a href="{{ route('categories.create') }}" class="{{ request()->routeIs('categories.create') ? 'active' : '' }}">Add Category</a></li>
							</ul>
						</li>
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('menus*') ? 'subdrop active' : '' }}">
								<i class="ti ti-books fs-16 me-2"></i><span>Menu Master</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('menus.index') }}" class="{{ request()->routeIs('menus.index') ? 'active' : '' }}">All Menu Items</a></li>
								<li><a href="{{ route('menus.create') }}" class="{{ request()->routeIs('menus.create') ? 'active' : '' }}">Add Menu Item</a></li>
							</ul>
						</li>
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('client/employees*') ? 'subdrop active' : '' }}">
								<i class="ti ti-users fs-16 me-2"></i><span>Employees</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('client.employees.index') }}" class="{{ request()->routeIs('client.employees.index') ? 'active' : '' }}">All Employees</a></li>
								<li><a href="{{ route('client.employees.create') }}" class="{{ request()->routeIs('client.employees.create') ? 'active' : '' }}">Add Employee</a></li>
							</ul>
						</li>
						<li>
							<a href="{{ route('reports.orders') }}" class="{{ request()->is('reports/orders*') ? 'active' : '' }}">
								<i class="ti ti-file-text fs-16 me-2"></i><span>Order Report</span>
							</a>
						</li>
						<li>
							<a href="{{ route('client.menu-report.index') }}" class="{{ request()->routeIs('client.menu-report.*') ? 'active' : '' }}">
								<i class="ti ti-chart-bar fs-16 me-2"></i><span>Menu-wise Report</span>
							</a>
						</li>
					</ul>
				</li>
				@endif

				<!-- Reports — admin only (client sees reports in Client Portal) -->
				@if(!$isClientUser && ($isAdmin || ($userPermissions->has('Reports') && $userPermissions->get('Reports')->can_view)))
				<li class="submenu-open">
					<h6 class="submenu-hdr">Reports</h6>
					<ul>
						<li>
							<a href="{{ route('reports.orders') }}" class="{{ request()->is('reports/orders*') ? 'active' : '' }}">
								<i class="ti ti-report-analytics fs-16 me-2"></i><span>Order Report</span>
							</a>
						</li>
					</ul>
				</li>
				@endif

				<!-- System -->
				@if($isAdmin)
				<li class="submenu-open">
					<h6 class="submenu-hdr">System</h6>
					<ul>
						<li>
							<a href="{{ route('printers.index') }}" class="{{ request()->is('printers*') ? 'active' : '' }}">
								<i class="ti ti-printer fs-16 me-2"></i><span>Printer Status</span>
							</a>
						</li>
						<li>
							<a href="{{ route('settings.upi') }}" class="{{ request()->is('settings*') ? 'active' : '' }}">
								<i class="ti ti-qrcode fs-16 me-2"></i><span>UPI Settings</span>
							</a>
						</li>
					</ul>
				</li>
				@endif

				<!-- User Management — admin only -->
				@php
					$canViewRoles       = !$isClientUser && ($isAdmin || ($userPermissions->has('Role Master')        && $userPermissions->get('Role Master')->can_view));
					$canViewPermissions = !$isClientUser && ($isAdmin || ($userPermissions->has('Permission Master')  && $userPermissions->get('Permission Master')->can_view));
				@endphp
				@if($canViewRoles || $canViewPermissions)
				<li class="submenu-open">
					<h6 class="submenu-hdr">User Management</h6>
					<ul>
						@if($canViewRoles)
						<li class="submenu">
							<a href="javascript:void(0);" class="{{ request()->is('roles*') ? 'subdrop active' : '' }}">
								<i class="ti ti-user-shield fs-16 me-2"></i><span>Role Master</span><span class="menu-arrow"></span>
							</a>
							<ul>
								<li><a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.index') ? 'active' : '' }}">All Roles</a></li>
								@if($isAdmin || ($userPermissions->has('Role Master') && $userPermissions->get('Role Master')->can_add))
								<li><a href="{{ route('roles.create') }}" class="{{ request()->routeIs('roles.create') ? 'active' : '' }}">Add Role</a></li>
								@endif
							</ul>
						</li>
						@endif
						@if($canViewPermissions)
						<li>
							<a href="{{ route('permissions.index') }}" class="{{ request()->is('permissions*') ? 'active' : '' }}">
								<i class="ti ti-shield-check fs-16 me-2"></i><span>Permission Master</span>
							</a>
						</li>
						@endif
					</ul>
				</li>
				@endif
			</ul>
		</div>
	</div>
</div>
