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

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="text-right">
            <span style="color: white">Powered by PLANETF</span>
        </div>

        <div class="text-left">
            <span style="color: white">**In case of incomplete PIN kindly add zero in front of the pin to complete the pin</span>
        </div>

        <div class="row">
            {{--@foreach($cards as $card)
                <div class="col-lg-6">
                    <div class="contact-box">
                            <div class="col-sm-12">
                                <div class="text-center">
                                    <div class="m-t-xs font-bold">
                                        @if($user->photo!=null)
                                            <img alt="image" class="img-circle m-t-xs img-responsive pull-left" width="20px" height="20px" src="https://mcd.5starcompany.com.ng/app/avatar/{{$user->user_name }}.JPG"> {{$user->company_name }}
                                        @else
                                            @if($user->company_name!=null)
                                            <img alt="image" class="img-circle m-t-xs img-responsive pull-left" width="20px" height="20px" src="https://admin-mcd.5starcompany.com.ng/img/mcd_logo.png"> {{$user->company_name }}
                                                @else
                                                <img alt="image" class="img-circle m-t-xs img-responsive pull-left" width="20px" height="20px" src="https://admin-mcd.5starcompany.com.ng/img/mcd_logo.png"> {{$user->user_name }}
                                                @endif
                                            @endif

                                    </div>
                                    @if($user->address!=null)
                                    Address: {{$user->address}}<br />
                                    @endif
                                    @if($user->phoneno!=null)
                                        Tel No: {{$user->phoneno}}<br />
                                    @endif
                                </div>
                                <div class="well">
                                        <strong>Card Pin:  {{$card->pin}}</strong> |
                                        Serial No: {{$card->serial}}
                                    <div class="pull-right">
                                        <a class="btn btn-xs btn-white"><i class="fa fa-thumbs-up"></i> {{$card->network}} #{{$card->amount}} </a>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>

            @endforeach--}}


                @foreach($cards as $card)
                <div class="well" style="margin: 10px;">
                    <div class="pull-left" style="margin-right: 20px; font-size: 15px; font-family: '28 Days Later'; border-right-style: double; font-style: oblique; ">
                        @if($user->company_name!=null)
                            {{$user->company_name }}
                        @else
                            {{$user->user_name }}
                        @endif

                        <br />
                        @if($user->address!=null)
                            Address: {{$user->address}} <br />
                        @endif
                        @if($user->phoneno!=null)
                            {{$user->phoneno}} <br />
                        @endif
                    </div>
                    <div class="pull-center">
                        <strong> Card Pin:  {{$card->pin}}</strong> <br />
                        Serial No: {{$card->serial}}
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-xs btn-white"><i class="fa fa-thumbs-up"></i> {{$card->network}} #{{$card->amount}} </a>
                    </div>
                </div>

                @endforeach

        </div>

    </div>
</div>
</body>

</html>
