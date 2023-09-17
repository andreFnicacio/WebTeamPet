<div class="portlet" style="padding-bottom: 20px;">
    <div class="portlet-body">
        <table class="table table-responsive" id="procedimentos-table">
            <thead>
                <th>Codígo</th>
                <th>Nome</th>
                <th>Pré-Cirúrgico?</th>
                <th>Ativo?</th>
                <th>Especialidade?</th>
                <th>Intervalo de Usos</th>
                {{--<th>Valor Base</th>--}}
                <th>Grupo</th>
                <th colspan="3">Ações</th>
            </thead>
            <tbody>
            @foreach($procedimentos as $procedimento)
                <tr>
                    <td>{!! $procedimento->cod_procedimento !!}</td>
                    <td>{!! $procedimento->nome_procedimento !!}</td>
                    <td class="text-center">
                        @if($procedimento->pre_cirurgico)
                            <i class="fa fa-circle" data-toggle="tooltip" title="SIM"></i>
                        @else
                            <i class="fa fa-circle-o" data-toggle="tooltip" title="NÃO"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($procedimento->ativo)
                            <i class="fa fa-circle" data-toggle="tooltip" title="SIM"></i>
                        @else
                            <i class="fa fa-circle-o" data-toggle="tooltip" title="NÃO"></i>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($procedimento->especialista)
                            <i class="fa fa-circle" data-toggle="tooltip" title="SIM"></i>
                        @else
                            <i class="fa fa-circle-o" data-toggle="tooltip" title="NÃO"></i>
                        @endif
                    </td>
                    <td>
                        @if($procedimento->intervalo_usos >= 9999)
                            ILIMITADO
                        @else
                            {!! $procedimento->intervalo_usos !!} dias
                        @endif
                    </td>
                    {{--<td>{!! $procedimento->valor_base !!}</td>--}}
                    <td>{!! $procedimento->grupo()->first()->nome_grupo !!}</td>
                    <td>
                        {!! Form::open(['route' => ['procedimentos.destroy', $procedimento->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{!! route('procedimentos.edit', [$procedimento->id]) !!}" class='btn btn-default btn-xs btn-circle edit'><i class="fa fa-pencil"></i></a>
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @include('common.pagination', ['route' => route('procedimentos.index')])
    </div>
</div>