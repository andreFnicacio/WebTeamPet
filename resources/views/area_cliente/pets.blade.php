@extends('layouts.app')

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
                  Pets
                </span>
            </div>
            <div class="actions" data-target="#cobrancas">
                
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            <div class="table-responsive responsive">
                <table class="table col-md-12">
                    <thead>
                        <tr>
                            <th> ID </th>
                            <th> Nome do pet </th>
                            <th> Tipo </th>
                            <th> Raça </th>
                            <th> Pagamento </th>
                            <th> Status Plano </th>
                            <th>  </th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        /**
                         * @var $pets \App\Models\Pets[]
                         */
                        $pets = $cliente->pets()->get();
                    @endphp
                    @foreach($pets as $pet)
                    <tr>
                        <td> {{ $pet->id }} </td>
                        <td>
                            <a href="{{ route('cliente.pet', $pet->id) }}" target="_blank">{{ $pet->nome_pet }}</a>
                        </td>
                        <td> {{ $pet->tipo }} </td>
                        <td> {{ $pet->raca }} </td>
                        <td>  <span class="label label-sm label-success"> {{ $pet->statusPagamento()['resumo'] }} </span></td>
                        @php
                            $status = $pet->ativo ? "Ativo" : "Inativo";
                        @endphp
                        <td>
                            <span class="badge bg-{{ $pet->ativo ? "green-jungle" : "yellow-saffron" }}">{{ ucwords($status) }}</span>
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