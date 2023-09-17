<table class="table table-responsive" id="urh-table">
    <thead>
        <th>Nome</th>
        <th>Valor R$</th>
        <th>Data Validade</th>
        <th>Credenciados</th>
        <th>Ativo</th>
        <th>Ações</th>
    </thead>
    <tbody>
    @foreach($urhs as $urh)
        <tr>
            <td>{!! $urh->nome_urh !!}</td>
            <td>{!! number_format($urh->valor_urh, 2, ',', '') !!}</td>
            <td>{!! \Carbon\Carbon::parse($urh->data_validade)->format('d/m/Y H:i') !!}</td>
            <td>{!! $urh->clinicas()->count() !!}</td>
            <td>
                @if($urh->ativo)
                    <span class="label label-success">Ativo</span>
                @else
                    <span class="label label-danger">Inativo</span>
                @endif
            </td>
            <td>
                <a href="javascript:;" target="_blank" class="btn btn-default btn-xs btn-circle edit" data-toggle="modal" data-target="#modal-urh-{{ $urh->id }}">
                    <i class="fa fa-edit"></i>
                </a>
                <div id="modal-urh-{{ $urh->id }}" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Editar - {!! $urh->nome_urh !!}</h4>
                            </div>
                            <div class="modal-body">
                                {!! Form::model($urh, [
                                                    'route' => [
                                                        'urh.update',
                                                        $urh->id
                                                    ],
                                                    'method' => 'patch',
                                                    'class' => 'form-horizontal novalidate',
                                                    'id' => 'urh-'.$urh->id
                                                ]);
                                !!}
                                @include('urh.fields')
                                {!! Form::close() !!}
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success" form="{{ 'urh-'.$urh->id }}">Salvar</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>

                    </div>
                </div>
                <a href="javascript:;" target="_blank" class="btn btn-default btn-xs btn-circle edit" data-toggle="modal" data-target="#modal-urh-historico-{{ $urh->id }}">
                    <i class="fa fa-clock-o"></i>
                </a>
                <div id="modal-urh-historico-{{ $urh->id }}" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Histórico - {!! $urh->nome_urh !!}</h4>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Valor R$</th>
                                            <th>Validade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(\App\Models\UrhHistorico::where('id_urh', $urh->id)->orderBy('created_at', 'DESC')->get() as $uh)
                                            <tr>
                                                <td>{{ number_format($uh->valor_urh, 2, ',', '') }}</td>
                                                <td>{{ $uh->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>