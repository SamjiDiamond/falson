@extends('layouts.layouts')
@section('title', 'Partner Balances')
@section('parentPageTitle', 'Transactions')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Balances</h4>
                    <p class="text-muted mb-4 font-13">Find below the list of partners and their balances</p>
                    <div class="table-responsive">
                        <table class="table mb-0 table-centered">
                            <thead>
                            <tr>
                                <th>Company</th>
                                <th>Main Balance</th>
                                <th>Bonus Balance</th>
                                <th>Airtel CG</th>
                                <th>GLO CG</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><img src="https://simhosting.ogdams.ng/assets/img/logo.png" alt=""
                                         class="rounded-circle thumb-sm mr-1"> OGDAMS
                                </td>
                                <td>₦{{number_format($ogdams['mainBalance'])}}</td>
                                <td>₦0</td>
                                <td>{{$ogdams['cgAirtel']}}</td>
                                <td>{{$ogdams['cgGlo']}}</td>
                                <td>
                                    <div class="dropdown d-inline-block float-right">
                                        <a class="nav-link dropdown-toggle arrow-none" id="dLabel4"
                                           data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                           aria-expanded="false"><i
                                                class="fas fa-ellipsis-v font-20 text-muted"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel4"><a
                                                class="dropdown-item" href="#">Creat Project</a> <a
                                                class="dropdown-item" href="#">Open Project</a> <a class="dropdown-item"
                                                                                                   href="#">Tasks
                                                Details</a></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img
                                        src="https://res.cloudinary.com/dytihk26b/image/upload/v1706620796/TEETOPDIGITALS/hw%20logo-96286-10292.png"
                                        alt="" class="rounded-circle thumb-sm mr-1">Honour World
                                </td>
                                <td>₦{{number_format($hw['available'])}}</td>
                                <td>₦{{$hw['bonus']}}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>
                                    <div class="dropdown d-inline-block float-right">
                                        <a class="nav-link dropdown-toggle arrow-none" id="dLabel5"
                                           data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                           aria-expanded="false"><i
                                                class="fas fa-ellipsis-v font-20 text-muted"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel5"><a
                                                class="dropdown-item" href="#">Creat Project</a> <a
                                                class="dropdown-item" href="#">Open Project</a> <a class="dropdown-item"
                                                                                                   href="#">Tasks
                                                Details</a></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img src="https://uzobestgsm.com/static/styling/img/logo.png" alt=""
                                         class="rounded-circle thumb-sm mr-1 bg-dark">UZOBEST GSM
                                </td>
                                <td>₦{{number_format($uzobest['wallet_balance'])}}</td>
                                <td>₦{{$uzobest['bonus_balance']}}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>
                                    <div class="dropdown d-inline-block float-right">
                                        <a class="nav-link dropdown-toggle arrow-none" id="dLabel5"
                                           data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                           aria-expanded="false"><i
                                                class="fas fa-ellipsis-v font-20 text-muted"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel5"><a
                                                class="dropdown-item" href="#">Creat Project</a> <a
                                                class="dropdown-item" href="#">Open Project</a> <a class="dropdown-item"
                                                                                                   href="#">Tasks
                                                Details</a></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img src="https://iyiinstant.com/static/styling/images/tt.png" alt=""
                                         class="rounded-circle thumb-sm mr-1">IYIINSTANT
                                </td>
                                <td>₦{{number_format($iyii['wallet_balance'])}}</td>
                                <td>₦{{$iyii['bonus_balance']}}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>
                                    <div class="dropdown d-inline-block float-right">
                                        <a class="nav-link dropdown-toggle arrow-none" id="dLabel5"
                                           data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                           aria-expanded="false"><i
                                                class="fas fa-ellipsis-v font-20 text-muted"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel5"><a
                                                class="dropdown-item" href="#">Creat Project</a> <a
                                                class="dropdown-item" href="#">Open Project</a> <a class="dropdown-item"
                                                                                                   href="#">Tasks
                                                Details</a></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img
                                        src="https://autosyncng.com/storage/site/tca6coWCc3vDOMNaWiiXdC7B6LAnWRnPdq7RSWc1.png"
                                        alt="" class="rounded-circle thumb-sm mr-1">AutoSyncNG
                                </td>
                                <td>₦{{number_format($autosync['wallet_balance'])}}</td>
                                <td>₦{{$autosync['commission_balance']}}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>
                                    <div class="dropdown d-inline-block float-right">
                                        <a class="nav-link dropdown-toggle arrow-none" id="dLabel5"
                                           data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                           aria-expanded="false"><i
                                                class="fas fa-ellipsis-v font-20 text-muted"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel5"><a
                                                class="dropdown-item" href="#">Creat Project</a> <a
                                                class="dropdown-item" href="#">Open Project</a> <a class="dropdown-item"
                                                                                                   href="#">Tasks
                                                Details</a></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img src="https://ringo.ng/assets/logo-71fb7793.svg" alt=""
                                         class="rounded-circle thumb-sm mr-1">AutoSyncNG
                                </td>
                                <td>₦{{number_format($autosync['wallet_balance'])}}</td>
                                <td>₦{{$autosync['commission_balance']}}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>
                                    <div class="dropdown d-inline-block float-right">
                                        <a class="nav-link dropdown-toggle arrow-none" id="dLabel5"
                                           data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                                           aria-expanded="false"><i
                                                class="fas fa-ellipsis-v font-20 text-muted"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel5"><a
                                                class="dropdown-item" href="#">Creat Project</a> <a
                                                class="dropdown-item" href="#">Open Project</a> <a class="dropdown-item"
                                                                                                   href="#">Tasks
                                                Details</a></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection
