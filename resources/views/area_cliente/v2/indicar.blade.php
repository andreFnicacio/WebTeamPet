@extends('layouts.metronic5')

@section('title')
    Indique um amigo
@endsection
@section('content')
    <form method="post" class="form-horizontal m-form m-form--fit m-form--label-align-right" action="{{ route('indicacoes.indicar') }}" id="form-indicacoes">
        <div class="m-portlet light portlet-fit portlet-form" id="form-indicar">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
						<span class="m-portlet__head-icon">
							<i class="fa fa-envelope"></i>
						</span>
                        <h3 class="m-portlet__head-text">
                            Indique um amigo
                        </h3>
                    </div>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="form-group m--margin-top-10">
                    <div class="alert m-alert m-alert--default" role="alert">
                        Indique o plano para um amigo e ganhe descontos.
                    </div>
                </div>
                {!! csrf_field() !!}
                @for($i = 0; $i < 3; $i++)
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Nome {{$i+1}}</label>
                            <div class="input-group m-input-group">
                                <input type="text" name="nome[{{$i}}]" class="form-control m-input for-client" aria-describedby="nome">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email {{$i+1}}</label>
                            <div class="input-group m-input-group">
                                <input type="email" name="email[{{$i}}]" class="form-control m-input for-client" aria-describedby="email">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Telefone {{$i+1}}</label>
                            <div class="input-group m-input-group">
                                <input type="text" name="telefone[{{$i}}]" class="form-control m-input for-client tel" aria-describedby="telefone" maxlength="20">
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
            <div class="m-portlet__foot m-portlet__foot--fit">
                <div class="m-form__actions">
                    <button type="submit" class="btn btn-primary submitIndicacoes">Enviar</button>
                    <button type="reset" class="btn btn-secondary">Cancelar</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {

            $(".submitIndicacoes").click(function(e) {

                e.preventDefault();

                var leads = [];
                var email, telefone, nome;

                for (var i = 0; i < 3; i++) {
                    email = telefone = nome = "";

                    email = $('[name=email\\['+i+'\\]]').val();
                    nome = $('[name=nome\\['+i+'\\]]').val();
                    telefone = $('[name=telefone\\['+i+'\\]]').val();

                    if(!email || !telefone) {
                        if (nome) {
                            if(!telefone) {
                                swal('Atenção!', 'Informe o telefone do contato ' + (i+1) + ' antes de continuar.', 'warning');
                                return false;
                            }
                            if(!email) {
                                swal('Atenção!', 'Informe o email do contato ' + (i+1) + ' antes de continuar.', 'warning');
                                return false;
                            }
                        }
                    }

                    // leads.push([
                    //     { name: 'identificador', value: 'Indique Um Amigo' },
                    //     { name: 'token_rdstation', value: '0eb70ce4d806faa1a1a23773e3d174d4' },
                    //     { name: 'email', value: email },
                    //     { name: 'nomecompleto', value: nome },
                    //     { name: 'telefone', value: telefone },
                    // ]);
                }

                $("#form-indicacoes").submit();

                // leads.forEach(function(item, index, all) {
                //     RdIntegration.post(item, function () { console.log('Integrado: RD'); });
                // });
            });
        });
    </script>
@endsection
