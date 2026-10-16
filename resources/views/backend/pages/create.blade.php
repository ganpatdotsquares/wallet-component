@extends('backend.layouts.app')
@section('title', 'CMS Page')
@section('content')

<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">Create CMS Page</h4>
    </div>
    <div class="col-md-7 align-self-center text-right">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item"><a href="{{ route('admin.page') }}">CMS Manager</a>
                </li>
                <li class="breadcrumb-item active">Create Page</li>
            </ol>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <form action="{{ route('admin.page.store') }}" method="POST" enctype="multipart/form-data" id="form">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="title">Title <span style="color: red;font-size:18px;"><b>*</b></span></label>
                                    <input type="text" name="title" id="title" class="form-control stop_first_space" placeholder="Enter Title" value="{{ old('name') }}" pattern="^[a-zA-Z_ ]*$">
                                    @error('name')
                                    <p style="color:red;">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="slug">Slug <span style="color: red;font-size:18px;"><b>*</b></span></label>
                                    <input type="text" name="slug" id="slug" class="form-control stop_first_space" placeholder="Enter Slug" value="{{ old('slug') }}">
                                    @error('slug')
                                    <p style="color:red;">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="short_description">Short Description <span style="color: red;font-size:18px;"><b>*</b></span></label>
                                    <textarea name="short_description" id="short_description" class="form-control" placeholder="Enter Short Description">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                    <p style="color:red;">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="description">Description <span style="color: red;font-size:18px;"><b>*</b></span></label>
                                    <textarea name="description" id="description" class="form-control" placeholder="Enter Description">{{ old('description') }}</textarea>
                                    @error('description')
                                    <p style="color:red;">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mr-sm-2 m-b-15">
                                        <input type="checkbox" class="custom-control-input" name="status" id="checkbox0" value="1">
                                        <label class="custom-control-label" for="checkbox0">Is Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <button type="submit" id="submitButton" class="btn btn-primary">Submit</button>
                        <a href="{{ route('admin.page') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom-script')
<script>
    $(document).ready(function() {
        CKEDITOR.replace("description");
        // Adding custom method for CKEditor validation
        $.validator.addMethod("noOnlySpaces", function(value, element) {
            return $.trim(value).length > 0;
        }, "Spaces are not allowed.");
        $.validator.addMethod("ckeditorRequired", function(value, element) {
            // Get the content of the CKEditor instance
            var data = CKEDITOR.instances['description'].getData();
            // Remove HTML tags and check if the content is not empty
            return $.trim(data.replace(/<[^>]*>/g, '')) !== '';
        }, "Description is required.");

        $('#form').validate({
            rules: {
                title: {
                    required: true,
                    maxlength: 100,
                    noOnlySpaces: true, // Ensure you have this method defined
                },
                short_description: {
                    required: true,
                    maxlength: 1000,
                    noOnlySpaces: true,
                },
                meta_description: {
                    required: true,
                    maxlength: 1000,
                    noOnlySpaces: true,
                },
                slug: {
                    required: true,
                    maxlength: 100,
                },
                description: {
                    required: true,
                    ckeditorRequired: true, // Custom CKEditor validation
                },
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });

        $("#form").on('submit', function(e) {
            // Disable the submit button to prevent multiple submissions
            $("#submitButton").attr("disabled", true);

            // Validate the CKEditor content
            var messageLength = CKEDITOR.instances['description'].getData().replace(/<[^>]*>/gi, '').length;
            if (!messageLength) {
                e.preventDefault();
                $("#submitButton").attr("disabled", false);
            }
        });
    });
</script>
@endpush

@stop