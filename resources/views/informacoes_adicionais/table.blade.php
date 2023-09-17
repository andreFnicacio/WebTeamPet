<div class="portlet">
    <div class="portlet-body">
        <div class="table-wrapper">

            <table class="table table-responsive" id="informacoesAdicionais-table">
                <thead>
                <th>Resumo</th>
                <th>Descrição</th>
                <th>Cor</th>
                <th>Ícone</th>
                <th>Prioridade</th>
                <th colspan="3">Ações</th>
                </thead>
                <tbody>
                @foreach($informacoesAdicionais as $informacoesAdicionais)
                    <tr>
                        <td>{!! $informacoesAdicionais->descricao_resumida !!}</td>
                        <td>{!! $informacoesAdicionais->descricao_completa !!}</td>
                        <td><span class="fa fa-circle font-{{$informacoesAdicionais->cor}}"></span></td>
                        <td><span class="fa {{$informacoesAdicionais->icone}}"></span></td>
                        <td>
                            <div class="progress" style="height: 20px; margin-bottom: 0">
                                <div class="progress-bar" role="progressbar" aria-valuenow="{{$informacoesAdicionais->prioridade}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$informacoesAdicionais->prioridade}}%;">
                                    {{$informacoesAdicionais->prioridade}}%
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{route("informacoesAdicionais.edit", [$informacoesAdicionais->id])}}" class="btn btn-default btn-xs btn-circle edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @include('common.pagination', ['route' => route('informacoesAdicionais.index')])
        </div>
    </div>
</div>

