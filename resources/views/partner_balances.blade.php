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
                                <th>MTN CG</th>
                                <th>9Mobile CG</th>
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
                                <td>{{$ogdams['cgMtn']}}</td>
                                <td>{{$ogdams['cg9mobile']}}</td>
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
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td><img src="https://uzobestgsm.com/static/styling/img/logo.png" alt=""
                                         class="rounded-circle thumb-sm mr-1 bg-dark">UZOBEST GSM
                                </td>
                                <td>₦{{number_format($uzobest['wallet_balance'])}}</td>
                                <td>₦{{$uzobest['bonus_balance']}}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td><img src="https://iyiinstant.com/static/styling/images/tt.png" alt=""
                                         class="rounded-circle thumb-sm mr-1">IYIINSTANT
                                </td>
                                <td>₦{{number_format($iyii['wallet_balance'])}}</td>
                                <td>₦{{$iyii['bonus_balance']}}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
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
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td><img src="https://ringo.ng/assets/logo-71fb7793.svg" alt=""
                                         class="rounded-circle thumb-sm mr-1">Ringo
                                </td>
                                <td>₦{{number_format($ringo['balance'])}}</td>
                                <td>₦{{number_format($ringo['commission_balance'])}}</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
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
