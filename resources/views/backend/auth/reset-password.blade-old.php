@extends('backend.layouts.auth')

@section('content')
<div class="auth-box">
    <div id="loginform1">
        <div class="logo">
            <span class="db">
                <img class="mb-3" src="{{ asset('backend/assets/images/logo.png') }}" alt="logo" style="width: 64%" />
            </span>
            <h5 class="font-medium m-b-20">Reset your Account Password</h5>
        </div>
        
        <!-- Form -->
        <div class="row">
            <div class="col-12">
            
                {{ Form::open(array('route' => 'admin.password.update', 'method' => 'POST', 'class' => "form-horizontal m-t-20", 'id' => "loginform")) }}
                    <input type="hidden" name="role" value="admin">
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                        </div>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email!=='' ? $email :  old('email') }}" required readonly>
                        
                    </div>
                        @error('email')
                            <div class="invalid-error error" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                        <br/>

                    <div class="form-group">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" placeholder="New Password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                    </div>
                    
                    <div class="form-group text-center">
                        <div class="col-xs-12 p-b-20">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input class="btn btn-block btn-lg btn-info" id="login-btn" type="submit" > 
                            <!-- <button class="btn btn-block btn-lg btn-info" id="login-btn-disable" type="submit">Reset Password</button> -->
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('per_page_script')
    <script>
        $('#loginform').validate({
            rules: {
                "password": {required: true,minlength: 8,maxlength: 50},
                "password_confirmation": {required: true, equalTo:"#password" }
            },
            messages: {
                "password": {
                    required: "Please enter password.",
                    minlength: "Password must be at least {0} characters long.",
                    maxlength: "Password should be maximum {0} characters."
                },
                "password_confirmation": {
                    required: "Please enter confirm password.",
                    equalTo:"Password and confirm password did not match"
                }
            },
            submitHandler: function(form) {
                $("#login-btn").attr('disabled' , true);
                form.submit();
            }
        })
        // $('input[type="submit"]').on('blur' , function () {
        //     if ($("#loginform").valid()) {
        //         $(this).removeAttr('disabled');                
        //         console.log('sdfdg');
        //     }
        // })

    </script>
@endsection