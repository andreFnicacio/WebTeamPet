@extends('mail.modelos.cliente.v2')

@section('content')

<td align="center" valign="top" id="templateBody">
    <!--[if (gte mso 9)|(IE)]>
    <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
        <tr>
            <td align="center" valign="top" width="600" style="width:600px;">
                <![endif]-->
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
                <tbody>
                    <tr>
                        <td valign="top" class="bodyContainer">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
                            <tbody class="mcnTextBlockOuter">
                                <tr>
                                    <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
                                        <!--[if mso]>
                                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
                                        <tr>
                                            <![endif]-->
                                            <!--[if mso]>
                                            <td valign="top" width="600" style="width:600px;">
                                                <![endif]-->
                                                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                                                    <tbody>
                                                    <tr>
                                                        <td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
                                                            <h1>
                                                                <br>
                                                                <span style="color:#696969">
                                                                <span style="font-size:20px">
                                                                <span style="font-family:helvetica neue,helvetica,arial,verdana,sans-serif">Recuperação de senha</span>
                                                                </span>
                                                                </span>
                                                            </h1>
                                                            <p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
                                                                <span style="color:#696969">
                                                                    Você solicitou uma recuperação da senha.
                                                                    Para redefinir a sua senha <a href="{{ url(route('password.reset', $token, false)) }}">acesse</a>
                                                                </span>
                                                            </p>
                                                            <br>
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <!--[if mso]>
                                            </td>
                                            <![endif]-->
                                            <!--[if mso]>
                                        </tr>
                                        </table>
                                        <![endif]-->
                                    </td>
                                </tr>
                            </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
                </table>
                <!--[if (gte mso 9)|(IE)]>
            </td>
        </tr>
    </table>
    <![endif]-->
    </td>

@endsection
