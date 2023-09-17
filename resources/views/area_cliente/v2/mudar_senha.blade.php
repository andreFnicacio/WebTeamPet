@extends('layouts.metronic5')

@section('title')
    Mudar senha
@endsection
@section('content')
    <form method="post" class="form-horizontal m-form m-form--fit m-form--label-align-right" action="{{ route('clientes.mudarSenha') }}">
      <div class="m-portlet light portlet-fit portlet-form" id="form-mudar-senha">
          <div class="m-portlet__head">
              <div class="m-portlet__head-caption">
                  <div class="m-portlet__head-title">
						<span class="m-portlet__head-icon">
							<i class="fa fa-lock"></i>
						</span>
                      <h3 class="m-portlet__head-text">
                          Mudar senha
                      </h3>
                  </div>
              </div>
          </div>
            <div class="m-portlet__body">
                <div class="form-group m--margin-top-10">
                    <div class="alert m-alert m-alert--default" role="alert">
                        Lembre-se de sempre utilizar senhas seguras para proteger os seus dados. Use combinações de letras maíusculas e minúsculas juntamente com números e símbolos sempre que possível.
                    </div>
                </div>
                    {!! csrf_field() !!}
                    <div class="form-group ">
                        <label for="exampleInputEmail1">Senha</label>
                        <div class="input-group m-input-group">
                            <input type="password" name="password" value="{{ old('password') }}" class="form-control m-input for-client" aria-describedby="campo-de-senha">
                        </div>
                        @if ($errors->has('password'))
                            <span class="m-form__help">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group ">
                        <label for="exampleInputEmail1">Confirmação</label>
                        <div class="input-group m-input-group">
                            <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}" class="form-control m-input for-client" aria-describedby="campo-de-senha">
                        </div>
                        @if ($errors->has('password'))
                            <span class="m-form__help">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>
                    <br>
            </div>
          <div class="m-portlet__foot m-portlet__foot--fit">
              <div class="m-form__actions">
                  <button type="submit" class="btn btn-primary">Mudar senha</button>
                  <button type="reset" class="btn btn-secondary">Cancelar</button>
              </div>
          </div>
        </div>
    </form>
@endsection

@section('scripts')
    @parent
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
