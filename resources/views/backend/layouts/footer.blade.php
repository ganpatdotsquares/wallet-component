<footer class="footer">
            © 2019 Eliteadmin by themedesigner.in
        </footer>
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
    </div>
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
    
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!--morris JavaScript -->
    <script src="{{ asset('backend/assets/theme/js/raphael-min.js') }}"></script>
    <script src="{{ asset('backend/assets/theme/js/morris.min.js') }}"></script>
    <script src="{{ asset('backend/assets/theme/js/jquery.sparkline.min.js') }}"></script>
    <!-- Popup message jquery -->
    <script src="{{ asset('backend/assets/theme/js/jquery.toast.js') }}"></script>
    <!-- Chart JS -->
    <script src="{{ asset('backend/assets/theme/js/dashboard1.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="{{ asset('backend/assets/theme/js/custom.js') }}"></script>

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
        });
    </script>
    
    @stack('custom-script')
</body>
</html>