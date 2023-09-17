<div class="portlet" style="padding-bottom: 20px;">
    <div class="portlet-body">
        <table class="table table-responsive" id="planos-table">
            <thead>
                <th>Nome</th>
                <th>Individual</th>
                <th>Familiar</th>
                <th>Vigência</th>
                <th>Modalidade</th>
                <th>Inativado em</th>
                <th>Status</th>
                <th colspan="3">Ações</th>
            </thead>
            <tbody>
                @foreach($planos as $planos)
                    <tr>
                        <td>{!! $planos->nome_plano !!}</td>
                        <td>R$ {!! number_format($planos->preco_plano_familiar, 2, ",", ".") !!}</td>
                        <td>R$ {!! number_format($planos->preco_plano_individual, 2, ",", ".") !!}</td>
                        <td>{!! $planos->data_vigencia ? $planos->data_vigencia->format("d/m/Y") : "" !!}</td>
                        <td>
                            @if($planos->participativo)
                                <span class="badge rounded badge-warning">Participativo</span>
                            @else
                                <span class="badge rounded badge-primary">Integral</span>
                            @endif
                        </td>
                        <td>{!! $planos->data_inatividade ? $planos->data_inatividade->format("d/m/Y") : "" !!}</td>
                        <td>
                            @if($planos->ativo)
                                <span class="badge rounded badge-success">Ativo</span>
                            @else
                                <span class="badge rounded badge-danger">Inativo</span>
                            @endif
                        </td>
                        <td>
                            {!! Form::open(['route' => ['planos.destroy', $planos->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{!! route('relatorios.coparticipacaoProcedimentosPlanos.download', [$planos->id]) !!}" class="btn btn-default btn-xs btn-circle edit" data-toggle="tooltip" data-original-title="Exportar relatório de coparticipação">
                                    <i class="fa fa-download"></i>
                                </a>
                                <a href="{!! route('planos.edit', [$planos->id]) !!}" class="btn btn-default btn-xs btn-circle edit" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                @permission('delete_planos')
                                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', [
                                        'type' => 'submit', 
                                        'class' => 'btn btn-danger btn-xs btn-circle edit', 
                                        'onclick' => "return confirm('Você tem certeza?')",
                                        'data-toggle' => "tooltip",
                                        'data-original-title' => "Excluir",
                                    ]) !!}
                                @endpermission
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @include('common.pagination', ['route' => route('planos.index')])
    </div>
</div>