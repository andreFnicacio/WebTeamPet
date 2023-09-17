@extends('layouts.app')

@section('title')
    Mudar senha
@endsection
@section('content')
    @if(isset($primeiroAcesso) && $primeiroAcesso || true)

    <div class="portlet light portlet-fit portlet-form" id="form-mudar-senha">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Modificar senha
                </span>
            </div>
        </div>
        <div class="portlet-body">
            <form method="post" class="form-horizontal" action="{{ route('clientes.mudarSenha') }}">
                {!! csrf_field() !!}
{{-- 
                <div class="form-group">
                    <label class="control-label col-md-3">Email
                    </label>
                    <div class="col-md-4">
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" class="form-control" type="text">
                    </div>
                    @if($errors->has('email'))
                        <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div> --}}
                <div class="form-group">
                    <label class="control-label col-md-3">Senha
                    </label>
                    <div class="col-md-4">
                        <input type="password" class="form-control" name="password" value="{{ old('password') }}" placeholder="Senha" class="form-control" type="text">
                    </div>
                    @if ($errors->has('password'))
                        <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Confirmação
                    </label>
                    <div class="col-md-4">
                        <input type="password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="Confirmação de senha" class="form-control" type="text">
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                    @endif
                </div>
                <br>
                <div class="form-group">
                    <div class="col-md-3">
                        
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-btn fa-refresh"></i>Mudar senha
                        </button>
                    </div>
                </div>
                
                <br>
            </form>
        </div>
    </div>

    @endif
@endsection

@section('scripts')
    @parent
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>

    <!-- AdminLTE App -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.3/js/app.min.js"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            //IntroJS
            var introKey = 'Intro__Clientes__mudarSenha__{{ Auth::user()->id }}';
            var introCMS = eval(sessionStorage.getItem(introKey));
            if(!introCMS) {
                AppIntros.mudarSenha();
                sessionStorage.setItem(introKey, true);
            }
        });
    </script>
@endsection
