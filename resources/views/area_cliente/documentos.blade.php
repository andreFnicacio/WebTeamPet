@extends('layouts.app')
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
    <div class="portlet  light  portlet-form " id="arquivos">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-folder-open-o font-red-sunglo"></i>
                <span class="caption-subject font-red-sunglo sbold uppercase">Arquivos</span>
            </div>
            <div class="actions">
                <div class="row">
                    <div class="col-md-offset-0 col-md-12">

                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-responsive">
                <table class="table col-md-12 historico-financeiro">
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