@extends('layouts.layouts')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Product List</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="/login">Home</a>
                </li>
                <li>
                    <a>Wallet</a>
                </li>
                <li class="active">
                    <strong>Wallet List</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Product Table</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#">Config option 1</a>
                                </li>
                                <li><a href="#">Config option 2</a>
                                </li>
                            </ul>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="">
                            <a onclick="fnClickAddRow();" href="javascript:void(0);" class="btn btn-primary ">Add a new row</a>
                        </div>
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Product Code</th>
                                <th>Product Type</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th>Server</th>
                                <th>Profit/Loss</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="gradeX">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="center"></td>
                                <td class="center"></td>
                                <td class="center"></td>
                            </tr>

                            </tbody>
                            <tfoot>
                            <tr>
                                <th>id</th>
                                <th>Product Code</th>
                                <th>Product Type</th>
                                <th>Selling Price</th>
                                <th>Cost Price</th>
                                <th>Server</th>
                                <th>Profit/Loss</th>
                            </tr>
                            </tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

            @endsection
