<div class="pre">
    <table border="0" width="680" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td width="680" height="25">
                <div style="margin-right: 5px; margin-left: 150px" align="left"><img
                        src="{{env('APP_LOGO')}}" width="100px" height="100px"/></div>
            </td>
        </tr>
        <tr>
            <td>
                <table border="0" width="680" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td width="15">&nbsp;</td>
                        <td width="650">
                            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tbody>
                                <tr>
                                    <td>
                                        <p>&nbsp;</p>
                                        <p>Hi {{$data['user_name']}},</p>
                                        <p>Find your request below</p>
                                        <br/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div>
                                            @if(strtolower($data['type']) == "neco")
                                                @foreach($data['token'] as $pins)
                                                    <div
                                                        style="margin-bottom: 40px; font-weight: bolder; font-size: 15px">
                                                        <p>Token: {{$pins['token']}}</p>
                                                    </div>
                                                @endforeach
                                            @endif

                                            @if(strtolower($data['type']) == "waec")
                                                @foreach($data['token'] as $pins)
                                                    <div
                                                        style="margin-bottom: 40px; font-weight: bolder; font-size: 15px">
                                                        <p>Pin: {{$pins['pin']}} <br/>
                                                            Serial Number: {{$pins['serial_number']}}</p>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td width="15"></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center">
                <br/>
                Date & Time: {{\Carbon\Carbon::now()->format('D, M d, Y  H:i:s a T')}}
                <br/>
                This mail was sent with ❤ from {{env('APP_NAME')}} to {{$data['email']}}
                <br/>
                <p>Copyright&copy;&nbsp;{{\Carbon\Carbon::now()->format('Y')}} {{env('APP_NAME')}}</p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
