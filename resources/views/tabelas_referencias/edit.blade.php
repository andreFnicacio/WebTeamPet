@extends('layouts.app')

@section('title')
    @parent
    Tabelas de Referência - Editar - {{ $tabelasReferencia->nome }}
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Tabelas Referencia
                </span>
            </div>
            @permission('edit_tabelas_referencia')
            <div class="actions" data-target="#tabelasReferencias">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::model($tabelasReferencia, [
                                'route' => [
                                    'tabelasReferencias.update',
                                    $tabelasReferencia->id
                                ],
                                'method' => 'patch',
                                'class' => 'form-horizontal',
                                'id' => 'tabelasReferencias'
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
                    @include('tabelas_referencias.fields')

                </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
    {{--<p class="alert alert-danger">ADICIONAR PROCEDIMENTOS AGRUPADOS</p>--}}
    <div class="portlet light  portlet-fit portlet-form">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Procedimentos</span>
            </div>
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">
                        <button class="btn green-jungle add-procedimento">
                            <i class="fa fa-plus-circle"></i> Adicionar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body" style="padding: 10px;">
            <table class="table table-responsive" id="procedimentos-table">
                <thead>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Valor Base</th>
                    <th>Valor Tabela</th>
                    <th>Ações</th>
                </thead>
                <tbody>
                    @foreach(\App\Models\TabelasProcedimentos::where('id_tabela_referencia', $tabelasReferencia->id)->get() as $tabProcedimento)
                        <tr>
                            @php
                                $procedimento = $tabProcedimento->procedimento();

                                if (!$procedimento) {
                                    continue;
                                }

                                $salvo = $procedimento->salvo($tabelasReferencia->id);
                            @endphp
                            <td width="40%">
                                @if($procedimento->ativo)
                                    {!! $procedimento->nome_procedimento !!}
                                @else
                                    <strike>{!! $procedimento->nome_procedimento !!}</strike>
                                @endif
                            </td>
                            <td>
                                <a target="_blank" href="{{ route('procedimentos.edit', $procedimento->id) }}">{!! $procedimento->id !!}</a>
                            </td>
                            <td>R$ {!! number_format($procedimento->valor_base, 2, ",", ".") !!}</td>
                            <td>
                                @if($procedimento->ativo)
                                    <div class="form-group">
                                        <form action="{{ url('/') }}/tabelasProcedimentos/create" method="POST" id="form-tabela_procedimento-{{ $procedimento->id }}">
                                            {{ csrf_field() }}
                                            @if(!empty($salvo))
                                                <input type="hidden" name="id" value="{{ $salvo->id }}">
                                            @endif
                                            <input type="number" class="col-sm-12 line-input {{ empty($salvo) ? "empty" : "" }}" data-before="{{ !empty($salvo) ? $salvo->valor : 0 }}" name="valor" value="{{ !empty($salvo) ? $salvo->valor : 0 }}">
                                            <input type="hidden" name="id_procedimento" value="{{ $procedimento->id }}">
                                            <input type="hidden" name="id_tabela_referencia" value="{{ $tabelasReferencia->id }}">
                                        </form>
                                    </div>
                                @else
                                    <input type="number" readonly="" class="col-sm-12 line-input {{ empty($salvo) ? "empty" : "" }}" data-before="{{ !empty($salvo) ? $salvo->valor : 0 }}" name="valor" value="{{ !empty($salvo) ? $salvo->valor : 0 }}">
                                @endif
                            </td>
                            <td>
                                @if($procedimento->ativo)
                                    <a class="btn btn-circle btn-success save-tabelas-procedimentos" data-target="#form-tabela_procedimento-{{ $procedimento->id }}" data-on-complete="disable">
                                        <span class="fa fa-save"></span>
                                    </a>
                                    <a class="btn btn-circle btn-danger delete-tabelas-procedimentos" data-id="{{ $tabProcedimento->id }}">
                                        <span class="fa fa-trash"></span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script>

        $(document).ready(function() {

            var procedimentos = [];
            var selectProcedimentos = '';

            $.when( $.ajax( "{{ route('tabelasReferencias.procedimentos', $tabelasReferencia->id) }}" ) )
                .then(function( data ) {
                procedimentos = data;
                selectProcedimentos = '<select class="selectProcedimentos form-control select2" style="width: 100%;" onclick="changeProcedimento(this)">';
                selectProcedimentos += '<option value=""></option>';
                $.each(procedimentos, function (i, proc) {
                    selectProcedimentos += '<option value="'+proc.id+'" data-valorbase="'+proc.valor_base+'" '+(proc.ativo ? '' : 'disabled')+'>'+proc.nome_grupo+' - '+proc.nome_procedimento+'</option>';
                });
                selectProcedimentos += '</select>';
            });

            $('.add-procedimento').click(function () {
                $('#procedimentos-table tbody').append('<tr>' +
                    '<td>'+selectProcedimentos+'</td>' +
                    '<td>--</td>' +
                    '<td>--</td>' +
                    '<td>--</td>' +
                    '<td>' +
                        '<a class="btn btn-circle btn-success save-tabelas-procedimentos" disabled data-on-complete="disable">' +
                            '<span class="fa fa-save"></span>' +
                        '</a>' +
                        '<a class="btn btn-circle btn-danger delete-tabelas-procedimentos" data-on-complete="disable">' +
                            '<span class="fa fa-trash"></span>' +
                        '</a>' +
                    '</td>' +
                    '</tr>');
                $('.select2').select2();
            });

            $("#procedimentos-table tbody").on("change", "select.selectProcedimentos", function() {
                var id = $(this).val();
                if (id) {
                    var valor_base = $(this).find('option:selected').attr('data-valorbase');
                    var tr = $(this).closest('tr');
                    tr.find('td:nth-child(2)').html('<a target="_blank" href="/procedimentos/'+id+'/edit">'+id+'</a>');
                    tr.find('td:nth-child(3)').text('R$ ' + parseFloat(valor_base).toFixed(2).replace('.',','));
                    tr.find('td:nth-child(4)')
                        .html('<div class="form-group">\n' +
                              '    <form action="{{ url("/") }}/tabelasProcedimentos/create" method="POST" id="form-tabela_procedimento-novo-'+id+'">\n' +
                              '        {{ csrf_field() }}\n' +
                              '        <input type="number" class="col-sm-12 line-input empty" data-before="0" name="valor" value="">\n' +
                              '        <input type="hidden" name="id_procedimento" value="'+id+'">\n' +
                              '        <input type="hidden" name="id_tabela_referencia" value="{{ $tabelasReferencia->id }}">\n' +
                              '    </form>\n' +
                              '</div>');
                    tr.find('td:nth-child(5)')
                        .html('<a class="btn btn-circle btn-success save-tabelas-procedimentos" data-target="#form-tabela_procedimento-novo-'+id+'" data-on-complete="disable">\n' +
                              '    <span class="fa fa-save"></span>\n' +
                              '</a>' +
                              '<a class="btn btn-circle btn-danger delete-tabelas-procedimentos" data-id="">\n' +
                              '    <span class="fa fa-trash"></span>\n' +
                              '</a>'
                        );
                }
            });

            $('[data-before]').change(function() {
                if($(this).val() !== $(this).data('before')) {
                    $(this).addClass('changed');
                } else {
                    $(this).removeClass('changed');
                }
            });

            $("#procedimentos-table tbody").on("click", ".delete-tabelas-procedimentos", function () {
                var id = $(this).attr('data-id');
                var tr = $(this).closest('tr');

                if (!id) {
                    tr.remove();
                } else {
                    swal({
                        title: 'Tem certeza?',
                        text: "Tem certeza que deseja remover este procedimento desta tabela?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim',
                        cancelButtonText: 'Não',
                    }).then(function () {
                        $.ajax({
                            'url': '{{ route('tabelasProcedimentos.delete') }}',
                            'type': 'post',
                            'data': {
                                'id': id,
                                '_token': '{{ csrf_token() }}'
                            },
                            'success': function (data) {
                                tr.remove();
                                swal({
                                    title: 'Sucesso!',
                                    text: 'Procedimento removido!',
                                    type: 'success',
                                });
                            }
                        });
                    }, function (dismiss) {
                    });
                }
            });

            $("#procedimentos-table tbody").on("click", ".save-tabelas-procedimentos", function () {
                var self   = $(this);
                var target = self.data('target');
                if(!target) {
                    return;
                }
                var $form = $(target);
                if($form.length < 1) {
                    return;
                }

                var data   = $form.serialize();
                var action = $form.attr('action');
                var method = $form.attr('method');
                var $input = $form.find('input[data-before]');
                if($input.val() === $input.data('before')) {
                    return;
                }

                $.ajax({
                    'url'     : action,
                    'type'    : method,
                    'data'    : data,
                    'success' : function(data) {
                        if (data.msg.type == 'success') {
                            console.log("Procedimento vinculado com a tabela.\n"+data);
                            $input.data('before', data.valor);
                            $input.removeClass('changed');
                            $input.removeClass('empty');
                            $input.addClass('updated');

                            var tr = self.closest('tr');
                            var savedSelect = tr.find('select.selectProcedimentos');
                            if (savedSelect) {
                                savedSelect.parent().text(savedSelect.find('option:selected').text());
                                savedSelect.remove();
                            }
                            console.log(data, data.id);
                            // tr.find('.delete-tabelas-procedimentos').attr('data-id', '123');
                            tr.find('.delete-tabelas-procedimentos').attr('data-id', data.data.id);
                        }
                        swal({
                            title: data.msg.title,
                            text: data.msg.text,
                            type: data.msg.type,
                        });
                    },
                    'error'   : function(data) {
                        console.log("Erro ao tentar vincular o procedimento com a tabela.\n"+data);
                        $input.removeClass('changed');
                        $input.addClass('error');
                    }
                });
            });

            var $actions = $('.actions[data-target="#tabelasReferencias"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('tabelasReferencias.index') !!}";
                return;
            });
        });
    </script>
@endsection