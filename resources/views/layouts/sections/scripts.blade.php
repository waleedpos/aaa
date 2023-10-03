<!-- BEGIN: Vendor JS-->

<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>
@yield('vendor-script')
@stack('pricing-script')
@yield('page-script')
