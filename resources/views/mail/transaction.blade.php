<html>
<body>
<div>
    <div style="margin-right: 5px; margin-left: 150px" align="left"><img src="{{env('APP_LOGO')}}" width="100px"
                                                                         height="100px"/></div>
    <div style="margin-right: 0px; font-family: calibri; font-size: 20px; clear: both;">
        <div style="float: left; margin-left: 25px;">{{env('APP_NAME')}} Transaction Alert on {{date('D, d M Y - h:i:s a')}}
        </div>
    </div>
    <div
        style="float: left; margin-left: 25px; margin-top: 10px; margin-bottom: 10px; font-family: calibri; font-size: 15px; clear: both;">
        Dear <strong>
            {{$data['user_name']}}</strong>, <br/> A payment was successfully completed on your account. <br/> Please
        see below details of the transaction:
    </div>
    <div style="clear: both;">
        <div
            style="margin-left: 25px; width: 400px; height: 385px; border-top-right-radius: 10px; border-top-left-radius: 10px; border: 1px solid #cccccc; font-family: calibri; font-size: 15px; float: left;">
            <table border="0" width="400" cellspacing="0" cellpadding="0" align="left">
                <tbody>
                <tr>
                    <td style="padding-left: 15px;" bgcolor="#E5E5E5" width="200" height="24">Amount</td>
                    <td style="padding-left: 15px;" bgcolor="#E5E5E5" width="200" height="24">
                        {{$data['amount']}}</td>
                </tr>
                <tr>
                    <td style="padding-left: 15px;" width="200" height="24">Payment Method</td>
                    <td style="padding-left: 15px;" width="200" height="24">
                        {{$data['payment_method']}}</td>
                </tr>
                <tr>
                    <td style="padding-left: 15px;" bgcolor="#E5E5E5" width="200" height="24">Description</td>
                    <td style="padding-left: 15px;" bgcolor="#E5E5E5" width="200" height="24">
                        {{$data['description']}}</td>
                </tr>
                <tr>
                    <td style="padding-left: 15px;" width="200" height="24">Reference Number</td>
                    <td style="padding-left: 15px;" width="200" height="24">
                        {{$data['transid']}}</td>
                </tr>
                <tr>
                    <td style="padding-left: 15px;" width="200" height="24">Initial Balance</td>
                    <td style="padding-left: 15px;" width="200" height="24">
                        {{$data['i_wallet']}}</td>
                </tr>
                <tr>
                    <td style="padding-left: 15px;" bgcolor="#E5E5E5" width="200" height="24">Wallet Balance</td>
                    <td style="padding-left: 15px;" bgcolor="#E5E5E5" width="200" height="24">
                        {{$data['f_wallet']}}</td>
                </tr>
                </tbody>
            </table>
            <div style="font-family: calibri; font-size: 12px; float: left; margin-top: 10px;">
                <strong>Note</strong>: {{$email_note}}<br/><br/> If you have any questions/issues, please contact us at
                <a href="mailto:{{$support_email}}">{{$support_email}}</a> <br/><span
                    style="font-family: calibri; font-size: 15px; float: left; clear: both;">Thanks for choosing us</span><br/>
                <span
                    style="font-family: calibri; font-size: 15px; float: left; clear: both; color: #006400;"><strong><font
                            size="5">{{env('APP_NAME')}} </font></strong> <br/> </span>
            </div>
        </div>
    </div>
    <div>
        <div style="width: 100%; height: 25px; padding-left: 25px; float: left;"><span
                style="font-family: calibri; font-size: 12px; float: left; color: #f30100;"> This mail was sent with ❤ from {{env('APP_NAME')}} to
{{$email}}</span></div>
    </div>
</div>
</body>
</html>


