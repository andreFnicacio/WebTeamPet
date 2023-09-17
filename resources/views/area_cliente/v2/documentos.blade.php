@extends('layouts.metronic5')
@section('css')
    <script>
        window.idCliente = "{{ $cliente->id }}";
    </script>
    @parent
@endsection
@section('title')
    @parent
    Documentos
@endsection
@section('content')
    <div class="m-portlet  light  portlet-form " id="arquivos">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
						<span class="m-portlet__head-icon">
							<i class="fa fa-calendar"></i>
						</span>
                    <h3 class="m-portlet__head-text">
                        Documentos
                    </h3>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table table-hover table-checkable order-column datatables responsive table-responsive dataTable no-footer dtr-inline">
                    <thead>
                    <tr>
                        <th> </th>
                        <th > Criação </th>
                        <th > Descrição </th>
                        <th > Tamanho </th>
                        <th > Autor </th>
                        <th >  </th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($cliente->uploads()->where('public', 1)->orderBy('created_at', 'DESC')->get() as $file)
                            <tr>
                                <td></td>
                                <td>{{ $file->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ nl2br($file->description) }}</td>
                                <td>{{ number_format($file->size/1024, 2, ",", ".") }}KB</td>
                                <td>{{ $file->user()->first()->name }}</td>
                                <td ><a class="btn btn-xs blue font-white tooltips" data-placement="top" data-original-title="Baixar" href="{{ url('/') }}/{{ $file->path }}" type="download" download="{{ $file->original_name }}" ><span  class="fa fa-download tooltips"></span></a></td>
                            </tr>
                        @endforeach
                        @foreach($cliente->documentosPlano() as $file)
                            <tr>
                                <td></td>
                                <td>{{ $file->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ nl2br($file->description) }}</td>
                                <td>{{ number_format($file->size/1024, 2, ",", ".") }}KB</td>
                                <td>{{ $file->user()->first()->name }}</td>
                                <td ><a class="btn btn-xs blue font-white tooltips" data-placement="top" data-original-title="Baixar" href="{{ url('/') }}/{{ $file->path }}" type="download" download="{{ $file->original_name }}" ><span  class="fa fa-download tooltips"></span></a></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td>{{ (new \Carbon\Carbon())->format('d/m/Y H:i:s') }}</td>
                            <td>Minuta de contrato</td>
                            <td>- KB</td>
                            <td>Lifepet Saúde</td>
                            <td ><a class="btn btn-xs blue font-white tooltips" data-placement="top" data-original-title="Baixar" href="{{ route('cliente.contrato') }}" type="download" download="minuta_contrato.pdf" ><span  class="fa fa-download tooltips"></span></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection