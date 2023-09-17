


@if(\Request::route()->getName() == 'pets.edit')
    <div class="form-group">
        <div class="col-md-12" >
            <div class="col-md-12" style="margin-bottom: 20px;">
                <h3 class="block" style="margin-top: 30px;">Dados do plano atual</h3>
            </div>
            @if(!empty($petsPlanos)  && empty($petsPlanos->data_encerramento_contrato))
                <input type="hidden" name="id_pets_planos" value="{{ $petsPlanos->id }}">
            @endif
            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3">Cadastro Ativo?--}}
                    {{--<span class="required"> * </span>--}}
                {{--</label>--}}
                {{--<div class="col-md-4">--}}
                    {{--<input type="checkbox" name="ativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Ativo" data-off-text="Cancelado">--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--<label class="control-label col-md-3">Status Financeiro--}}
                    {{--<span class="required"> * </span>--}}
                {{--</label>--}}
                {{--<div class="col-md-4">--}}
                    {{--<span class="label label-sm label-success"> Contrato em dia </span>--}}
                    {{--<span class="label label-sm label-warning"> Em atraso </span>--}}
                    {{--<span class="label label-sm label-danger"> Inadimplente +60 dias </span>--}}
                    {{--<span class="label label-sm label-info"> Indefinido </span>--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="form-group">
                <label class="control-label col-md-3">Plano
                    <span class="required"> * </span>
                </label>
                <div class="col-md-4">
                    <select name="id_plano" data-previous="{{$planoAtual->id}}" id="single" placeholder="Selecione um cadastro" class="form-control select2">
                        <option></option>
                        @foreach(\App\Models\Planos::orderBy('ativo', 'desc')->get() as $plano)
                            <option value="{{ $plano->id }}"
                                    {{ (!empty($planoAtual) && $plano->id == $planoAtual->id) ? "selected" : (!$plano->ativo ? "disabled" : "") }}
                            >
                            {{ $plano->id }}-{{ $plano->nome_plano }} - Ind: R${{ number_format($plano->preco_plano_individual) }} / Fam: R${{ number_format($plano->preco_plano_familiar) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Vendedor</label>
                <div class="col-md-4">
                    <select name="id_vendedor" id="vendedor" placeholder="Selecione um cadastro" class="form-control">
                        <option></option>
                        @foreach(\App\Models\Vendedores::all() as $vendedor)
                            <option value="{{ $vendedor->id }}" data-image="{{ route('vendedores.avatar', $vendedor->id) }}" {{ $petsPlanos->id_vendedor == $vendedor->id ? "selected" : "" }}>
                                {{ $vendedor->id }} - {{ $vendedor->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{--@if(!empty($planoAtual->id))--}}
                <div class="form-group form-status">
                    <label class="control-label col-md-3" for="sexo">Status
                        <span class="required"> * </span>
                    </label>
                    <div class="col-md-4">
                        <select required name="status" id="status" class="form-control">
                            @if(!empty($primeiroPlano))
                                @foreach(\App\Models\PetsPlanos::STATUS as $key => $status)
                                    @if($key !== \App\Models\PetsPlanos::STATUS_PRIMEIRO_PLANO)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endif
                                @endforeach
                            @else
                                <option value="{{ \App\Models\PetsPlanos::STATUS_PRIMEIRO_PLANO }}">{{ \App\Models\PetsPlanos::STATUS[\App\Models\PetsPlanos::STATUS_PRIMEIRO_PLANO] }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            {{--@endif--}}

            <div class="form-group">
                <label class="control-label col-md-3">Plano Familiar?
                    <span class="required"> * </span>
                </label>
                <div class="col-md-4">
                    <input type="checkbox" {{ $pets->familiar ? "checked" : "" }} name="familiar" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Data Inicial do Contrato
                    <span class="required"> * </span>
                </label>
                <div class="col-md-4">
                    <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                        <input value="{{ $petsPlanos->data_inicio_contrato ? $petsPlanos->data_inicio_contrato->format('d/m/Y') : (new \Carbon\Carbon())->format('d/m/Y') }}" type="text" class="form-control" name="data_inicio_contrato" {{ $petsPlanos->data_inicio_contrato ? "readonly" : "" }} required>
                        <span class="input-group-btn">
                           <button class="btn default" type="button">
                            <i class="fa fa-calendar"></i>
                           </button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Adesão
                    <span class="required"> </span>
                </label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">R$</span>
                        <input name="adesao" value="{{ $petsPlanos->adesao }}" type="number" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3">Valor do Plano
                    <span class="required"> </span>
                </label>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">R$</span>
                        <input name="valor_plano" value="{{ $petsPlanos->valor_momento }}" type="number" class="form-control" />
                    </div>
                </div>
            </div>
            <!--
            <div class="form-group">
                <label class="control-label col-md-3">Encerramento
                    <span class="required"> </span>
                </label>
                <div class="col-md-4">
                    <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                        <input value="{{ $petsPlanos->data_encerramento_contrato ?: "" }}" name="data_encerramento_contrato" type="text" class="form-control" {{ $petsPlanos->data_encerramento_contrato ? "readonly" : "" }}>
                        <span class="input-group-btn">
                       <button class="btn default" type="button">
                        <i class="fa fa-calendar"></i>
                       </button>
                    </span>
                    </div>
                </div>
            </div>
            -->
        </div>
    </div>
@endif

@section('scripts')
    @parent
    {{--<script>--}}
        {{--$('#single').change(function () {--}}
            {{--if ($( this ).val() != $( this ).attr('data-previous')) {--}}
                {{--$('.form-status').show();--}}
            {{--} else {--}}
                {{--$('.form-status').hide();--}}
            {{--}--}}
        {{--});--}}
    {{--</script>--}}
@endsection
