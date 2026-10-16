@extends('backend.layouts.auth')
@section('content')

<div class="login-register" style="background-image: url('{{ asset('backend/assets/theme/images/login-register.jpg') }}');">
    <div class="login-box card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <!-- Display flash messages if any -->

                    <div class="logo" style="text-align: center;">
                        <span class="db">
                            @if (config('GET.main_logo'))
                            <img class="mb-3" src="{{ asset('assets/photo/' . config('GET.main_logo')) }}" alt="logo" style="width:50%">
                            @else
                            <img src="{{ asset('backend/assets/theme/images/dots.png') }}" alt="homepage" />
                            @endif
                        </span>
                    </div>
                    @if (Session::has('admin-login-error'))
                    <div class="alert alert-danger">
                        {{ Session::get('admin-login-error') }}
                    </div>
                    @endif
                    <form action="{{ route('admin.login.post') }}" method="POST" class="form-horizontal m-t-20 admin-login-form" id="loginform" novalidate>
                        @csrf
                        <input type="hidden" name="role" value="admin">
                        @php
                        $login_email = $login_pass = $is_remember = "";
                        if(isset($_COOKIE['admin_login_email']) && isset($_COOKIE['admin_login_pass']))
                        {
                        $login_email = $_COOKIE['admin_login_email'];
                        $login_pass = $_COOKIE['admin_login_pass'];
                        $is_remember = "checked='checked'";
                        }
                        @endphp
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                </div>
                                <input type="email" name="email" value="{{ isset($login_email) ?  $login_email : old('email') }}" class="form-control form-control-lg" placeholder="Email" aria-label="Email" id="login_email" aria-describedby="basic-addon1">
                            </div>
                            @if (isset($errors) && $errors->has('email'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
                                </div>
                                <input type="password" name="password" value="{{ isset($login_pass) ?  $login_pass : old('password') }}" class="form-control form-control-lg" placeholder="Password" aria-label="Password" aria-describedby="basic-addon2" id="password">
                                <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-eye-slash toggle-password" data-target="#password" style="font-size: 18px; color: #007bff;"></i></span>
                                </div>
                            </div>
                            @if (isset($errors) && $errors->has('password'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </div>
                            @endif
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="custom-control custom-checkbox">
                                    <input class="form-check-input" type="checkbox" {{$is_remember}} name="remember" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember Me
                                    </label>
                                    <a href="{{ route('admin.forgot_password') }}" id="to-recover1" class="text-dark float-right"><i class="fa fa-lock m-r-5"></i> Forgot password?</a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <div class="col-xs-12 p-b-20">
                                <button class="btn btn-block btn-lg btn-info" type="submit">Log In</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
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

    $("#loginform").validate({
        rules: {
            email: {
                required: true,
                email: true,
                customEmail: true
            },
            password: {
                required: true,
                minlength: 6,
                strongPassword: true
            }
        },
        messages: {
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address."
            },
            password: {
                required: "Please enter your password.",
                minlength: "Your password must be at least 6 characters long."
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