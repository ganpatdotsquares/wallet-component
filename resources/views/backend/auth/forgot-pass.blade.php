@extends('backend.layouts.auth')
@section('content')
<section id="wrapper" class="login-register" style="background-image: url('{{ asset('backend/assets/images/background/login-register.jpg') }}');">
    <div class="login-box card">
        <div class="card-body">
            <div class="auth-box">
                <div id="loginform">
                    <div class="logo" style="text-align: center;">
                        <span class="db">
                            @if (config('GET.main_logo'))
                            <img class="mb-3" src="{{ asset('assets/photo/' . config('GET.main_logo')) }}" alt="logo" style="width:50%">
                            @else
                            <img src="{{ asset('backend/assets/images/dots.png') }}" alt="homepage" />
                            @endif
                        </span>
                    </div>
                    <h5 class="font-medium m-b-20 text-center">Forgot Password</h5>

                    <!-- Form -->
                    <div class="row">
                        <div class="col-12">
                            <form id="reset-pwd-form" method="POST" action="{{ route('admin.send_reset_mail') }}">
                                @csrf
                                <input type="hidden" name="role" value="admin">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="form-control form-control-lg" aria-label="Email" aria-describedby="basic-addon1">
                                </div>
                                @if (isset($errors) && $errors->has('email'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </div>
                                @endif
                                
                                <div class="form-group text-center">
                                    <div class="col-xs-6 p-b-20">
                                        <button id="reset-pwd-button" class="btn btn-block btn-lg btn-info " type="submit">Reset Password </button>
                                    </div>
                                </div>
                                <a href="{{route('admin.login')}}" id="to-recover1" class=" text-dark float-right"> <i class="fa fa-arrow-left m-r-5"></i>Back to Login</a>
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

    // Add email regex validator
    $.validator.addMethod("customEmail", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,63}$/.test(value);
    }, "Please enter a valid email address.");

    $("#reset-pwd-form").validate({
        rules: {
            email: {
                required: true,
                email: true,
                customEmail: true
            },
        },
        messages: {
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address."
            },
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