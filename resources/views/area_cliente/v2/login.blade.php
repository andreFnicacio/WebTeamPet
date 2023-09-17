<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 4
Version: 5.0.5
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en" >
<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>
        Lifepet | Login
    </title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <!--end::Web font -->
    <!--begin::Base Styles -->
    <link href="{{ url('/assets/metronic5') }}/dist/default/assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/assets/metronic5') }}/dist/default/assets/demo/default/base/style.bundle.css?nocache" rel="stylesheet" type="text/css" />
    <!--end::Base Styles -->
    <link rel="shortcut icon" href="{{ url('/assets/metronic5') }}/dist/default/assets/demo/default/media/img/logo/favicon.ico" />
    <style type="text/css">
        
#queroser {
    width: 246px;
    background-color: #fff;
    color: #164762;
    padding: 0.8rem 3rem;
    /* margin-top: 30px; */
    border: none;
    border-radius: 60px;
    clear: both !important;
    display: block;
    margin: 0 auto;
    margin-top: 30px;
}

        @media (max-width: 768px) {

             div#m_login {
    background-image: url(https://www.lifepet.com.br/wp-content/uploads/2018/04/fundo1.jpg);
    background-size: cover;
    background-position: center top;
}
input {
    background: rgba(247, 246, 249, 0.85) !important;
}
.m-login.m-login--2 .m-login__wrapper .m-login__container .m-login__form .m-login__form-action .btn {
    padding: 0.8rem 3rem;
    margin-top: 10px;
    background-color: #164762;
    border: 1px solid #164762;
}
#queroser{  background-color:#ff8400; color: #fff ; padding: 0.8rem 3rem;
    margin-top: 30px; border: none; border-radius: 60px; }
}

       
    </style>
</head>
<!-- end::Head -->
<!-- end::Body -->
<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default"  >
<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--singin m-login--2 m-login-2--skin-2" id="m_login" style="background-color: #009cf3";>
        <div class="m-grid__item m-grid__item--fluid	m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__logo"><a href="#"><img height="80" src="{{ url('/assets/metronic5') }}/dist/default/assets/app/media/img/logos/logo-2.png"></a></div>
                <div class="m-login__signin">
                    <div class="m-login__head">
                        <h3 class="m-login__title font-white">
                            Bem-vindo. Faça seu login.
                        </h3>
                    </div>
                    <form class="m-login__form m-form" action="{{ route('cliente.doLogin') }}" method="POST">
                        {{ csrf_field() }}
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <button class="close" data-close="alert"></button>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif
                        <div class="form-group m-form__group">
                            <input class="form-control m-input"   type="text" id="loginemail" placeholder="E-mail" name="email" autocomplete="off">
                        </div>
                        <div class="form-group m-form__group">
                            <input class="form-control m-input m-login__form-input--last"  id="loginsenha"  type="password" placeholder="Senha" name="password">
                        </div>
                        {{--<div class="row m-login__form-sub">--}}
                            {{--<div class="col m--align-left m-login__form-left">--}}
                                {{--<label class="m-checkbox  font-white  m-checkbox--focus">--}}
                                    {{--<input type="checkbox" name="remember">--}}
                                    {{--Lembrar--}}
                                    {{--<span></span>--}}
                                {{--</label>--}}
                            {{--</div>--}}
                            {{--<div class="col m--align-right m-login__form-right">--}}
                                {{--<a href="javascript:;" id="m_login_forget_password" class="m-link font-white">--}}
                                    {{--Esqueceu sua senha?--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="m-login__form-action">
                            <button id="" type="submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">
                                Entrar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="m-login__signup">
                    <div class="m-login__head">
                        <h3 class="m-login__title font-white">
                            Cadastre-se
                        </h3>
                        <div class="m-login__desc font-white">
                            Para você se cadastrar, utilize o mesmo e-mail e CPF de cadastro na Lifepet:
                        </div>
                    </div>
                    <form class="m-login__form m-form" action="{{ route('cliente.registrar') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group m-form__group">
                            <input class="form-control m-input" type="text" placeholder="Nome Completo" name="name" required>
                        </div>
                        <div class="form-group m-form__group">
                            <input class="form-control m-input" type="text" placeholder="E-mail de cadastro" name="email" autocomplete="off" required>
                        </div>
                        <div class="form-group m-form__group">
                            <input class="form-control m-input cpf" type="text" placeholder="CPF do cadastro" name="cpf" autocomplete="off" required>
                        </div>


                        <div class="form-group m-form__group">
                            <input class="form-control m-input" type="password" placeholder="Senha" name="password" required>
                        </div>
                        <div class="form-group m-form__group">
                            <input class="form-control m-input m-login__form-input--last" type="password" placeholder="Repita a Senha" name="password_confirmation" required>
                        </div>
                        <div class="row form-group m-form__group m-login__form-sub">
                            <div class="col m--align-left">
                                <label class="m-checkbox m-checkbox--focus font-white">
                                    <input type="checkbox" name="agree" required>
                                    Eu concordo com a
                                    <a href="#" class="m-link m-link--focus font-white">
                                        Política de Privacidade.
                                    </a>
                                    .
                                    <span></span>
                                </label>
                                <span class="m-form__help"></span>
                            </div>
                        </div>
                        <div class="m-login__form-action">
                            <button id="" type="submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn">
                                Cadastrar
                            </button>

                            <button type="reset" id="m_login_signup_cancel" class="btn btn-outline-focus m-btn m-btn--pill m-btn--custom  m-login__btn">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="m-login__forget-password">
                    <div class="m-login__head">
                        <h3 class="m-login__title font-white">
                            Esqueceu sua senha?
                        </h3>
                        <div class="m-login__desc font-white">
                            Entre com seu e-mail para recuperá-la:
                        </div>
                    </div>
                    <form class="m-login__form m-form" action="">
                        <div class="form-group m-form__group">
                            <input class="form-control m-input" type="text" placeholder="Email" name="email" id="m_email" autocomplete="off">
                        </div>
                        <div class="m-login__form-action">
                            <button id="m_login_forget_password_submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn m-login__btn--primaryr">
                                Enviar
                            </button>

                            <button id="m_login_forget_password_cancel" class="btn btn-outline-focus m-btn m-btn--pill m-btn--custom m-login__btn">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
                <div class="m-login__account">
							<span class="m-login__account-msg font-white">
								Já é cliente, mas ainda não tem cadastro?
							</span>
                    &nbsp;
                    <a href="javascript:;" id="m_login_signup" style="font-weight: 700;" class=" font-white m-link m-link--light m-login__account-link">
                        Clique aqui!
                    </a>

                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- end:: Page -->
<!--begin::Base Scripts -->
<script src="{{ url('/assets/metronic5') }}/dist/default/assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
<script src="{{ url('/assets/metronic5') }}/dist/default/assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
<!--end::Base Scripts -->
<!--begin::Page Snippets -->
<script src="{{ url('/assets/metronic5') }}/dist/default/assets/snippets/pages/user/login.js" type="text/javascript"></script>
<script src="{{ url('/assets/') }}/global/plugins/jquery-mask/jquery.mask.min.js" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        $('.cpf').mask('000.000.000-00', {reverse: false});
    })
</script>
<!--end::Page Snippets -->
</body>
<!-- end::Body -->
</html>
