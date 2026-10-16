@extends('backend.layouts.auth')

@section('content')
<section id="wrapper" class="login-register" style="background-image: url('{{ asset('backend/assets/images/background/login-register.jpg') }}');">
    <div class="login-box 1234 card" style="width:500px;margin-top:-100px">
        <div class="card-body">
            <div class="auth-box">
                <div id="loginform">
                    <div class="logo" style="text-align: center;">
                        <span class="db">
                            @if (config('GET.main_logo'))
                            <img class="mb-3" src="{{ asset('assets/photo/' . config('GET.main_logo')) }}" alt="logo" style="width:30%">
                            @else
                            <img src="{{ asset('backend/assets/images/dots.png') }}" alt="homepage" />
                            @endif
                        </span>
                    </div>
                    <h5 class="font-medium m-b-20 text-center">Reset Password</h5>
                    @if(Session::has('error'))
                    <div class="alert alert-danger" role="alert">
                        <strong>Error - </strong> {{ Session::get('error') }}
                    </div>
                    @endif
                    @if(Session::has('status'))
                    <div class="alert alert-success" role="alert">
                        <strong>Success - </strong> {{ Session::get('status') }}
                    </div>
                    @endif

                    <!-- Form -->
                    <div class="row">
                        <div class="col-12">
                            <form method="POST" action="{{ route('admin.password.change') }}" id="change-pwd-form" name="change-pwd-form">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">

                                <!-- Email Input -->
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="form-control form-control-lg" aria-label="Email" aria-describedby="basic-addon1">
                                </div>
                                @error('email')
                                <div class="invalid-error" style="color:red;" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                                <label id="email-error" class="error" style="color: red;" for="email"></label>

                                <!-- Password Input -->
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="ti-lock"></i></span>
                                    </div>
                                    <input type="password" id="password" name="password" value="{{ old('password') }}" placeholder="Password" class="form-control form-control-lg" aria-label="password" aria-describedby="basic-addon1">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-eye-slash toggle-password" data-target="#password" style="font-size: 18px; color: #007bff;"></i></span>
                                    </div>
                                </div>
                                @error('password')
                                <div class="invalid-error" style="color:red;" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                                <label id="password-error" class="error" style="color: red;" for="password"></label>

                                <!-- Confirm Password Input -->
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="ti-lock"></i></span>
                                    </div>
                                    <input type="password" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="Confirm Password" class="form-control form-control-lg" aria-label="password_confirmation" aria-describedby="basic-addon1">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-eye-slash toggle-password" data-target="#password_confirmation" style="font-size: 18px; color: #007bff;"></i></span>
                                    </div>
                                </div>
                                @error('password_confirmation')
                                <div class="invalid-error" style="color:red;" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                                @enderror
                                <label id="password_confirmation-error" class="error" style="color: red;" for="password_confirmation"></label>

                                <!-- Submit Button -->
                                <div class="form-group text-center">
                                    <div class="col-xs-6 p-b-20">
                                        <button id="change-pwd-button" type="submit" class="btn btn-block btn-lg btn-info">Reset Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('custom-script')
<script>

    // Add password regex validator
    $.validator.addMethod("strongPassword", function (value, element) {
        return this.optional(element) || 
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/.test(value);
    }, "Password must be at least 6 characters long and include 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.");

    // Add email regex validator
    $.validator.addMethod("customEmail", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,63}$/.test(value);
    }, "Please enter a valid email address.");

    $("#change-pwd-form").validate({
        rules: {
            email: {
                required: true,
                email: true,
                customEmail: true
            },
            password: {
                required: true,
                minlength: 8,
                strongPassword: true
            },
            password_confirmation: {
                required: true,
                equalTo: "#password"
            }
        },
        messages: {
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address."
            },
            password: {
                required: "Please enter password.",
                minlength: "Password must be at least {0} characters long.",
                maxlength: "Password should be maximum {0} characters."
            },
            password_confirmation: {
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
        }
    });
</script>
@endpush