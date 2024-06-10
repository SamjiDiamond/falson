<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MCD Recharge Card</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Morris -->
    <link href="css/plugins/morris/morris-0.4.3.min.css" rel="stylesheet">

    <!-- Gritter -->
    <link href="js/plugins/gritter/jquery.gritter.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- Data Tables -->
    <link href="css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.responsive.css" rel="stylesheet">
    <link href="css/plugins/dataTables/dataTables.tableTools.min.css" rel="stylesheet">

    <link href="css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
    <link href="css/plugins/jqGrid/ui.jqgrid.css" rel="stylesheet">


    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Flot -->
    <script src="js/plugins/flot/jquery.flot.js"></script>
    <script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="js/plugins/flot/jquery.flot.spline.js"></script>
    <script src="js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="js/plugins/flot/jquery.flot.pie.js"></script>
    <script src="js/plugins/flot/jquery.flot.symbol.js"></script>
    <script src="js/plugins/flot/jquery.flot.time.js"></script>

    <!-- Peity -->
    <script src="js/plugins/peity/jquery.peity.min.js"></script>
    <script src="js/demo/peity-demo.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

    <!-- jQuery UI -->
    <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- Jvectormap -->
    <script src="js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

    <!-- EayPIE -->
    <script src="js/plugins/easypiechart/jquery.easypiechart.js"></script>

    <!-- Sparkline -->
    <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>

    <!-- Sparkline demo data  -->
    <script src="js/demo/sparkline-demo.js"></script>

    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="js/plugins/jeditable/jquery.jeditable.js"></script>

</head>

<body>
<div id="wrapper">

    <div class="wrapper wrapper-content animated fadeInRight" style="color: white">

        <div class="text-center">
            <img src="/img/mcd_logo.png" height="50px" width="50px" alt="user"
                 class="rounded-circle img-thumbnail mb-1"/> PLANETF
        </div>

        <div class="text-center" style="font-size: xx-large">
            Account Statement
        </div>

        <div class="text-right">
            <span style="color: white">Powered by PLANETF</span>
        </div>

        <div class="text-left">
            <span style="color: white">Account Name: SamjiDiamond</span>
        </div>

        <div style="margin-top: 50px"/>

        <div class="row">
            <div>
                <table id="datatable-buttons" class="table mb-0">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>I. Wallet</th>
                        <th>F. Wallet</th>
                        <th>I.P</th>
                        <th>Ref</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($trans as $dat)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>&#8358;{{number_format($dat->amount)}}</td>
                            <td>{{$dat->description}}</td>
                            <td class="center">

                                @if($dat->status=="delivered" || $dat->status=="Delivered" || $dat->status=="ORDER_RECEIVED" || $dat->status=="ORDER_COMPLETED")
                                    <span class="badge badge-success">{{$dat->status}}</span>
                                @elseif($dat->status=="not_delivered" || $dat->status=="Not Delivered" || $dat->status=="Error" || $dat->status=="ORDER_CANCELLED" || $dat->status=="Invalid Number" || $dat->status=="Unsuccessful")
                                    <span class="badge badge-warning">{{$dat->status}}</span>
                                @else
                                    <span class="badge badge-info">{{$dat->status}}</span>
                                @endif

                            </td>
                            <td>&#8358;{{number_format($dat->i_wallet)}}</td>
                            <td>&#8358;{{number_format($dat->f_wallet)}}</td>
                            <td>{{$dat->ip_address}}</td>
                            <td>{{$dat->ref}}</td>
                            <td>{{$dat->date}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
</body>

</html>
