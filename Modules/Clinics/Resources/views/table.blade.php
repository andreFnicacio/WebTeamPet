<div class="portlet" style="padding-bottom: 20px;">
    <div class="portlet-body">
        <table class="table table-responsive" id="clinicas-table">
            <thead>
                <th>Identificação</th>
                <th>Nome</th>
                {{--<th>Imediato</th>--}}
                {{-- <th>Email</th>
                <th>Telefone</th> --}}
                <th>Tabela</th>
                <th>Ativo</th>
                <th colspan="3" class="text-center">Ações</th>
            </thead>
            <tbody>
                @foreach($clinicas as $clinica)
                    <tr>
                        <td>
                                <img alt="" src="{!! $clinica->avatar() !!}" class="img-circle" style="width:40px;height:40px;">
                            </td>
                        <td>{!! $clinica->nome_clinica !!}</td>
                        {{--<td>{!! $clinica->contato_principal !!}</td>--}}
                        {{-- <td>{!! $clinica->email_contato !!}</td>
                        <td>{!! $clinica->telefones !!}</td> --}}
                        <td class="text-center">{!! $clinica->nome_tabela !!}</td>
                        <td class="text-center">
                            @if($clinica->ativo)
                                <span class="badge badge-success">SIM</span>
                            @else
                                <span class="badge badge-danger">NÃO</span>
                            @endif
                        </td>
                        <td class="text-center" width="15%">
                            {!! Form::open(['route' => ['clinicas.destroy', $clinica->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{!! route('clinicas.perfil', [$clinica->id]) !!}" class='btn btn-default btn-xs btn-circle edit'>
                                    <i class="fa fa-pencil"></i>
                                </a>
                                @permission('delete_clinicas')
                                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs btn-circle edit', 'onclick' => "return confirm('Você tem certeza?')"]) !!}
                                @endpermission
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @include('common.pagination', ['route' => route('clinicas.index')])
    </div>
</div>