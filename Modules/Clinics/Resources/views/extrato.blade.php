@extends('layouts.app')

@section('title')
    @parent
    Meu Extrato
@endsection

@section('content')

    <section class="content-header text-center">
        <h1 class="title">Meu Extrato</h1>
        <h3>URH Atual: {{ (new \App\Helpers\Utils())::money($clinica->urh->valor_urh) }}</h3>
    </section>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="filter-label">Competência</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group input-large">
                            <form id="filtro-competencia" action="{{ route('clinicas.extrato') }}" method="GET">
                                <select name="competencia" class="select2" onchange="$('#filtro-competencia').submit()">
                                    @foreach($competenciasSelect as $comp)
                                        <option value="{{ $comp['value'] }}" {{ $comp['value'] == $competenciaSelecionada ? 'selected' : '' }}>{{ $comp['label'] }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="portlet light portlet-fit portlet-form ">

        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-dollar font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Extrato - {{ \App\Helpers\Utils::getMonthName(\Carbon\Carbon::today()->month) }}/{{ \Carbon\Carbon::today()->year }}
                </span>
            </div>
            <div class="actions">
                <div class="btn-group">
                    <a class="btn btn-sm green dropdown-toggle no-print" href="javascript:;" data-toggle="dropdown"> EXPORTAR
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li data-toggle="tooltip" data-title="Em Breve!" data-placement="left">
                            <a href="javascript:;">
                                <i class="fa fa-file-excel-o disabled"></i> Excel
                            </a>
                        </li>
                        <li data-toggle="tooltip" data-title="Em Breve!" data-placement="left">
                            <a href="javascript:;">
                                <i class="fa fa-file-pdf-o disabled"></i> PDF
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="portlet-body">
            <div class="table-scrollable table-scrollable-borderless">
                <table class="table table-hover table-light">
                    <thead>
                    <tr class="uppercase">
                        <th colspan="1">GUIA</th>
                        <th>PLANO</th>
                        <th>DESCRIÇÃO</th>
                        <th>URH</th>
                        <th>DATA</th>
                        <th>PRESTADOR</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($extrato as $ex)
                        <tr>
                            <td>
                                <a href="{{ route('autorizador.verGuia', $ex->numero_guia) }}" target="_blank" class="primary-link">#{{ $ex->numero_guia }}</a>
                            </td>
                            <td>
                                {{ $ex->plano ?: ' Gamification ' }}
                            </td>
                            <td>
                                {{ $ex->descricao }}
                            </td>
                            <td>
                                <span class="badge {{ $ex->urh > 0 ? 'badge-success' : 'badge-danger' }} badge-success btn-sm btn-circle" style="margin-right:4px; margin-top:-2px;">
                                    <i class="fa fa-database"></i>
                                </span>
                                {{ $ex->urh }}
                            </td>
                            <td>{{ $ex->data }}</td>
                            <td>
                                <span class="bold theme-font">{{ $ex->prestador }}</span>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="6">
                            {{--<h4 class="text-center">--}}
                                {{--Total: {{ $valorUrhAcumulada }} URHs--}}
                            {{--</h4>--}}
                            <h5 class="text-center">
                                <strong>{{ (new \App\Helpers\Utils())::money($valorUrhAcumulada) }}</strong>
                            </h5>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

@endsection
