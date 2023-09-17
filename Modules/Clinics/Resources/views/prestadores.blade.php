@extends('layouts.app')

@section('title')
    @parent
    Meus Prestadores
@endsection

@section('css')
    @parent
    <link href="{{ url('/') }}/assets/global/plugins/ladda/ladda-themeless.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <section class="content-header text-center">
        <h1 class="title">Meus Prestadores</h1>
        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
                <h5 class="text-center">
                    Esta lista deve conter todos os médicos veterinários atuantes no momento.
                    É muito importante manter esta lista atualizada para que os clientes consigam encontrar as especialidades que precisam, de forma mais rápida e segura.
                </h5>
            </div>
        </div>
    </section>

    <div class="portlet light portlet-fit portlet-form ">

        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-user-md font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                    Meus Prestadores
                </span>
            </div>
            <div class="actions">
                <div class="btn-group">
                    <button class="btn btn-sm green" data-toggle="modal" data-target="#add-prestador">
                        <i class="fa fa-plus-circle"></i>
                        ADICIONAR PRESTADOR
                    </button>
                </div>
            </div>
        </div>

        <div class="portlet-body">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table table-hover table-light">
                    <thead>
                    <tr class="uppercase">
                        <th>PRESTADOR</th>
                        <th>CRMV</th>
                        <th>ESPECIALIDADE</th>
                        <th>TELEFONE</th>
                        <th>EMAIL</th>
                        <th>AVALIAÇÃO</th>
                        <th>AÇÕES</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($prestadores))
                        @foreach($prestadores as $prestador)
                            <tr>
                                <td>
                                    <span class="theme-font">{{ $prestador->nome }}</span>
                                </td>
                                <td>
                                    <span class="theme-font">{{ $prestador->getCRMV() }}</span>
                                </td>
                                <td>
                                    <span class="theme-font">{{ $prestador->especialista && $prestador->id_especialidade ? $prestador->especialidade->nome : 'Clínico Geral' }}</span>
                                </td>
                                <td>
                                    <span class="theme-font">{{ $prestador->telefone }}</span>
                                </td>
                                <td>
                                    <span class="theme-font">{{ $prestador->email }}</span>
                                </td>
                                <td>
                                    <span class="theme-font">{!! $prestador->ratingBadge() !!}</span>
                                </td>
                                <td>
                                    <span class="theme-font">
                                        <form action="#" method="POST" class="form-desvincular-prestador">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="id" value="{{ $clinica->id }}">
                                            <input type="hidden" name="id_prestador" value="{{ $prestador->id }}">
                                            <a class="btn btn-outline btn-circle dark btn-sm black submit">
                                                <i class="fa fa-trash-o"></i>
                                                Remover
                                            </a>
                                        </form>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">
                                <h5 class="text-center">
                                    <span class="theme-font">Nenhum prestador vinculado até o momento.</span>
                                </h5>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="modal" id="add-prestador">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">Adicionar Prestador</div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                            <div class="busca-prestador">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h4 class="text-center margin-top-30 margin-bottom-30">Informe o CRMV para buscar o Prestador</h4>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="form-group">
                                            <label>CRMV</label>
                                            <span class="required">*</span>
                                            <input type="text" name="crmv" class="form-control" id="crmv" required>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label>UF</label>
                                            <span class="required">*</span>
                                            <select name="crmv_uf" id="crmv_uf"  class="select2">
                                                <option value="AC" {{ ($clinica->estado == 'AC' ? 'selected' : '') }}>AC</option>
                                                <option value="AL" {{ ($clinica->estado == 'AL' ? 'selected' : '') }}>AL</option>
                                                <option value="AP" {{ ($clinica->estado == 'AP' ? 'selected' : '') }}>AP</option>
                                                <option value="AM" {{ ($clinica->estado == 'AM' ? 'selected' : '') }}>AM</option>
                                                <option value="BA" {{ ($clinica->estado == 'BA' ? 'selected' : '') }}>BA</option>
                                                <option value="CE" {{ ($clinica->estado == 'CE' ? 'selected' : '') }}>CE</option>
                                                <option value="DF" {{ ($clinica->estado == 'DF' ? 'selected' : '') }}>DF</option>
                                                <option value="ES" {{ ($clinica->estado == 'ES' ? 'selected' : '') }}>ES</option>
                                                <option value="GO" {{ ($clinica->estado == 'GO' ? 'selected' : '') }}>GO</option>
                                                <option value="MA" {{ ($clinica->estado == 'MA' ? 'selected' : '') }}>MA</option>
                                                <option value="MT" {{ ($clinica->estado == 'MT' ? 'selected' : '') }}>MT</option>
                                                <option value="MS" {{ ($clinica->estado == 'MS' ? 'selected' : '') }}>MS</option>
                                                <option value="MG" {{ ($clinica->estado == 'MG' ? 'selected' : '') }}>MG</option>
                                                <option value="PA" {{ ($clinica->estado == 'PA' ? 'selected' : '') }}>PA</option>
                                                <option value="PB" {{ ($clinica->estado == 'PB' ? 'selected' : '') }}>PB</option>
                                                <option value="PR" {{ ($clinica->estado == 'PR' ? 'selected' : '') }}>PR</option>
                                                <option value="PE" {{ ($clinica->estado == 'PE' ? 'selected' : '') }}>PE</option>
                                                <option value="PI" {{ ($clinica->estado == 'PI' ? 'selected' : '') }}>PI</option>
                                                <option value="RJ" {{ ($clinica->estado == 'RJ' ? 'selected' : '') }}>RJ</option>
                                                <option value="RN" {{ ($clinica->estado == 'RN' ? 'selected' : '') }}>RN</option>
                                                <option value="RS" {{ ($clinica->estado == 'RS' ? 'selected' : '') }}>RS</option>
                                                <option value="RO" {{ ($clinica->estado == 'RO' ? 'selected' : '') }}>RO</option>
                                                <option value="RR" {{ ($clinica->estado == 'RR' ? 'selected' : '') }}>RR</option>
                                                <option value="SC" {{ ($clinica->estado == 'SC' ? 'selected' : '') }}>SC</option>
                                                <option value="SP" {{ ($clinica->estado == 'SP' ? 'selected' : '') }}>SP</option>
                                                <option value="SE" {{ ($clinica->estado == 'SE' ? 'selected' : '') }}>SE</option>
                                                <option value="TO" {{ ($clinica->estado == 'TO' ? 'selected' : '') }}>TO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="button" class="btn green center-block margin-top-10 btn-buscar-prestador mt-ladda-btn ladda-button" data-style="zoom-in" data-spinner-color="#333">
                                        <span class="ladda-label">Buscar</span>
                                    </button>
                                </div>
                            </div>
                            <div class="box-prestador">
                                <div class="dados-prestador text-center" style="display: none;margin: 25px 0;">
                                    <h4>Prestador encontrado!</h4>
                                    <div class="well" style="padding: 10px;">
                                        <h4><strong class="nome-prestador"></strong></h4>
                                        <h5><strong class="especialidade-prestador"></strong></h5>
                                        <h5><strong class="crmv-prestador"></strong></h5>
                                        <form action="#" id="form-vincular-prestador" method="POST">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="id" value="{{ $clinica->id }}">
                                            <input type="hidden" name="id_prestador" class="id-prestador">
                                            <button class="btn blue center-block">Adicionar</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="form-prestador" style="display: none;margin: 25px 0;">
                                    <div class="text-center">
                                        <h4 class="bold">Não encontramos ninguém com esse CRMV :(</h4>
                                        <h5 class="bold">Informe os dados do novo Prestador!</h5>
                                    </div>
                                    <form action="{{ route('clinicas.solicitarPrestador') }}" method="POST" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label>Nome</label>
                                            <span class="required">*</span>
                                            <input type="text" name="Nome" class="form-control" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>CPF</label>
                                                    <span class="required">*</span>
                                                    <input type="text" name="CPF" class="form-control cpf" required>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Tipo</label>
                                                    <span class="required">*</span>
                                                    <select name="Tipo de Pessoa" class="select2" required>
                                                        <option value="PF">Pessoa Física</option>
                                                        <option value="PJ">Pessoa Jurídica</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <span class="required">*</span>
                                                    <input type="text" name="Email" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Telefone</label>
                                                    <span class="required">*</span>
                                                    <input type="text" name="Telefone" class="form-control tel" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>CRMV</label>
                                                    <span class="required">*</span>
                                                    <input type="text" name="CRMV" class="form-control crmv" required>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>UF (CRMV)</label>
                                                    <span class="required">*</span>
                                                    <select name="Estado do CRMV" id="crmv_uf" class="select2 crmv_uf" required>
                                                        <option value="AC">AC</option>
                                                        <option value="AL">AL</option>
                                                        <option value="AP">AP</option>
                                                        <option value="AM">AM</option>
                                                        <option value="BA">BA</option>
                                                        <option value="CE">CE</option>
                                                        <option value="DF">DF</option>
                                                        <option value="ES">ES</option>
                                                        <option value="GO">GO</option>
                                                        <option value="MA">MA</option>
                                                        <option value="MT">MT</option>
                                                        <option value="MS">MS</option>
                                                        <option value="MG">MG</option>
                                                        <option value="PA">PA</option>
                                                        <option value="PB">PB</option>
                                                        <option value="PR">PR</option>
                                                        <option value="PE">PE</option>
                                                        <option value="PI">PI</option>
                                                        <option value="RJ">RJ</option>
                                                        <option value="RN">RN</option>
                                                        <option value="RS">RS</option>
                                                        <option value="RO">RO</option>
                                                        <option value="RR">RR</option>
                                                        <option value="SC">SC</option>
                                                        <option value="SP">SP</option>
                                                        <option value="SE">SE</option>
                                                        <option value="TO">TO</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Data do CRMV</label>
                                            <span class="required">*</span>
                                            <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
                                                <input type="text" name="Data de Formação" class="form-control" readonly required>
                                                <span class="input-group-btn">
                                                    <button class="btn default" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Possui Especialidade?</label>
                                            <select name="Especialidade" id="id_especialidade" class="select2">
                                                <option value=""></option>
                                                @foreach(\App\Models\Especialidades::all() as $e)
                                                    <option value="{{ $e->nome }}">{{ $e->nome }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Documentos</label>
                                            <span class="required">*</span>
                                            <input type="file" name="documentos[]" class="form-control" required multiple>
                                            <small class="helper">A Carteira do CRMV é indispensável! Se for especialista, o diploma também deverá ser anexado.</small>
                                        </div>
                                        <div class="form-group">
                                            <div class="mt-checkbox-inline">
                                                <label class="mt-checkbox">
                                                    <input type="checkbox" value="">
                                                     <small>
                                                        Os dados do novo prestador serão analisados por nossa equipe. Após a validação,
                                                        o prestador será cadastrado automaticamente no sistema. Qualquer intercorrência,
                                                        entraremos em contato.
                                                     </small>
                                                    <span></span>
                                                </label>
                                            </div>
                                             <label for="aceite-termos" class="control-label">

                                            </label>
                                            <input type="checkbox" id="aceite-termos" class="mt-radio-inline">
                                        </div>
                                        <button class="btn blue center-block">Adicionar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection


@section('scripts')
    @parent
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ url('/') }}/assets/global/plugins/ladda/spin.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/ladda/ladda.min.js" type="text/javascript"></script>
     <script src="{{ url('/') }}/assets/pages/scripts/ui-buttons-spinners.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <script type="text/javascript">
        $('.btn-buscar-prestador').click(function(e) {
            e.preventDefault();

            var prestador = null;
            var crmv = $('#crmv').val();
            var crmv_uf = $('#crmv_uf').val();

            var l = Ladda.create(this);
            l.start();

            if (!crmv) {
                swal('Atenção!', 'Informe o CRMV do prestador', 'warning');
                l.stop();
            } else {
                $.get('/api/v1/buscaPrestador/', {
                    crmv: crmv,
                    crmv_uf: crmv_uf,
                }, function(res) {
                    if (res) {
                        preencheDadosPrestador(res);
                    } else {
                        exibeFormPrestador(crmv, crmv_uf);
                    }
                    $('.busca-prestador').hide();
                    l.stop();
                });
            }

            return false;
        });

        $('.form-desvincular-prestador .submit').on('click', function (e) {
            var form = $(this).closest('form');
            e.preventDefault();
            swal({
                title: 'Tem certeza?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, desejo remover!',
                cancelButtonText: 'Não!'
            }).then((result) => {
                if (result) {
                    $.ajax({
                        url: "{{ route('clinicas.desvincularPrestador') }}",
                        type: 'POST',
                        data: form.serializeArray()
                    }).then(function (res) {
                        window.location.reload();
                    });
                }
            });
            return false;
        });

        $('#form-vincular-prestador').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('clinicas.vincularPrestador') }}",
                type: 'POST',
                data: $('#form-vincular-prestador').serializeArray()
            }).then(function (res) {
                window.location.reload();
            });
            return false;
        });

        function preencheDadosPrestador(prestador) {
            $('.dados-prestador .nome-prestador').text(prestador.nome);
            $('.dados-prestador .especialidade-prestador').text(prestador.nome_especialidade ? prestador.nome_especialidade : 'Clínico Geral');
            $('.dados-prestador .crmv-prestador').text(prestador.crmv + '-' + prestador.crmv_uf);
            $('.dados-prestador .id-prestador').val(prestador.id);
            $('.form-prestador').hide();
            $('.dados-prestador').show();
        }

        function exibeFormPrestador(crmv, crmv_uf) {
            $('.form-prestador form .crmv').val(crmv);
            $('.form-prestador form .crmv_uf').val(crmv_uf);
            $('.form-prestador form #select2-crmv_uf-container').text(crmv_uf);
            $('.form-prestador').show();
            $('.dados-prestador').hide();
        }
    </script>
@endsection