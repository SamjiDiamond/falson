<div class="pre">
    <table border="0" width="680" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td width="680" height="25">
                <div style="margin-right: 5px; margin-left: 150px" align="left"><img src="{{env('APP_LOGO')}}"
                                                                                     width="100px" height="100px"/>
                </div>
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
                                        <p>Hello {{$customer->user_name}},</p>
                                        <p>Please find attached your account statement as requested, you may view at
                                            your convenience</p>
                                        <br/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <p>
                                            If this wasn't you, please reach out to our customer care <br/>
                                        </p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td width="15">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center">
                <br/>
                This mail was sent with ❤ from {{env('APP_NAME')}} to {{$customer->email}}
                <br/>
                <p>Copyright&copy;&nbsp;{{\Carbon\Carbon::now()->format('Y')}} {{env('APP_NAME')}}</p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
