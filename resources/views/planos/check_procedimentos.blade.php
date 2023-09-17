@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Checar Procedimentos do plano
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="portlet">
                    <div class="portlet-body">
                        <div class="table-wrapper">
                            <div class="tools margin-bottom-30">
                                <span class="font-lg bold">Deseja resolver as inconsistências automaticamente?</span>
                                <a href="{{ route('planos.corrigirInconsistencias', ['id' => $id_plano]) }}" class="btn green-jungle" style="margin-left: 20px;">
                                    <i class="fa fa-thumbs-up"></i> Sim
                                </a>
                                <a href="{{ route('planos.edit', ['id' => $id_plano]) }}" class="btn red-sunglo" style="margin-left: 20px;">
                                    <i class="fa fa-thumbs-down"></i> Não
                                </a>
                            </div>
                            <table class="table table-responsive datatable table-hover responsive" id="clientes-table">
                                <thead>
                                    <th>ID Procedimento</th>
                                    <th>Valor do Sistema</th>
                                    <th>Valor do Arquivo</th>
                                    <th>Descrição da inconsistência</th>
                                </thead>
                                <tbody>
                                @foreach($procedimentosInconsistentes as $idProcedimento => $inconsistencias)
                                    @foreach($inconsistencias as $inconsistencia)
                                        <tr>
                                            <td>{{ $idProcedimento }}</td>
                                            <td>{{ $inconsistencia['valor_cadastrado'] ?? 'Nulo' }}</td>
                                            <td>{{ $inconsistencia['valor_arquivo'] ?? 'Nulo' }}</td>
                                            <td>{{ $inconsistencia['descricao'] }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


