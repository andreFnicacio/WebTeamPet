@extends('layouts.app')

@section('title')
    @parent
    Grupos de Carências - Editar - {{ $grupos->nome_grupo }}
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Grupos
                </span>
            </div>
            @permission('edit_grupos')
            <div class="actions" data-target="#grupos">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($grupos, [
                                'route' => [
                                    'grupos.update',
                                    $grupos->id
                                ],
                                'method' => 'patch',
                                'class' => 'form-horizontal',
                                'id' => 'grupos'
                            ]);
            !!}
                <div class="form-body">

                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button> Verifique se você preencheu todos os campos. 
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button> Validado com sucesso. 
                    </div>
                    <div class="col-md-12" style="margin-bottom: 20px;"> 
                        <h3 class="block" style="margin-top: 0px;">Dados Gerais</h3>
                    </div>
                    @include('grupos.fields')

                </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
    <!-- BEGIN PORTLET -->
    <div class="portlet  light  portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Procedimentos</span>
            </div>
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">
                        <a class="btn  green-jungle btn-outline sbold" target="_blank" href="{{ route('procedimentos.create', ['id_grupo' => $grupos->id]) }}" ><i class="icon-plus red-sunglo"></i> Novo</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12">
                    <thead>
                    <tr>
                        <th> Código </th>
                        <th> Nome </th>
                        <th> Especialidade? </th>
                        <th> Intervalo de uso </th>
                        <th> Valor Base </th>
                        <th> Criado em </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        /**
                         * @var $procedimentos \App\Models\Procedimentos[]
                         */
                        $procedimentos = $grupos->procedimentos()->orderBy('ativo', 'desc')->get();
                    @endphp
                    @foreach($procedimentos as $procedimento)
                        <tr>
                            <td> {{ $procedimento->cod_procedimento }} </td>
                            <td>
                                @if(!$procedimento->ativo)
                                <del>{{ $procedimento->nome_procedimento }}</del>
                                @else
                                    {{ $procedimento->nome_procedimento }}
                                @endif
                            </td>
                            <td> {{ $procedimento->especialista ? "Sim" : "Não" }} </td>
                            <td> {{ $procedimento->intervalo_usos }} </td>
                            <td> {{ $procedimento->valor_base }} </td>
                            <td> {{ $procedimento->created_at ? $procedimento->created_at->format('d/m/Y') : "" }} </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END PORTLET -->
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var $actions = $('.actions[data-target="#grupos"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('grupos.index') !!}";
                return;
            });
        });
    </script>
@endsection