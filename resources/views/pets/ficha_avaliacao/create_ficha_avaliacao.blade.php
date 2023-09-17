@extends('layouts.app')

@section('title')
    @parent
    Pets - Ficha de Avaliação
@endsection

@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-file-text-o font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                    Ficha de Avaliação
                </span>
            </div>
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            <form action="{{ route('pets.fichaAvaliacaoStore', ["idPet" => $pet->id]) }}" method="post" class="form-horizontal" role="form">
                {{ csrf_field() }}

                <input type="hidden" name="id_pet" value="{{ $pet->id }}">
                <input type="hidden" name="id_clinica" value="{{ $clinica->id }}">

                <div class="form-body">

                    <div class="row">
                        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="portlet blue box">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-paw"></i>
                                                Dados do Pet
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Pet:</label>
                                                    <p>{!! $pet->nome_pet !!}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Espécie:</label>
                                                    <p>{!! $pet->tipo == "CACHORRO" ? "Canina" : "Felina" !!}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Raça:</label>
                                                    <p>{!! $pet->raca->nome !!}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Tutor:</label>
                                                    <p>{!! $pet->cliente->nome_cliente !!}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Nascimento:</label>
                                                    <p>{!! $pet->data_nascimento->format('d/m/Y') !!}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-6 col-md-4">
                                                    <label class="bold">Data:</label>
                                                    <p>{!! (new \Carbon\Carbon())->today()->format('d/m/Y') !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="portlet blue box">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-info-circle"></i>
                                        Informações Adicionais
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="bold">Porte <span class="font-red">*</span></label>

                                            <select name="porte" class="select2" required>
                                                <option value=""></option>
                                                <option value="PEQUENO">Pequeno</option>
                                                <option value="MÉDIO">Médio</option>
                                                <option value="GRANDE">Grande</option>
                                                <option value="GIGANTE">Gigante</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="bold">Pelagem <span class="font-red">*</span></label>
                                            <input name="pelagem" type="text" class="form-control" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="bold">Microchip <span class="font-red">*</span></label>
                                            <input name="numero_microchip" type="text" class="form-control numero_microchip" {{ $is_free ? "value=PF".$pet->id." readonly" : '' }} required maxlength="15" minlength="15">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="portlet blue box">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-info-circle"></i>
                                        Anamnese
                                    </div>
                                </div>
                                <div class="portlet-body">

                                    @php
                                        $i = 0;
                                        $j = 0;
                                    @endphp
                                    @foreach($perguntas as $categoria => $pergunta)

                                        <h5 class="margin-bottom-40"><strong>{!! $categoria !!}</strong></h5>

                                        @foreach($perguntas[$categoria] as $pergunta)
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <label>
                                                        {!! $pergunta->nome_pergunta !!}
                                                        <input type="hidden" name="respostas[{{ $i }}][id_pergunta]" value="{{ $pergunta->id }}">
                                                        @if($pergunta->helper)
                                                            <i class="fa fa-question-circle font-lg" data-toggle="tooltip" title="{!! $pergunta->helper !!}"></i>
                                                        @endif
                                                    </label>
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-3">
                                                            <div class="mt-radio-inline">
                                                                <label class="mt-radio">
                                                                    <input type="radio" name="respostas[{{ $i }}][resposta]" value="1"> Sim
                                                                    <span></span>
                                                                </label>
                                                                <label class="mt-radio">
                                                                    <input type="radio" name="respostas[{{ $i }}][resposta]" value="0"> Não
                                                                    <span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-9">
                                                            <div class="form-group form-md-line-input ">
                                                                <input type="text" name="respostas[{{ $i }}][descricao]" class="form-control" placeholder="Descreva...">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @php
                                                $i++;
                                            @endphp

                                        @endforeach

                                        @php
                                            $j++;
                                        @endphp

                                        @if($j < count($perguntas))
                                            <hr>
                                        @endif

                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-lg green btn-block">SALVAR</button>

                        </div>
                    </div>

                </div>
            </form>
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
@endsection

@if(!$is_free)
    @section('scripts')
        @parent
        <script>
            $('.numero_microchip').mask('000000000000000');
            $('.numero_microchip').on('blur', function(e) {
                if ($(this).val().length > 0 && $(this).val().length < 15) {
                    swal('Atenção', 'O número do microchip deve conter 15 dígitos!', 'warning');
                    $(this).val('').focus();
                }
            });
        </script>
    @endsection
@endif
