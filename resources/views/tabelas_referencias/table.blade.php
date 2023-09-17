<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive" id="tabelasReferencias-table">
            <thead>
            <th>Nome</th>
            <th class="text-center">
                Vinculados
                <span class="label label-default circle label-xs">
                        <span class="fa fa-question" data-toggle="tooltip" data-placement="bottom"
                              title="Quantidade de clínicas ativas que usam essa tabela"></span>
                    </span>

            </th>
            <th class="text-center">
                Base
                <span class="label label-default circle label-xs">
                        <span class="fa fa-question" data-toggle="tooltip" data-placement="bottom"
                              title="Se a tabela é a base de referência para as outras tabelas, ou seja, os valores da tabela base serão considerados, caso não hajam exceções em outras tabelas."></span>
                    </span>
            </th>
            <th colspan="3" width="15%" class="text-center">Ações</th>
            </thead>
            <tbody>
            @foreach($tabelasReferencias as $tabelasReferencia)
                <tr>
                    <td>
                        {!! $tabelasReferencia->nome !!}
                    </td>

                    <td class="text-center">
                        <a class="badge" data-toggle="modal" data-target="#cat{{ $tabelasReferencia->id }}-modal">
                            {{ \Modules\Clinics\Entities\Clinicas::where('id_tabela', $tabelasReferencia->id)
                                ->where('ativo', '1')
                                ->count('id') }}
                        </a>
                        <div class="modal text-left" id="cat{{ $tabelasReferencia->id }}-modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">{{ $tabelasReferencia->nome }}</div>
                                    <div class="modal-body">
                                        <h5>Clínicas vinculadas:</h5>
                                        <ul class="list-unstyled">
                                            @foreach((new \Modules\Clinics\Entities\Clinicas())->where('id_tabela', $tabelasReferencia->id)->where('ativo', '1')->orderBy('nome_clinica', 'ASC')->get() as $clinica)
                                                <li>
                                                    <a target="_blank"
                                                       href="{!! route('clinicas.perfil', $clinica->id) !!}">{{ $clinica->nome_clinica }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>


                    <td class="text-center">
                        @if($tabelasReferencia->tabela_base)
                            <span class="badge badge-success" data-toggle="tooltip" data-placement="bottom"
                                  title="Esta tabela contém TODOS os procedimentos, com seus respectivos valores.">
                                Sim
                            </span>
                        @else
                            <span class="badge" data-toggle="tooltip" data-placement="bottom"
                                  title="Esta tabela contém apenas procedimentos exceções. O valor dos procedimentos que não estão nesta tabela, serão considerados os da tabela base.">
                                Não
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        {!! Form::open(['route' => ['tabelasReferencias.destroy', $tabelasReferencia->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{!! route('tabelasReferencias.edit', [$tabelasReferencia->id]) !!}"
                               class='btn btn-default btn-xs btn-circle edit'>
                                <i class="fa fa-pencil"></i>
                            </a>
                        </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>