@extends('layouts.metronic5')

@section('title')
    @parent
    Área do Cliente - Pets
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Indicações
                </span>
            </div>
            <div class="actions">
                
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            <div class="table-responsive">
                <table class="table table-hover table-checkable order-column datatables responsive table-responsive dataTable no-footer dtr-inline">
                    <thead>
                        <tr>
                            <th> ID </th>
                            <th> Nome </th>
                            <th> Email </th>
                            <th> Simulado </th>
                            <th> Comprado </th>
                            <th> Pago </th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        /**
                         * @var $pets \App\Models\Indicacoes[]
                         */
                    @endphp
                    @foreach($indicacoes as $indicacao)
                    <tr>
                        <td> {{ $indicacao->id }} </td>
                        <td>
                            {{ $indicacao->nome }}
                        </td>
                        <td> {{ $indicacao->email }} </td>
                        <td> {{ $indicacao->simulado ? "Sim" : "Não" }} </td>
                        <td>
                            {{ $indicacao->comprado ? "Sim" : "Não" }}
                        </td>
                        <td>
                            {{ $indicacao->pago ? "Sim" : "Não" }}
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
@endsection

@section('scripts')
    @parent

@endsection