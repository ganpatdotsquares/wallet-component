@extends('backend.layouts.app')
@section('title', 'Profile')
@section('content')
<style>
    .position-relative {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #888;
    }
</style>

<div class="container-fluid">

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Profile</h4>
        </div>
        <div class="col-md-7 align-self-center text-right">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-xlg-9 col-md-7" style="margin-top:2%">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link @if(request()->input('tab') == 1 || !request()->has('tab')) active @endif" data-toggle="tab" href="#profile" role="tab">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->input('tab') == 2) active @endif" data-toggle="tab" href="#settings" role="tab">Change Password</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Profile tab -->
                <div class="tab-pane @if(request()->input('tab') == 1|| request()->input('tab') != 2) active @endif" id="profile" role="tabpanel">
                    <div class="card-body">
                        <form id="update-profile-form" class="form-horizontal form-material" method="POST" enctype="multipart/form-data" action="{{ route('admin.update_profile')}}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="col-md-12">Full Name<span class="danger" style="color:red">*</span></label>
                                <div class="col-md-12">
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Johnathan Doe" class="form-control form-control-line">
                                    @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <input type="hidden" name="tab" value="1">
                            <div class="form-group">
                                <label for="example-email" class="col-md-12">Email<span class="danger" style="color:red">*</span></label>
                                <div class="col-md-12">
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="johnathan@admin.com" class="form-control form-control-line" id="example-email" readonly>
                                    @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Phone No</label>
                                <div class="col-md-12">
                                    <input type="text" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" placeholder="123 456 7890" class="form-control form-control-line">
                                    @error('mobile_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('profile_image') ? 'is-invalid' : '' }}">
                                <label for="parent">Profile Image</label>
                                <input type="file" id="profile_image" name="profile_image" class="form-control" />
                                @if ($errors->has('profile_image'))
                                <label id="mobile-error" class="error">{{ $errors->first('profile_image') }}</label>
                                @endif
                                @if (isset($user->profile_image))
                                <br>
                                <div>
                                    <td><img src="{{ asset('backend/images/profile/' . $user->profile_image) }}"
                                            alt="" height="80px">
                                    </td>
                                </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button class="btn btn-success" type="submit">Update Profile</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password tab -->
                <div class="tab-pane @if(request()->input('tab') == 2) active @endif" id="settings" role="tabpanel">
                    <div class="card-body">
                        <form id="change-password-form" class="form-horizontal form-material" method="POST" action="{{ route('admin.change_password') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="col-md-12">Current Password<span class="danger" style="color:red">*</span></label>
                                <div class="col-md-12 position-relative">
                                    <input type="password" id="currentpassword" name="currentpassword" placeholder="Current Password" class="form-control form-control-line">
                                    <i class="fa fa-eye-slash toggle-password" data-target="#currentpassword"></i>
                                    @error('currentpassword')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">New Password<span class="danger" style="color:red">*</span></label>
                                <div class="col-md-12 position-relative">
                                    <input type="password" id="newpassword" name="newpassword" placeholder="New Password" class="form-control form-control-line">
                                    <i class="fa fa-eye-slash toggle-password" data-target="#newpassword"></i>
                                    @error('newpassword')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Confirm New Password<span class="danger" style="color:red">*</span></label>
                                <div class="col-md-12 position-relative">
                                    <input type="password" id="newpassword_confirmation" name="newpassword_confirmation" placeholder="Confirm New Password" class="form-control form-control-line">
                                    <i class="fa fa-eye-slash toggle-password" data-target="#newpassword_confirmation"></i>
                                    @error('newpassword_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <input type="hidden" name="tab" value="2">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button class="btn btn-success" type="submit">Change Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Add password regex validator
        $.validator.addMethod("strongPassword", function (value, element) {
            return this.optional(element) || 
                /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/.test(value);
        }, "Password must be at least 6 characters long and include 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.");

        // Add email regex validator
        $.validator.addMethod("customEmail", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,63}$/.test(value);
        }, "Please enter a valid email address.");

        // Define custom validation methods if needed
        $.validator.addMethod("noOnlySpaces", function(value, element) {
            return $.trim(value).length > 0;
        }, "Spaces are not allowed.");

        // Custom validator for file size
        $.validator.addMethod('filesize', function(value, element, param) {
            if (element.files.length === 0) {
                return true;
            }
            return element.files[0].size <= param;
        }, 'File size must be less than 2MB.');

        // Initialize jQuery Validation for the profile update form
        $('#update-profile-form').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50
                },
                email: {
                    required: true,
                    email: true,
                    customEmail: true
                },
                mobile_number: {
                    digits: true,
                    minlength: 7,
                    maxlength: 15
                },
                profile_image: {
                    extension: "jpg|jpeg|png|gif",
                    filesize: 2097152 // 2MB in bytes
                }
            },
            messages: {
                name: {
                    required: "Please enter your full name.",
                    minlength: "Please enter at least 3 characters.",
                    maxlength: "Please do not enter more than 50 characters."
                },
                email: {
                    required: "Please enter your email address.",
                    email: "Please enter a valid email address."
                },
                mobile_number: {
                    required: "Please enter your contact number.",
                    digits: "Please enter only numbers.",
                    minlength: "Please enter at least 7 digits.",
                    maxlength: "Please do not enter more than 15 digits."
                },
                profile_image: {
                    extension: "Please upload a file in one of the following formats: jpg, jpeg, png, gif.",
                    filesize: "File size must be less than 2MB."
                }
            },
            errorElement: 'error',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                $(form).find('button[type="submit"]').attr("disabled", true);
                // Form is valid, submit the form
                form.submit();
            }
        });

        // Initialize jQuery Validation for the password change form
        $('#change-password-form').validate({
            rules: {
                currentpassword: {
                    required: true,
                    minlength: 6
                },
                newpassword: {
                    required: true,
                    minlength: 6,
                    strongPassword: true
                },
                newpassword_confirmation: {
                    required: true,
                    equalTo: "#newpassword"
                }
            },
            messages: {
                currentpassword: {
                    required: "Please enter your current password.",
                    minlength: "Your password must be at least 6 characters long."
                },
                newpassword: {
                    required: "Please enter password.",
                    minlength: "Password must be at least {0} characters long.",
                    maxlength: "Password should be maximum {0} characters."
                },
                newpassword_confirmation: {
                    required: "Please enter confirm password.",
                    equalTo:"Password and confirm password did not match"
                }
            },
            errorElement: 'error',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                $(form).find('button[type="submit"]').attr("disabled", true);
                // Form is valid, submit the form
                form.submit();
            }
        });
    });
</script>
@endpush
