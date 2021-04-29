<!DOCTYPE html>
<html lang="en">
    @include('dashboard.company.partials.head')
    <body>
        @include('dashboard.company.partials.header')
        <div class="content-wrapper">
            <div class="container" style="position:relative;">
                @yield('content')
            </div><!-- /.container -->
        </div><!-- /.content-wrapper -->
        @include('dashboard.company.include.footer')
        @yield('customScript')
    </body>
</html>
