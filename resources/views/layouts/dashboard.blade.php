@include('dashboard.admin.include.header')
<div class="contentWrapper">
    <div class="container">
        @yield('content')
    </div>
</div>

@include('dashboard.admin.include.footer')
@yield('customScript')

</body>
</html>
