@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Category Product</h4>
                <h6>Category new product</h6>
            </div>
        </div>
        <ul class="table-top-head">
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh"><i class="ti ti-refresh"></i></a>
            </li>
            <li>
                <a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i class="ti ti-chevron-up"></i></a>
            </li>
        </ul>
        <div class="page-btn mt-0">
            <a href="product-list.html" class="btn btn-secondary"><i data-feather="arrow-left" class="me-2"></i>Back to Category</a>
        </div>
    </div>
    <form method="POST" action="{{$id}}" class="add-product-form" enctype='multipart/form-data'>
        @csrf
        <div class="add-product">
            <div class="accordions-items-seperate" id="accordionSpacingExample">
                <div class="accordion-item border mb-4">
                    <h2 class="accordion-header" id="headingSpacingOne">
                        <div class="accordion-button collapsed bg-white" data-bs-toggle="collapse" data-bs-target="#SpacingOne" aria-expanded="true" aria-controls="SpacingOne">
                            <div class="d-flex align-items-center justify-content-between flex-fill">
                            <h5 class="d-flex align-items-center"><i data-feather="info" class="text-primary me-2"></i><span>Category Information</span></h5>
                            </div>
                        </div>
                    </h2>
                    <div id="SpacingOne" class="accordion-collapse collapse show" aria-labelledby="headingSpacingOne">
                        <div class="accordion-body border-top">
                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Category Name<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="category_name" value="{{ old('category_name', $category->category_name) }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Category Type<span class="text-danger ms-1" >*</span></label>
                                        <select class="select" name="category_type">
                                            <option value="">Select</option>
                                            <option value="Maharashtrian">Maharashtrian</option>
                                            <option value="South Indian">South Indian</option>
                                            <option value="North Indian">North Indian</option>
                                            <option value="Gujarati">Gujarati</option>
                                            <option value="Punjabi">Punjabi</option>
                                            <option value="Bengali">Bengali</option>
                                            <option value="Rajasthani">Rajasthani</option>
                                            <option value="Kashmiri">Kashmiri</option>
                                            <option value="Goan">Goan</option>
                                            <option value="Kerala">Kerala</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item border mb-4">
                    <h2 class="accordion-header" id="headingSpacingThree">
                        <div class="accordion-button collapsed bg-white" data-bs-toggle="collapse" data-bs-target="#SpacingThree" aria-expanded="true" aria-controls="SpacingThree">
                            <div class="d-flex align-items-center justify-content-between flex-fill">
                            <h5 class="d-flex align-items-center"><i data-feather="image" class="text-primary me-2"></i><span>Category Image</span></h5>
                            </div>
                        </div>
                    </h2>
                    <div id="SpacingThree" class="accordion-collapse collapse show" aria-labelledby="headingSpacingThree">
                        <div class="accordion-body border-top">
                            <div class="text-editor add-list add">
                                <div class="col-lg-12">
                                    <div class="add-choosen">
                                        <div class="mb-3">
                                            <div class="image-upload image-upload-two">
                                                <input type="file" id="imageInput" multiple accept="image/*" name="image">
                                                <div class="image-uploads">
                                                    <i data-feather="plus-circle" class="plus-down-add me-0"></i>
                                                    <h4>Add Images</h4>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="imagePreviewContainer" class="phone-img-container">
                                            <!-- Selected images will be displayed here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="d-flex align-items-center justify-content-end mb-4">
                <button type="submit" class="btn btn-primary">Upadate Category</button>
            </div>
        </div>
    </form>
</div>
<script>
    document.getElementById('imageInput').addEventListener('change', function(event) {
        let container = document.getElementById('imagePreviewContainer');
        container.innerHTML = ''; // Clear previous images

        Array.from(event.target.files).forEach(file => {
            if (file && file.type.startsWith('image/')) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let imgWrapper = document.createElement('div');
                    imgWrapper.classList.add('phone-img');
                    
                    let img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Uploaded Image';

                    let removeBtn = document.createElement('a');
                    removeBtn.href = 'javascript:void(0);';
                    removeBtn.innerHTML = '<i data-feather="x" class="x-square-add remove-product"></i>';
                    removeBtn.onclick = function() {
                        imgWrapper.remove(); // Remove image on click
                    };

                    imgWrapper.appendChild(img);
                    imgWrapper.appendChild(removeBtn);
                    container.appendChild(imgWrapper);
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endsection