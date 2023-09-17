@extends('layouts.app')

@section('title')
    @parent
    Emissor de guias - Realizar Guia
@endsection
@section('content')
    @include('common.swal')

    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">Realizar Guia
                                        </span>
            </div>
            <div class="actions">
                <div class="btn-group btn-group-devided" data-toggle="buttons">

                    <button type="submit" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>


        </div>
        <div class="portlet-body" id="emissor-guia">
            <!-- BEGIN FORM-->

            <form action="{{ route('autorizador.realizar') }}" class="form-horizontal" method="POST">
                <div class="form-group">
                    <label class="control-label col-md-3">Credenciada:
                    </label>
                    <div class="col-sm-5">
                        <span>{{ $guia->clinica->nome_clinica }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Encaminhamento / Liberação:
                    </label>
                    <div class="col-sm-5">
                        <span>{{ $guia->created_at->format('d/m/Y') }} / {{ $guia->data_liberacao ? $guia->data_liberacao : " - " }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Solicitante:
                    </label>
                    <div class="col-sm-5">
                        <span>{{ $guia->solicitante ? $guia->solicitante->nome_clinica : "-" }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Tipo de atendimento:
                    </label>
                    <div class="col-sm-5">
                        <span>{{ $guia->tipo_atendimento }}</span>
                    </div>
                </div>
                {{ csrf_field() }}
                <input type="hidden" name="numero_guia" value="{{ $numero_guia }}">
                <div class="form-group">
                    <label class="control-label col-md-3">Prestador:
                        <span class="required"> * </span>
                    </label>
                    <div class="col-md-5">
                        <select id="prestadores" required name="id_prestador" placeholder="Selecione um cadastro"
                                class="form-control select2">
                            <option></option>
                            @foreach(\Modules\Veterinaries\Entities\Prestadores::all() as $prestador)
                                <option value="{{ $prestador->id }}">{{ $prestador->id }}
                                    - {{ $prestador->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Laudo: <span class="required"> * </span>
                    </label>
                    <div class="col-md-5">
                        <textarea name="laudo" id="" rows="5" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">
                    </label>
                    <div class="col-md-5">
                        <button type="submit" class="btn green-jungle">SALVAR</button>
                    </div>

                </div>
            </form>
            <br>
            <!-- END FORM-->
        </div>
    </div>

@endsection

@section('scripts')
    @parent

@endsection
