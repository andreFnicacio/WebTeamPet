@extends('layouts.app')

@php
    if(Entrust::hasRole(['CLINICAS']) && !Entrust::hasRole(['ADMINISTRADOR', 'AUDITORIA'])) {
        $clinica = \Modules\Clinics\Entities\Clinicas::where('id_usuario', Auth::user()->id)->first();
    }
@endphp

@section('css')
    @parent
    <link href="{{ url('/') }}/assets/global/plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css"
          rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/assets/pages/css/search.min.css" rel="stylesheet" type="text/css"/>
    <style>
        .portlet.light > .portlet-title > .caption > .caption-subject {
            font-size: 29px;
            font-family: 'Roboto', sans-serif;
            font-weight: 100 !important;
            letter-spacing: 0.67px;
            float: none;

        }

        .portlet.light > .portlet-title > .caption {
            float: none;
            margin-bottom: 20px;
        }

        ul.dropdown-menu.dropdown-acoes-guia {
            overflow: auto;
            position: relative;
            min-width: unset;
        }

        .select2-container--bootstrap {
            width: auto !important;
        }
    </style>
@endsection

@section('title')
    @parent
    Guias
@endsection

@section('content')
    @include('common.swal')

    <section class="content-header text-center">
        <h1 class="title">Buscar Cliente</h1>
    </section>

    {{-- <div class="row">

       <div class="col-md-12"> --}}
    <div class="search-page search-content-3">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="search-filter" style="padding-bottom: 10px;">

                    <div class="search-label uppercase">Nome ou CPF</div>

                    <form action="{{ route('pets.fichaAvaliacaoBuscar') }}" method="GET">
                        {{-- {{ csrf_field() }} --}}
                        <div class="input-icon right">
                            <i class="icon-magnifier"></i>
                            <input type="text" name="nome_cpf" class="form-control" value="" placeholder="Nome ou CPF"
                                   required>
                            <small class="helper">Informe o Nome ou o CPF do titular do plano</small>
                        </div>

                        <button class="btn btn-lg green bold uppercase btn-block margin-bottom-15">Buscar</button>
                    </form>

                </div>
            </div>
            <div class="col-lg-8 col-md-8">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-paw font-blue"></i>
                            <span class="caption-subject bold font-blue uppercase"> Pets </span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        @if($cliente)
                            <h3 class="text-center margin-bottom-30 bold">{{ $cliente->nome_cliente }}</h3>
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th width="10%">Foto</th>
                                        <th>Nome</th>
                                        <th>Espécie/Raça</th>
                                        <th>Ficha de Avaliação</th>
                                    </tr>
                                    @foreach($cliente->pets as $pet)
                                        <tr>
                                            <td width="10%">
                                                <div class="pet_avatar"
                                                     style="background: url({{ $pet->avatar() }}) no-repeat center center / cover;width: 70px;height: 70px;background-size: cover;border-radius: 50%;"></div>
                                                {{-- <img src="{{ $pet->avatar() }}" style="width:40%;border-radius: 50%;"> --}}
                                            </td>
                                            <td>
                                                <h4 class="mt-card-name">
                                                    <strong>{{ strtoupper($pet->nome_pet) }}</strong>
                                                </h4>
                                            </td>
                                            <td>
                                                <p class="mt-card-desc font-grey-mint">{{ $pet->tipo == "GATO" ? "Gato" : "Cão" }}
                                                    - {{ $pet->raca->nome }}</p>
                                            </td>
                                            <td>
                                                <a href="{{ route('pets.fichaAvaliacao', ['idPet' => $pet->id]) }}"
                                                   class="btn green-jungle">
                                                    <i class="fa fa-plus-circle"></i>
                                                    Adicionar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                    </div>
                    @else
                        <h3 class="text-center">. . .</h3>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
    {{-- </div>
</div> --}}
@endsection

@section('scripts')
    @parent

    <script>
        $(document).ready(function () {
            $('#btnRealizarEncaminhamento').click(function (e) {
                if (!$('input[name="id_clinica"]').val()) {
                    swal('Erro!', 'Esta guia não pode ser realizada!', 'error');
                    return false;
                } else if (!$('select[name="id_prestador"]').val()) {
                    swal('Atenção!', 'Selecione um prestador!', 'warning');
                    return false;
                } else {
                    swal({
                        title: 'Atenção!',
                        text: "Ao vincular uma guia, você estará assumindo que o atendimento foi realizado por você na data de hoje. Não se esqueça de imprimir e coletar a assinatura do cliente.",
                        type: 'warning',
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonText: 'Sim, desejo realizar!'
                    }).then((result) => {
                        $('#realizarEncaminhamento').submit();
                    })
                }
                return true;
            });
        });
    </script>

    {{--<script src="{{ url('/') }}/assets/global/scripts/datatable.js" type="text/javascript"></script>--}}
    <script src="{{ url('/') }}/assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js"
            type="text/javascript"></script>
@endsection
