@extends('layouts.app')

@section('title')
    @parent
    Credenciados - Editar - {{ $clinicas->nome_clinica }}
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Credenciados
                </span>

                <button type="submit" class="btn btn-xs blue" data-toggle="modal" data-target="#atualizar-usuario">
                    <i class="fa fa-user"></i>
                    Usuário
                </button>
                <form action="{{ route('clinicas.atualizarUsuario', ['id' => $clinicas->id]) }}"
                      style="display: inline; margin-left: 10px" method="post">
                    {{ csrf_field() }}
                    <div class="modal" id="atualizar-usuario">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Atualizar Usuário
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <input type="text" class="form-control" name="email"
                                               value="{{ $clinicas->id_usuario ? $clinicas->user->email : $clinicas->email_contato }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Nova Senha</label>
                                        <input type="text" class="form-control" name="password">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            @permission('edit_clinicas')
            <div class="actions" data-target="#clinicas">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($clinicas, [
                                'route' => [
                                    'clinicas.update',
                                    $clinicas->id
                                ],
                                'method' => 'patch',
                                'class' => 'form-horizontal',
                                'id' => 'clinicas'
                            ]);
            !!}
            <div class="form-body">

                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    Verifique se você preencheu todos os campos.
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    Validado com sucesso.
                </div>
                <div class="col-md-12" style="margin-bottom: 20px;">
                    <h3 class="block" style="margin-top: 0px;">Dados Gerais</h3>
                </div>
                @include('clinicas.fields')

            </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
    <!-- BEGIN VALIDATION STATES-->
    <div id="clinicasLimites">

        <div class="portlet  light  portlet-form ">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-settings font-red-sunglo"></i>
                    <span class="caption-subject font-red-sunglo sbold uppercase">Limites</span>
                </div>
                <div class="actions">
                    <div class="row">
                        <div class="col-md-offset-0 col-md-12">
                            <a class="btn  green-jungle btn-outline sbold" href="#novo-limite" target="_blank"
                               data-toggle="modal"><i class="icon-plus red-sunglo"></i> Novo</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table col-md-12">
                        <thead>
                        <tr>
                            <th> Plano</th>
                            <th> Procedimento</th>
                            <th> Limite</th>
                            <th> Intervalo</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            /**
                             * @var $pets \Modules\Veterinaries\Entities\Prestadores[]
                             */
                            $prestadores = $clinicas->prestadores()->get();
                        @endphp
                        @foreach($prestadores as $p)
                            <tr>
                                <td> {{ $p->id }} </td>
                                <td> {{ $p->nome }}</td>
                                <td> {{ $p->crmv }} </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="novo-limite" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
            <div class="modal-dialog">

                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Criar limite</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label for="">Plano:</label><br>
                                </div>
                                <div class="col-sm-8">
                                    <select required class="form-control select2" name="plano">

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label for="">Procedimento:</label><br>
                                </div>
                                <div class="col-sm-8">
                                    <select required class="form-control select2" name="procedimento">

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label for="">Limite:</label><br>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" name="limite" id="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-3">
                                    <label for="">Intervalo:</label><br>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" name="intervalo" id="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline btn-success">Confirmar</button>
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END VALIDATION STATES-->
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function () {
            var $actions = $('.actions[data-target="#clinicas"]');

            $actions.find('#save').click(function () {
                var target = $actions.attr('data-target');
                if (target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function () {
                var target = $actions.attr('data-target');
                location.href = "{!! route('clinicas.index') !!}";
                return;
            });
        });
    </script>
@endsection