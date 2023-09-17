{{--<!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
    {{--<meta charset="utf-8">--}}
    {{--<meta http-equiv="X-UA-Compatible" content="IE=edge">--}}
    {{--<title>InfyOm Laravel Generator</title>--}}

    {{--<!-- Tell the browser to be responsive to screen width -->--}}
    {{--<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">--}}

    {{--<!-- Bootstrap 3.3.7 -->--}}
    {{--<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">--}}

    {{--<!-- Font Awesome -->--}}
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">--}}

    {{--<!-- Ionicons -->--}}
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">--}}

    {{--<!-- Theme style -->--}}
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/AdminLTE.min.css">--}}

    {{--<!-- iCheck -->--}}
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/skins/_all-skins.min.css">--}}

    {{--<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->--}}
    {{--<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->--}}
    {{--<!--[if lt IE 9]>--}}
    {{--<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>--}}
    {{--<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>--}}
    {{--<![endif]-->--}}

{{--</head>--}}
{{--<body class="hold-transition login-page">--}}
{{--<div class="login-box">--}}
    {{--<div class="login-logo">--}}
        {{--<a href="{{ url('/home') }}"><b>InfyOm </b>Generator</a>--}}
    {{--</div>--}}

    {{--<!-- /.login-logo -->--}}
    {{--<div class="login-box-body">--}}
        {{--<p class="login-box-msg">Sign in to start your session</p>--}}

        {{--<form method="post" action="{{ url('/login') }}">--}}
            {{--{!! csrf_field() !!}--}}

            {{--<div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">--}}
                {{--<input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">--}}
                {{--<span class="glyphicon glyphicon-envelope form-control-feedback"></span>--}}
                {{--@if ($errors->has('email'))--}}
                    {{--<span class="help-block">--}}
                    {{--<strong>{{ $errors->first('email') }}</strong>--}}
                {{--</span>--}}
                {{--@endif--}}
            {{--</div>--}}

            {{--<div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">--}}
                {{--<input type="password" class="form-control" placeholder="Password" name="password">--}}
                {{--<span class="glyphicon glyphicon-lock form-control-feedback"></span>--}}
                {{--@if ($errors->has('password'))--}}
                    {{--<span class="help-block">--}}
                    {{--<strong>{{ $errors->first('password') }}</strong>--}}
                {{--</span>--}}
                {{--@endif--}}

            {{--</div>--}}
            {{--<div class="row">--}}
                {{--<div class="col-xs-8">--}}
                    {{--<div class="checkbox icheck">--}}
                        {{--<label>--}}
                            {{--<input type="checkbox" name="remember"> Remember Me--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<!-- /.col -->--}}
                {{--<div class="col-xs-4">--}}
                    {{--<button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>--}}
                {{--</div>--}}
                {{--<!-- /.col -->--}}
            {{--</div>--}}
        {{--</form>--}}

        {{--<a href="{{ url('/password/reset') }}">I forgot my password</a><br>--}}
        {{--<a href="{{ url('/register') }}" class="text-center">Register a new membership</a>--}}

    {{--</div>--}}
    {{--<!-- /.login-box-body -->--}}
{{--</div>--}}
{{--<!-- /.login-box -->--}}

{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>--}}
{{--<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>--}}

{{--<!-- AdminLTE App -->--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/js/app.min.js"></script>--}}
{{--<script>--}}
    {{--$(function () {--}}
        {{--$('input').iCheck({--}}
            {{--checkboxClass: 'icheckbox_square-blue',--}}
            {{--radioClass: 'iradio_square-blue',--}}
            {{--increaseArea: '20%' // optional--}}
        {{--});--}}
    {{--});--}}
{{--</script>--}}
{{--</body>--}}
{{--</html>--}}
        <!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <title>Lifepet Manager | Login</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Preview page of Lifepet Manager for " name="description" />
    <meta content="" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{ url('/') }}/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{ url('/') }}/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ url('/') }}/assets/pages/css/login-5.min.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="favicon.ico" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css" />

    {{--Purple Theme--}}
    <style>
        .btn.blue:not(.btn-outline) {
            color: #FFF;
            background-color: #6e64ec;
            border-color: #6e64ec;
        }
        .btn.blue:not(.btn-outline).active, .btn.blue:not(.btn-outline):active, .btn.blue:not(.btn-outline):hover, .open>.btn.blue:not(.btn-outline).dropdown-toggle {
            color: #FFF;
            background-color: #5550b1;
            border-color: #5550b1;
        }
    </style>
</head>

<!-- END HEAD -->

<body class=" login">
<!-- BEGIN : LOGIN PAGE 5-2 -->
<div class="user-login-5">
    <div class="row bs-reset">
        <div class="col-md-6 login-container bs-reset">
            <img class="login-logo login-6" src="{{ url('/') }}/assets/pages/img/lifepet-logotipo-lilas-300px.png" style="padding: 49px 0;"/>
            <div class="login-content">
                <h1>Autorizador Lifepet</h1>
                @include('common.swal')
                <p> Olá, Credenciado. <br>Seja bem-vindo ao sistema de emissão de guias da Lifepet. </p>

                <form action="{{ url('/login') }}" class="login-form" method="post">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <button class="close" data-close="alert"></button>
                            <span>{{$errors->first()}}</span>
                        </div>
                    @endif

                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        <span>Entre com seu nome de usuário e senha abaixo:  </span>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            {{ csrf_field() }}
                            <input class="form-control form-control-solid placeholder-no-fix form-group" type="text" autocomplete="off" placeholder="Email" name="email" required/> </div>
                        <div class="col-xs-6">
                            <input class="form-control form-control-solid placeholder-no-fix form-group" type="password" autocomplete="off" placeholder="Senha" name="password" required/> </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-6 text-left">
                            <a href="#" data-toggle="modal" data-target="#modal-esqueci-senha">Esqueci minha senha</a>
                            <div class="modal fade" tabindex="-1" role="dialog" id="modal-esqueci-senha">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">Esqueci minha senha</h4>
                                        </div>
                                        <div class="modal-body" style="padding: 50px 30px;">
                                            <div class="form-group">
                                                <label style="font-size: 15px;"><strong>Informe o email do seu login</strong></label>
                                                <input type="text" class="form-control" name="email-esqueci-senha" id="email-esqueci-senha" required style="margin-bottom:0px;">
                                                <span class="helper" style="font-size:12px;">Enviaremos uma mensagem para o seu email para resetar sua senha</span>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                                            <button type="button" class="btn btn-primary btn-esqueci-senha">Enviar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 text-right">
                            <div class="forgot-password">
                                {{--<a href="javascript:;" id="forget-password" class="forget-password">Esqueceu sua senha?</a>--}}
                            </div>
                            <button class="btn blue" type="submit">Entrar</button>
                        </div>
                    </div>
                </form>
                {{--<!-- BEGIN FORGOT PASSWORD FORM -->--}}
                {{--<form class="forget-form" action="javascript:;" method="post">--}}
                    {{--<h3>Esqueceu sua senha?</h3>--}}
                    {{--<p> Entre com seu e-mail de cadastro para recuperar. </p>--}}
                    {{--<div class="form-group">--}}
                        {{--<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" /> </div>--}}
                    {{--<div class="form-actions">--}}
                        {{--<button type="button" id="back-btn" class="btn blue btn-outline">Voltar</button>--}}
                        {{--<button type="submit" class="btn blue uppercase pull-right">Enviar</button>--}}
                    {{--</div>--}}
                {{--</form>--}}
                <!-- END FORGOT PASSWORD FORM -->
            </div>
            <div class="login-footer">
                <div class="row bs-reset">

                    <div class="col-xs-12 bs-reset">
                        <div class="login-copyright text-right">
                            <p>Copyright &copy; Lifepet 2017</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 bs-reset">
            <div class="login-bg"> </div>
        </div>
    </div>
</div>
<!-- END : LOGIN PAGE 5-2 -->
<!--[if lt IE 9]>
<script src="{{ url('/') }}/assets/global/plugins/respond.min.js"></script>
<script src="{{ url('/') }}/assets/global/plugins/excanvas.min.js"></script>
<script src="{{ url('/') }}/assets/global/plugins/ie8.fix.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="{{ url('/') }}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ url('/') }}/assets/pages/scripts/login-5.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<!-- END THEME LAYOUT SCRIPTS -->
<script>
    $(document).ready(function()
    {
        $('#clickmewow').click(function()
        {
            $('#radio1003').attr('checked', 'checked');
        });

        $('.btn-esqueci-senha').click(function() {
            var email = $('#email-esqueci-senha').val();
            $('#modal-esqueci-senha').modal('toggle');
            swal({
                title: 'Buscando email',
                html: 'Aguarde um instante...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => {
                    swal.showLoading();
                    $.post('/api/v1/esquecisenha/enviaremail', {
                        'email': email
                    }, function (res) {
                        if (res.exists == true) {
                            swal('Sucesso!', 'Enviamos um link de redefinição de senha para o seu email!', 'success');
                            return true;
                        }
                        swal('Atenção', 'Nenhum usuário foi encontrado com este email!', 'warning');
                        return false;
                    });
                }
            });
        });
    })
</script>
</body>

</html>
