
@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>Category Master</h4>
                <h6>Manage Your Categories</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a id="custom-excel-export" data-bs-toggle="tooltip" data-bs-placement="top" title="Export to Excel">
                    <img src="{{ $base_url }}/assets/img/icons/excel.svg" alt="Excel Icon">
                </a>
            </li>
        </ul>
        <div class="page-btn">
            <a href="add" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add Category</a>
        </div>
    </div>
    
    <!-- /product list -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <div class="search-set">
                <div class="search-input">
                    <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                </div>
            </div>
            <!-- <div class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        Customer
                    </a>
                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Carl Evans</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Minerva Rameriz</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Robert Lamon</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Patricia Lewis</a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        Staus
                    </a>
                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Completed</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Pending</a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        Payment Status
                    </a>
                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Paid</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Unpaid</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Overdue</a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        Sort By : Last 7 Days
                    </a>
                    <ul class="dropdown-menu  dropdown-menu-end p-3">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Recently Added</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Ascending</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Desending</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Last Month</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">Last 7 Days</a>
                        </li>
                    </ul>
                </div>
            </div> -->
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="footer-search" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Category ID</th>
                            <th>Image</th>
                            <th>Category Name</th>
                            <th>Category Type</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="sales-list">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /product list -->
</div>
<script src="{{ $base_url }}/assets/js/jquery-3.7.1.min.js"></script>

<script src="{{ $base_url }}/assets/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    $(document).ready(function () {
        // Check if DataTable is already initialized
        if ($.fn.DataTable.isDataTable('#footer-search')) {
            $('#footer-search').DataTable().destroy(); // Destroy existing instance
            $('#footer-search').empty(); // Optional: Clear table to avoid duplicate rendering
        }

        $('#footer-search').DataTable({
            
            "ajax": {
                "url": "allData", // Replace with your actual API URL
                "type": "GET"
            },

            "columns": [
                { "data": "category_id" },
                { 
                    "data": "image",
                    "render": function(data) {
                        return data ? `<img src="{{$base_url}}/storage/app/${data}" class="avatar avatar-md">` : 
                                    `<img src="assets/img/default-category.png" class="avatar avatar-md">`;
                    }
                },
                { "data": "category_name" },
                { "data": "category_type" },
                { 
                    "data": "status_id",
                    "render": function(data, type, row) {
                        let statusClass = data == 1 ? 'bg-success' : 'bg-danger';
                        let statusText = data == 1 ? 'Active' : 'Inactive';
                        return `<span class="badge ${statusClass} toggle-status" data-id="${row.category_id}" data-status="${data}">${statusText}</span>`;
                    }
                },
                {
                    mData: "created_at",
                    mRender: function(data, type, row) {
                        var string = row.created_at;
                        var date = new Date(string);
                        return date.toLocaleString();
                    }
                },
                { 
                    "data": null,
                    "orderable": false,
                    "render": function(data, type, row) {
                        return `
                            <div class="edit-delete-action">
                                <a class="me-2 border rounded d-flex align-items-center p-2" href="${row.category_id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                            </div>
                        `;
                    }
                }
            ],
            "bFilter": true,
            "sDom": 'fBtlpi',
            "ordering": true,
            "language": {
                search: ' ',
                sLengthMenu: '_MENU_',
                searchPlaceholder: "Search",
                sLengthMenu: 'Row Per Page _MENU_ Entries',
                info: "_START_ - _END_ of _TOTAL_ items",
                paginate: {
                    next: ' <i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>'
                }
            },
            "initComplete": function(settings, json) {
                $('.dataTables_filter').appendTo('#tableSearch');
                $('.dataTables_filter').appendTo('.search-input');
            }
        });
        
    });
    $(document).on('click', '.toggle-status', function() {
        let statusElement = $(this);
        let categoryId = statusElement.data('id');
        let currentStatus = statusElement.data('status');

        let newStatus = currentStatus == 1 ? 0 : 1; // Toggle status

        $.ajax({
            url: "update-category-status",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token in headers
            },
            data: {
                category_id: categoryId,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    let updatedClass = newStatus == 1 ? 'bg-success' : 'bg-danger';
                    let updatedText = newStatus == 1 ? 'Active' : 'Inactive';

                    statusElement.removeClass('bg-success bg-danger').addClass(updatedClass).text(updatedText);
                    statusElement.data('status', newStatus);
                } else {
                    alert("Failed to update status.");
                }
            },
            error: function() {
                alert("Error updating status.");
            }
        });
    });

    document.getElementById("custom-excel-export").addEventListener("click", function () {
        // Get the table element
        var table = document.getElementById("footer-search");

        // Convert the table to a worksheet
        var workbook = XLSX.utils.table_to_book(table, { sheet: "Sales Report" });

        // Export the workbook to an Excel file
        XLSX.writeFile(workbook, "Category-report.xlsx");
    });
</script>
@endsection