<div class="portlet">
    <div class="portlet-body">
        <table class="table table-responsive" id="vendedores-table">
    <thead>
        <th></th>
        <th>Identificação</th>
        <th>Nome</th>
        {{--<th>Imediato</th>--}}
        <th>Email</th>
        <th>Status</th>
        <th>Inside Sales</th>
        <th colspan="3" class="text-center">Ações</th>
    </thead>
    <tbody>
    @foreach($vendedores as $vendedor)
        <tr>
            <td><img src="{{ route('vendedores.avatar', $vendedor->id) }}" class="img-circle" width="100"></td>
            <td>{!! $vendedor->cpf_cnpj !!}</td>
            <td>{!! $vendedor->nome !!}</td>
            {{--<td>{!! $vendedor->contato_principal !!}</td>--}}
            <td>{!! $vendedor->email_contato !!}</td>
            <td>
                @if($vendedor->ativo)
                    <span class="badge badge-success">ATIVO</span>
                @else
                    <span class="badge badge-default">INATIVO</span>
                @endif
            </td>
            <td>
                <div class="text-center">
                    @if($vendedor->ativo)
                        @if($vendedor->canUseInsideSales())
                            <span class="badge badge-success">SIM</span>
                        @else
                            <span class="badge badge-danger" style="cursor: pointer;" data-toggle="modal" data-target="#modal-is-{{ $vendedor->id }}">
                                NÃO <i class="fa fa-question"></i>
                            </span>
                            <div class="modal" id="modal-is-{{ $vendedor->id }}">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">Acesso ao Inside Sales</div>
                                        <div class="modal-body">
                                            <div class="text-left">
                                                <h4>Necessário para o acesso:</h4>
                                                <ul class="list-unstyled">
                                                    @foreach($vendedor->getInsideSalesRules() as $regra)
                                                        <li>
                                                            @if($regra['status'])
                                                                <i class="fa fa-check font-green"></i>
                                                            @else
                                                                <i class="fa fa-times font-red"></i>
                                                            @endif
                                                            {!! $regra['msg'] !!}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </td>

            <td class="text-center" width="15%">
                {!! Form::open(['route' => ['vendedores.destroy', $vendedor->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('vendedores.edit', [$vendedor->id]) !!}" class='btn btn-default btn-xs btn-circle edit'>
                        <i class="fa fa-pencil"></i>
                    </a>
                    @permission('delete_vendedores')
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs btn-circle edit', 'onclick' => "return confirm('Você tem certeza?')"]) !!}
                    @endpermission
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
    </div>
</div>