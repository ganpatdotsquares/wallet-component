<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/assets/theme/images/favicon.png') }}">
    <title>{{ config('GET.system_application_name') }}</title>
    <!-- This page CSS -->
    <link href="{{ asset('backend/assets/theme/css/login-register-lock.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/theme/css/morris.css') }}" rel="stylesheet">
    <!--Toaster Popup message CSS -->
    <link href="{{ asset('backend/assets/theme/css/jquery.toast.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('backend/assets/theme/css/style.min.css') }}" rel="stylesheet">
    <!-- Dashboard 1 Page CSS -->
    <link href="{{ asset('backend/assets/theme/css/dashboard1.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/theme/css/custom-developer.css') }}" rel="stylesheet">

</head> 

<body class="skin-default card-no-border">
    <section id="wrapper">
        @yield('content')
    </section>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{ asset('backend/assets/theme/js/jquery-3.2.1.min.js') }}"></script>
    <!-- Bootstrap popper Core JavaScript -->
    <script src="{{ asset('backend/assets/theme/js/popper.min.js') }}"></script>
    <script src="{{ asset('backend/assets/theme/js/bootstrap.min.js') }}"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('backend/assets/theme/js/perfect-scrollbar.jquery.min.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('backend/assets/theme/js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('backend/assets/theme/js/sidebarmenu.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('backend/assets/theme/js/custom.min.js') }}"></script>
    <!-- Popup message jquery -->
    <script src="{{ asset('backend/assets/theme/js/jquery.toast.js') }}"></script>
    <!-- jQuery Validation -->
    <script src="{{ asset('backend/assets/theme/js/jquery.validate.min.js') }}"></script>
    <!-- Custom JavaScript -->
    <script src="{{ asset('backend/assets/theme/js/custom.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-password').forEach(function(icon) {
                icon.addEventListener('click', function() {
                    var target = document.querySelector(this.getAttribute('data-target'));
                    if (target.type === 'password') {
                        target.type = 'text';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    } else {
                        target.type = 'password';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    }
                });
            });
        });

        @if (session('status'))
            $.toast({
                heading: 'Success',
                text: '{{ session('status') }}',
                position: 'top-right',
                loaderBg: '#00c292',
                icon: 'success',
                hideAfter: 3500,
                stack: 6
            });
        @endif

        @if (session('error'))
            $.toast({
                heading: 'Error',
                text: '{{ session('error') }}',
                position: 'top-right',
                loaderBg: '#e46a76',
                icon: 'error',
                hideAfter: 3500,
                stack: 6
            });
        @endif

        @if (session('info'))
            $.toast({
                heading: 'Info',
                text: '{{ session('info') }}',
                position: 'top-right',
                loaderBg: '#03a9f3',
                icon: 'info',
                hideAfter: 3500,
                stack: 6
            });
        @endif
    </script>
    @stack('custom-script')
</body>
</html>