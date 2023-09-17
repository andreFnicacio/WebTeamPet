@extends('layouts.metronic5')
@php
    $user = auth()->user();
    $cliente = \App\Models\Clientes::where('id_usuario', $user->id)->first();
    $pets = $cliente->pets()->where('ativo', 1)->get();
    $badges = \App\Helpers\Utils::petBadges($pets->all());
    //$historicos = \App\Models\HistoricoUso::whereIn('id_pet', $pets->pluck('id'))
    //                                        ->orderBy('created_at', 'DESC')
    //                                        //->groupBy('numero_guia')
    //                                        ->get();

    $queryHistorico = \Modules\Guides\Entities\HistoricoUso::whereIn('id_pet', $pets->pluck('id'))
                                        ->where('tipo_atendimento', '!=', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                                        ->orderBy('created_at', 'desc');
    if(\Entrust::hasRole(['CLIENTE'])) {
        $queryHistorico->where('status', '=', 'LIBERADO');
    }
    $historicosComuns = $queryHistorico->get();
    $historicosEncaminhamentos = \Modules\Guides\Entities\HistoricoUso::whereIn('id_pet', $pets->pluck('id'))
                                 ->where('tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                                 ->orderBy('created_at', 'desc')->get();
    $historicos = $historicosComuns->merge($historicosEncaminhamentos)->sortByDesc(function($guia) {
        return $guia->realizado_em ? $guia->realizado_em->format('YmdHis') : $guia->created_at->format('YmdHis');
    });

    $encaminhamentosAbertos = \Modules\Guides\Entities\HistoricoUso::whereIn('id_pet', $pets->pluck('id'))
                              ->where('tipo_atendimento', \Modules\Guides\Entities\HistoricoUso::TIPO_ENCAMINHAMENTO)
                              ->where('status', \Modules\Guides\Entities\HistoricoUso::STATUS_LIBERADO)
                              ->whereNull('realizado_em')
                              ->whereNotNull('data_liberacao')
                              ->get();
@endphp

@section('css')
    @parent
    <style>
        .m-list-timeline__items .m-list-timeline__item .m-list-timeline__time {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            width: 110px;
            padding: 0 7px 0 0;
            font-size: 0.85rem;
        }

        .box-encaminhamento {
            margin-bottom: 20px;
            box-shadow: 0px 1px 5px 1px #00000033;
        }

        .box-encaminhamento .box-encaminhamento-header,
        .box-encaminhamento .box-encaminhamento-body {
            padding: 15px
        }

        .box-encaminhamento .box-encaminhamento-header {
            background-color: #fba91b;
            color: white;
        }


        .box-encaminhamento-header > * {
            display: inline-block;
            width: auto;
        }

        .box-encaminhamento-header h4 {
            font-size: 1.2rem;
        }

        .box-encaminhamento-header .ajuda {
            text-align: right;
            float: right;
            padding: 2px 12px;
            background: white;
            color: #009cf3;
            font-weight: bold;
            border-radius: 15px;
            font-size: 0.7rem;
        }

        .box-encaminhamento-body .number {
            background: #fba91b;
            color: white;
            padding: 2px 9px;
            border-radius: 15px;
        }

        .box-encaminhamento-guia {
            margin-bottom: 20px;
            box-shadow: 0px 1px 5px 1px #00000033;
            padding: 15px;
        }

        .info-guia p {
            margin: 0;
        }

        .box-encaminhamento-guia .box-encaminhamento-header h6 {
            color: white;
            background: #009cf3;
            padding: 2px 12px;
            font-weight: bold;
            border-radius: 15px;
        }

        .marcar {
            text-align: center;
            height: 100%;
            color: #009cf3;
        }

        .marcar .fa-clock-o {
            font-size: 40pt;
        }

        .marcar span {
            margin-top: 20px;
            display: inline-block;
        }
    </style>
@endsection

@section('content')
    @parent

    <!-- END: Subheader -->
    <div class="m-content">
        <!--begin:: Widgets/Stats-->

        <!--end:: Widgets/Stats-->
        <!--Begin::Main Portlet-->
        {{-- @if($encaminhamentosAbertos->count())
            <div class="row">
                <div class="col-sm-12">
                    <div class="box-encaminhamento">
                        <div class="box-encaminhamento-header">
                            <h4><i class="fa fa-arrow-right"></i>&nbsp;Encaminhamento</h4>
                            <span class="ajuda">O que é isso?</span>
                        </div>
                        <div class="box-encaminhamento-body">
                            <p class="bold">Você possui <span class="number">{{ $encaminhamentosAbertos->count() }}</span> guias de encaminhamento autorizadas</p>
                            <p>Inicie agora o processo de marcação de consulta.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    @foreach($encaminhamentosAbertos as $encaminhamento)
                    <div class="box-encaminhamento-guia">
                        <div class="box-encaminhamento-header">
                            <h6>Guia {{ $encaminhamento->numero_guia }}</h6>
                        </div>
                        <div class="box-encaminhamento-body">
                            <div class="row">
                                <div class="col-sm-10 info-guia" style="width: 70%;">
                                    <p><strong>Solicitante:</strong> {{ $encaminhamento->solicitante ? $encaminhamento->solicitante->nome_clinica : '--'}}</p>
                                    <p><strong>Data da solicitação:</strong> {{ $encaminhamento->created_at->format('d/m/Y') }}</p>
                                    <p><strong>Liberada a partir de:</strong> {{ $encaminhamento->data_liberacao->format('d/m/Y') }}</p>
                                    <p><strong>Procedimento:</strong> {{ $encaminhamento->procedimento ? $encaminhamento->procedimento->nome_procedimento : '--'}}</p>
                                </div>
                                <div class="col-sm-2" style="width: 30%;">
                                    @if($encaminhamento->data_liberacao->lte(\Carbon\Carbon::today()))
                                    <div class="marcar">
                                        <a href="{{ route('cliente.encaminhamentos.definirCredenciado', $encaminhamento->id) }}">
                                            <i class="fa fa-clock-o"></i>
                                            <br>
                                            <span>MARCAR</span>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif --}}
        <div class="row">
            <div class="col-xl-6 acessorapido">
                <!--begin:: Widgets/Activity-->
                <div class="m-portlet m-portlet--bordered-semi m-portlet--widget-fit m-portlet--full-height m-portlet--skin-light ">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text m--font-light">
                                    Acesso rápido
                                </h3>
                            </div>
                        </div>

                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget17">
                            <div class="m-widget17__visual m-widget17__visual--chart m-portlet-fit--top m-portlet-fit--sides m--bg-danger">
                                <div class="m-widget17__chart" style="height:220px;">

                                </div>
                            </div>
                            <div class="m-widget17__stats">
                                <div class="m-widget17__items m-widget17__items-col1">

                                    <div class="m-widget17__item">
                                                <span class="m-widget17__icon">
                                                    <i class="fa fa-paw m--font-brand"></i>
                                                </span>

                                        <a href="{{ route('cliente.pets') }}">
                                                        <span class="m-widget17__subtitle">
                                                            Meus Pets
                                                        </span>
                                        </a>

                                        <span class="m-widget17__desc">
                                                    Histórico de uso e carências
                                                </span>
                                    </div>

                                    <div class="m-widget17__item">
                                            <span class="m-widget17__icon">
                                                <i class="flaticon-user-ok m--font-info"></i>
                                            </span>
                                        <a href="{{ route('cliente.dados') }}">
                                                <span class="m-widget17__subtitle">
                                                    Cadastro
                                                </span>
                                        </a>
                                        <span class="m-widget17__desc">
                                                Mantenha seus dados em dia
                                            </span>
                                    </div>
                                </div>
                                <div class="m-widget17__items m-widget17__items-col2">
                                    <div class="m-widget17__item">
                                            <span class="m-widget17__icon">
                                                <i class="f flaticon-coins m--font-success"></i>
                                            </span>
                                        <a href="{{ route('cliente.financeiro') }}">
                                            <span class="m-widget17__subtitle">
                                                2ª via de boleto
                                            </span>
                                        </a>
                                        <span class="m-widget17__desc">
                                                    Atualize agora
                                            </span>
                                    </div>
                                    <div class="m-widget17__item">
                                            <span class="m-widget17__icon">
                                                <i class="f flaticon-folder-2 m--font-danger"></i>
                                            </span>
                                        <a href="{{ route('cliente.documentos') }}">
                                            <span class="m-widget17__subtitle">
                                                Documentos
                                            </span>
                                        </a>
                                        <span class="m-widget17__desc">
                                                Confira seu contrato
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="m-widget17__stats green" style="cursor: pointer;"
                                 data-target="#solicitacao_reembolso" data-toggle="modal">
                                <div class="m-widget17__items m-widget17__items-col1">
                                    <div class="m-widget17__item bg-green-dark bg-green-dark-opacity"
                                         style="margin-top: 0;">
                                        <span class="m-widget17__icon">
                                            <i class="fa fa-exchange font-white"></i>
                                        </span>
                                        <span class="m-widget17__subtitle font-white">
                                            Reembolso
                                        </span>
                                        <span class="m-widget17__desc font-white">
                                            Solicite agora
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end:: Widgets/Activity-->
            </div>
            <div class="col-xl-6">
                <div class="m-portlet m-portlet--full-height usoshome ">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                    Histórico de usos
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            {{--<button type="button" class="btn btn-outline-success btn-sm">--}}
                            {{--Ver tudo--}}
                            {{--</button>--}}
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="m_widget4_tab1_content">
                                <div class="m-scrollable" data-scrollable="true" data-max-height="400"
                                     style="height: 400px; overflow: hidden;">
                                    <div class="m-list-timeline m-list-timeline--skin-light">
                                        <div class="m-list-timeline__items">

                                            @foreach($historicos as $historico)
                                                <a href="#modal-detalhe-guia-{{ $historico->id }}" data-toggle="modal"
                                                   class="link-discreto">
                                                    <div class="m-list-timeline__item">

                                                        <span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
                                                        <span class="m-list-timeline__text">
                                                        {!! $badges[$historico->id_pet] !!}
                                                        <span class="liberacao text-capitalize">
                                                            {{ \App\Helpers\Utils::excerpt($historico->procedimento()->first()->nome_procedimento) }}
                                                        </span>
                                                        </span>
                                                        <span class="m-list-timeline__time">
                                                            {{ \App\Helpers\Utils::shortDate($historico->created_at) }}
                                                        </span>
                                                    </div>
                                                </a>
                                            @endforeach


                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="m_widget4_tab2_content"></div>
                            <div class="tab-pane" id="m_widget4_tab3_content"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--End::Main Portlet-->

    </div>

    @include('area_cliente.v2.solicitacao_reembolso')

@endsection
