@section('css')
    @parent
    <style>
        .form-button button {
            -webkit-appearance: none;
            width: 100%;
            background: transparent;
            border: none;
            padding: 5px;
        }

        .form-button button:hover {
            text-decoration: none;
            background-image: none;
            background-color: #f6f6f6;
            color: #555;
            filter: none;
        }

        .dropdown-menu-left {
            right: 0;
            left: unset;
        }
    </style>
@endsection
<table class="table table-striped table-hover order-column datatables" >
    <thead>
    <tr>
        <th> Solicitante </th>
        <th> Título </th>
        <th width="20%"> Corpo </th>
        <th> Status </th>
        <th> Data da Solicitação </th>
        <th class="text-center"> Ações </th>
        {{--<th> Ações </th>--}}
    </tr>
    </thead>
    <tbody>
    @foreach($sugestoes as $s)
        <tr>
            <td>{{ $s->user()->first()->name }}</td>
            <td>{{ $s->titulo }}</td>
            <td>{{ $s->corpo }}</td>
            <td>
                @if($s->lido)
                    <span class="label bg-blue" data-toggle="tooltip" title="Lida">L</span>
                @endif
                @if($s->realizado)
                    <span class="label bg-green-meadow" data-toggle="tooltip" title="Realizada">R</span>
                @endif
                @if($s->arquivada)
                    <span class="label bg-grey" data-toggle="tooltip" title="Arquivada">A</span>
                @endif
            </td>
            <td>{{ $s->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-xs green dropdown-toggle" type="button" data-placement="left" data-toggle="dropdown" aria-expanded="false"> Ações
                        <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                        <li>
                            @if(Entrust::hasRole(['ADMINISTRADOR']))
                                <a href="#modal-prioridade" data-toggle="modal" onclick="setSugestaoModalPrioridade({{ json_encode($s) }})">
                                    <i class="icon-tag"></i> Definir prioridade
                                </a>
                            @endif
                        </li>
                        <li>
                            <form action="{{ route('ajuda.sugestoes.ler', $s->id) }}" class="form-button" method="POST">
                                {{ csrf_field() }}
                                <button type="submit">
                                    <i class="fa fa-eye"></i>Ler
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('ajuda.sugestoes.realizar', $s->id) }}" class="form-button" method="POST">
                                {{ csrf_field() }}
                                <button type="submit">
                                    <i class="fa fa-check-circle"></i>Realizar
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('ajuda.sugestoes.arquivar', $s->id) }}" class="form-button" method="POST">
                                {{ csrf_field() }}
                                <button type="submit">
                                    <i class="fa fa-archive"></i>Arquivar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@if(empty($status) || !in_array('ARQUIVADA', $status))
    <div class="text-center">
        <small>Arquivadas ({{ \App\Models\Sugestoes::where('arquivada', 1)->count() }})</small>
    </div>
@endif

@section('scripts')
    @parent
    <div id="modal-prioridade" class="modal fade" tabindex="-1" data-replace="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Priorizar sugestão</h4>
                </div>
                <div class="modal-body col-sm-10 col-sm-offset-1">
                    <form role="form" action="{{ route('ajuda.sugestoes.priorizar') }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_sugestao" id="prioridade_sugestao">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <h6>Título</h6>
                                        <div class="input-group col-sm-12">
                                            <span class="input-group-addon input-left">
                                                <i class="fa fa-pencil"></i>
                                            </span>
                                            <input type="text" readonly="" id="prioridade_titulo" class="form-control text-uppercase" name="titulo" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6>Sugestão</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <textarea name="corpo" readonly="" id="prioridade_corpo" id="corpo" class="form-control" rows="4" style="resize: none;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6>Prioridade</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <input type="text" ionrange name="prioridade" id="prioridade_valor" min="1" max="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="col-sm-3 pull-left" style="margin-top: 16px;
                                                                   display: inline-block;
                                                                   padding-left: 0;
                                                                   font-family: 'Roboto', sans-serif;
                                                                   font-size: 12px;
                                                                   color: #848484;">
                                <small>{{ (new \Carbon\Carbon())->format('d/m/Y') }}</small>
                            </div>
                            <button type="submit" class="btn blue pull-right">
                                <span>Enviar</span> <span class="fa fa-send"></span>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    {{--<button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>--}}
                </div>
            </div>
        </div>
    </div>
    <script>

            function setSugestaoModalPrioridade(sugestao) {
                $('#prioridade_sugestao').val(sugestao.id);
                $('#prioridade_valor').val(sugestao.prioridade);
                $('#prioridade_titulo').val(sugestao.titulo);
                $('#prioridade_corpo').val(sugestao.corpo);
            }

    </script>
@endsection