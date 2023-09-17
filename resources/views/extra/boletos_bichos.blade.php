@extends('layouts.app')


@section('title')
    @parent
    Extra - 2ª via - Clientes Cia. dos Bichos
@endsection
@section('content')
    <br>
    <br>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="portlet">
                    <div class="portlet-body">
                        <form id="search-form" method="GET" class="search-form search-form-expanded" action="{{ url('/extras/boletos/bichos') }}">
                            <div class="input-group">
                                <select name="id_cliente" id="" class="select2 form-control">
                                    <option value=""></option>
                                    @foreach($clientes as $c)
                                        <option value="{{ $c['id'] }}">{{ $c['nome'] }}</option>
                                    @endforeach
                                </select>
                                <span class="input-group-btn">
                                    <button type="submit" class="btn">
                                        <i class="icon-magnifier"></i>
                                    </button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="portlet portlet-light">
                    <div class="portlet-body">
                        <table class="table" id="segunda-via-bichos">
                            <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Competência/Download</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($todasCobrancas as $cobrancaBichos)
                                <tr>
                                    <td>
                                        {{ $cobrancaBichos['nome'] }}
                                    </td>
                                    <td width="30%">
                                        @foreach($cobrancaBichos['cobrancas'] as $cobranca)
                                            <table>
                                                <tbody>
                                                <td>
                                                    {{ explode('/', $cobranca->dt_competencia_recb)[0] . '/' . explode('/', $cobranca->dt_competencia_recb)[2] }}
                                                </td>
                                                <td>
                                                    <a href="{{ $cobranca->link_2via }}" target="_blank" class="btn btn-default">
                                                        <i class="fa fa-file-pdf-o"></i>
                                                    </a>
                                                </td>
                                                </tbody>
                                            </table>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent

@endsection