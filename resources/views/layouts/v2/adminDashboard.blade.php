<!DOCTYPE html>
<html>
    <head>
        <title>Record Time</title>
        @include('dashboard.v2.admin.include.head')
    </head>
    <body>
        @include('dashboard.v2.admin.include.sidebar')
        <div class="content-wrapper">
            @include('dashboard.v2.admin.include.navigation')


            <div class="content">
                @yield('content')

                {{--<div class="cardView">--}}
                    {{--<div class="cardHeader">--}}
                        {{--<strong class="float-left">All Companies</strong>--}}
                        {{--<a href="https://oms.dev/dashboard/su/company/create" class="btn btn-primary float-right btn-sm">Add New</a>--}}
                        {{--<div class="clearfix"></div>--}}
                    {{--</div>--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-md-12">--}}
                            {{--<!--Table-->--}}
                            {{--<table class="table">--}}

                                {{--<!--Table head-->--}}
                                {{--<thead class="blue-grey lighten-4">--}}
                                {{--<tr>--}}
                                    {{--<th scope="col">ID</th>--}}
                                    {{--<th scope="col">Name</th>--}}
                                    {{--<th scope="col">Address</th>--}}
                                    {{--<th scope="col">Phone</th>--}}
                                    {{--<th scope="col">Created At</th>--}}
                                    {{--<th scope="col">Action</th>--}}
                                {{--</tr>--}}
                                {{--</thead>--}}
                                {{--<!--Table head-->--}}

                                {{--<!--Table body-->--}}
                                {{--<tbody>--}}
                                {{--<tr>--}}
                                    {{--<td colspan="6" class="text-center tableEmpty"><svg class="svg-inline--fa fa-box-open fa-w-20" aria-hidden="true" data-prefix="fas" data-icon="box-open" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg=""><path fill="currentColor" d="M53.2 41L1.7 143.8c-4.6 9.2.3 20.2 10.1 23l197.9 56.5c7.1 2 14.7-1 18.5-7.3L320 64 69.8 32.1c-6.9-.8-13.5 2.7-16.6 8.9zm585.1 102.8L586.8 41c-3.1-6.2-9.8-9.8-16.7-8.9L320 64l91.7 152.1c3.8 6.3 11.4 9.3 18.5 7.3l197.9-56.5c9.9-2.9 14.7-13.9 10.2-23.1zM425.7 256c-16.9 0-32.8-9-41.4-23.4L320 126l-64.2 106.6c-8.7 14.5-24.6 23.5-41.5 23.5-4.5 0-9-.6-13.3-1.9L64 215v178c0 14.7 10 27.5 24.2 31l216.2 54.1c10.2 2.5 20.9 2.5 31 0L551.8 424c14.2-3.6 24.2-16.4 24.2-31V215l-137 39.1c-4.3 1.3-8.8 1.9-13.3 1.9z"></path></svg><!-- <i class="fas fa-box-open"></i> --></td>--}}
                                {{--</tr>--}}
                                {{--</tbody>--}}
                                {{--<!--Table body-->--}}
                            {{--</table>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>

            <footer class="card-footer text-muted">
                <div class="float-left">
                    <span style="font-size:12px;"><strong>Version</strong> 1.0</span>
                </div>
                <div class="float-right">
                    <span style="font-size:12px;"><strong>Powered By</strong> : Web And App Pvt. Ltd.</span>
                </div>
            </footer>
        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/popper.js@1.12.6/dist/umd/popper.js" integrity="sha384-fA23ZRQ3G/J53mElWqVJEGJzU0sTs+SvzG8fXVWP+kJQ1lwFAOkcUOysnlKJC33U" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/bootstrap-material-design@4.1.1/dist/js/bootstrap-material-design.js" integrity="sha384-CauSuKpEqAFajSpkdjv3z9t8E7RlpJ1UP0lKM/+NdtSarroVKu069AlsRPKkFBz9" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
        <script>
            $(document).ready( function () {
                $('#datatable').dataTable({
                    'paging'      : true,
                    'lengthChange': true,
                    'searching'   : true,
                    'ordering'    : true,
                    'info'        : true,
                    'autoWidth'   : true,
                    'order'       : [[0,"desc"]],
                    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                });
            } );
        </script>
        @yield('customScript')
    </body>
</html>
