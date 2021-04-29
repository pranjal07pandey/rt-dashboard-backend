<!DOCTYPE html>
<html>
<head>
@include('dashboard.admin.include.header')

    <!-- Full Width Column -->
    <div class="content-wrapper">
        <div class="container">
           @yield('content')
        </div>
        <!-- /.container -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="container">
            <div class="pull-right hidden-xs">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2017 <a href="#">Record Time</a>.</strong> All rights
            reserved.
        </div>
        <!-- /.container -->
    </footer>
</div>
<!-- ./wrapper -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="{{ asset('assets/dashboard/js/jquery-3.1.1.js') }}"></script>


    <!-- Include all compiled plugins (below), or include individual files as needed -->
    {{--<script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap.min.js') }}"></script>--}}

    {{--<script src="{{ asset('assets/dashboard/js/drag.js') }}"></script>--}}
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- jQuery 2.2.0 -->
{{--<script src="{{ asset('assets/dashboard/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>--}}
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('assets/dashboard/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('assets/dashboard/plugins/fastclick/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/dashboard/dist/js/app.min.js') }}"></script>
<!-- AdminLTE zipFiledemo purposes -->
<script src="{{ asset('zipFile') }}"></script>
    <script type="text/javascript">
        $( function() {
            $( "#sortable" ).sortable();
            $( "#sortable" ).disableSelection();
        } );
    </script>
@yield('customScript')
</body>
</html>
