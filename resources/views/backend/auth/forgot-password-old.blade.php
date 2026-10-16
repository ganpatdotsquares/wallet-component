@extends('backend.layouts.auth')

@section('content')
<div class="auth-box">
    <div id="loginform1">
        <div class="logo">
            <span class="db">
            @if (config('GET.main_logo'))
                <img class="mb-3" src="{{ asset('assets/photo/' . config('GET.main_logo')) }}" alt="logo" style="width: 64%">
                
            @else
            <img src="{{ asset('backend/assets/images/dots.png') }}" alt="homepage" class="light-logo" />
            @endif

                <img class="mb-3" src="{{ asset('backend/assets/images/logo.png') }}" alt="logo" style="width: 64%" />
            </span>
            <h5 class="font-medium m-b-20">Forgot Password ?</h5>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-12">
            
                {{ Form::open(array('route' => 'admin.forgot-password', 'method' => 'POST', 'class' => "form-horizontal m-t-20", 'id' => "loginform")) }}
                    <input type="hidden" name="role" value="admin">
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                        </div>
                        {{ Form::email('email', old('email') , array('placeholder' => 'Email', 'class' => "form-control form-control-lg", 'aria-label' => "Email", 'aria-describedby' => "basic-addon1", "id" => "admin-login-form")) }}
                        
                    </div>
                    <label id="admin-login-form-error" class="error" for="admin-login-form"></label>
                        @error('email')
                            <div class="invalid-error error" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                        <br/>
                    
                    <div class="form-group text-center">
                        <div class="col-xs-12 p-b-20">
                            <button class="btn btn-block btn-lg btn-info" id="login-btn" type="submit">Send Password Reset Link</button>
                            <a  class="btn btn-block btn-lg btn-warning" href="{{route('admin.login')}}">Back</a>
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
        $.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Please enter valid mail."
        );
        $('#loginform').validate({
                    rules: {
                        "email": {required: true, email:true , regex:"^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$"},
                    },
                    messages: {
                        "email": {
                            required: "Please enter email.",
                            email: 'Please enter valid email'
                        }
                    },
                    submitHandler: (form) => {
                        $('#form-submit').attr("disabled", true);
                        form.submit();
                    }
                });

</script>
@endsection