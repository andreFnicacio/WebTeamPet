

<!-- BEGIN FORM-->
 {!! Form::model($pets, [
    'route' => [
        'pets.update',
        $pets->id
    ],
    'method' => 'patch',
    
    'id' => 'pets'
]);
!!}

<div class='mb-2'>
    <div class="actions text-right" data-target="#pets">
        <div class="btn-group btn-group-devided" data-toggle="buttons">
            <button type="submit" id="save" class="btn green-jungle">Salvar</button>
            <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
        </div>
    </div>
</div>
<form>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-paw"></i>Dados do pet
        </div>

        

    </div>
    <div class="portlet-body">
        <div class="row">
            
            <div class="form-group col-md-10">
                <label class="control-label">Número interno do Pet
                </label>
    
                <input type="text" value="{{ $pets->id }}"  placeholder="Gerado Automaticamente" disabled  class="form-control" />
            
            </div>
            
            @if(\Request::route()->getName() == 'pets.edit')
                <div class="form-group col-md-2">
                    <label class="control-label">Ativo
                        <span class="required"> * </span>
                    </label>

                    {{ Form::hidden('ativo',0) }}
                    <div>
                        <input readonly type="checkbox" {{ $pets->ativo ? "checked" : "" }} name="ativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
                    </div>
                </div>
            @endif
    

            <div class="form-group col-md-12">
                <label class="control-label">Nome do Pet
                    <span class="required"> * </span>
                </label>
                <input type="text" required value="{{ $pets->nome_pet }}" name="nome_pet" data-required="1" class="form-control" />
            </div>
        

            @if(!\Entrust::hasRole(['CLIENTE']))
            <div class="form-group col-md-6">
                <label class="control-label">
                    @if($pets->id_cliente)
                        <a href="{{ route('clientes.edit', $pets->id_cliente) }}">
                            Tutor
                        </a>
                    @else
                        Tutor
                    @endif
                    <span class="required"> * </span>
                </label>
                <div>
                    <select id="id_cliente" name="id_cliente" required placeholder="Selecione um cadastro" class="form-control select2">
                        <option></option>
                        @foreach(\App\Models\Clientes::orderBy('nome_cliente', 'asc')->get() as $c)
                            <option
                                    value="{{ $c->id }}"
                                    {{ $c->id == $pets->id_cliente ? "selected" : "" }}
                            >{{ $c->id . " - " . $c->nome_cliente }}</option>
                        @endforeach
                    </select>
                    <small >
                        &nbsp;
                    </small>
                </div>
            </div>
            @endif

            <div class="form-group col-md-4">
                <label class="control-label">Microchip
                    <span class="required"> * </span>
                </label>
                <div>
                    <input type="text" value="{{ $pets->numero_microchip }}" name="numero_microchip" required="required" data-required="1" class="form-control" />
                    <small >
                        Verifique corretamente o número digitado.
                    </small>
                </div>
            </div>
            <div class="form-group col-md-2">
                <label class="control-label">
                    <span class="required">  </span>
                </label>
                <div>
                    @if($microchip)
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalExemplo">
                            Add
                        </button>
                    @endif
                </div>
            </div>
            <div class="form-group col-md-4">
                <label class="control-label" for="tipo">Tipo
                    <span class="required"> * </span>
                </label>
                <div>
                    <select required name="tipo" id="tipo" class="form-control">
                        @foreach([
                            'CACHORRO' => 'Cachorro',
                            'GATO'     => 'Gato =^.^='
                        ] as $value => $option)
                            <option value="{{ $value }}" {{ strtoupper($value) === strtoupper($pets->tipo) ? "selected" : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    <small >
                        &nbsp;
                    </small>
                </div>
                
            </div>

            <div class="form-group col-md-4">
                <label class="control-label" for="sexo">Sexo
                    <span class="required"> * </span>
                </label>
                <div>
                    <select required name="sexo" id="sexo" class="form-control">
                        @foreach([
                            'ND' => 'Não Declarado',
                            'M'     => 'Macho',
                            'F'     => 'Fêmea'
                        ] as $value => $option)
                            <option value="{{ $value }}" {{ strtoupper($value) === strtoupper($pets->sexo) ? "selected" : '' }} {{ strtoupper($value) === "ND" ? "disabled" : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    <small >
                        &nbsp;
                    </small>
                </div>
            </div>

            <div class="form-group col-md-4">
                <label class="control-label">Raça
                    <span class="required"> * </span>
                </label>
                <div>
                    <select id="id_raca" name="id_raca" required placeholder="Selecione uma raça" class="form-control select2">
                        <option></option>
                        @foreach(\App\Models\Raca::orderBy('nome', 'asc')->get() as $r)
                            <option
                                    value="{{ $r->id }}"
                                    {{ $r->id == $pets->id_raca ? "selected" : "" }}
                            >{{ $r->nome . " - " . $r->tipo }}</option>
                        @endforeach
                    </select>
                    <small >
                        &nbsp;
                    </small>
                </div>
            </div>

            <div class="form-group col-md-4">
                <label class="control-label">Data de Nasc.
                    <span class="required"> * </span>
                </label>
                <div>
                    <div required class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                        <input required type="text" value="{{ $pets->data_nascimento ? $pets->data_nascimento->format('d/m/Y') : ""}}" name="data_nascimento" class="form-control" readonly>
                        <span class="input-group-btn">
                            <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-3">
                <label class="control-label">Doenças Pré-existentes
                    <span class="required"> * </span>
                </label>
                <div>
                    <input type="checkbox" {{ $pets->contem_doenca_pre_existente ? "checked" : "" }} name="contem_doenca_pre_existente" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não">
                </div>
            </div>
            <div class="form-group col-md-2">
                <label class="control-label">Id Externo
                    <span class="required"> * </span>
                </label>
                <div>
                    <input type="text" value="{{ $pets->id_externo }}" name="id_externo" data-required="1" class="form-control" />
                </div>
            </div>
            @if(!\Entrust::hasRole(['CLIENTE']))
            <div class="form-group col-md-12">
                <label class="control-label">Observações
                    <span class="required"> * </span>
                </label>
                <div>
                    <textarea name="observacoes" type="text" class="form-control" >{{ $pets->observacoes }}</textarea>
                </div>
            </div>
            @endif
            
                <!-- Nome Pet Field -->


                <!-- Submit Field -->

                <div class="form-group col-sm-12">
                    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
                    <a href="{!! route('pets.index') !!}" class="btn btn-default">Cancel</a>
                </div>

        </div>
    </div>

</div>
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-money"></i>Dados de cobrança
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                @if(!\Entrust::hasRole(['CLIENTE']))
                    {{--<div class="form-group col-md-2">--}}
                    {{--<label class="control-label">Participativo?--}}
                    {{--<span class="required"> * </span>--}}
                    {{--</label>--}}
                    {{--<div >--}}
                    {{--{{Form::hidden('participativo',0)}}--}}
                    {{--<input type="checkbox" name="participativo" {{ $pets->participativo ? "checked" : "" }} class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group col-md-5">--}}
                    {{--<label class="control-label">Conveniado--}}

                    {{--</label>--}}
                    {{--<div>--}}
                    {{--<select name="id_conveniado" id="id_conveniado" class="form-control">--}}
                    {{--<option value=""></option>--}}
                    {{--@foreach(\App\Models\Conveniados::all() as $conveniado)--}}
                    {{--<option value="{{ $conveniado->id }}" {{ $pets->id_conveniado === $conveniado->id ? "selected" : "" }}>{{ $conveniado->nome_conveniado }}</option>--}}
                    {{--@endforeach--}}
                    {{--</select>--}}
                    {{--</div>--}}
                    {{--</div>--}}

                    <div class="form-group col-md-5">
                        <label class="control-label">Regime

                        </label>
                        <div>
                            <select name="regime" id="regime" class="form-control">
                                @foreach(\App\Models\Pets::$regimes as $regime)
                                    <option value="{{ $regime }}" {{ $pets->regime === $regime ? "selected=selected" : "" }}>{{ $regime }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{--<div class="form-group col-md-4">--}}
                    {{--<label class="control-label">Vencimento--}}
                    {{--<span class="required"> * </span>--}}
                    {{--</label>--}}
                    {{--<div>--}}
                    {{--<select name="vencimento" id="vencimento" class="form-control" required>--}}
                    {{--@for($i = 1; $i < 32; $i++)--}}
                    {{--<option value="{{ $i }}" {{ $pets->vencimento === $i ? "selected" : "" }}>{{ $i }}</option>--}}
                    {{--@endfor--}}
                    {{--</select>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group col-md-4">
                        <label class="control-label">Mês do Reajuste
                            <span class="required"> * </span>
                        </label>
                        <div>
                            <select name="mes_reajuste" id="mes_reajuste" class="form-control" required>
                                <option value="">Selecione um mês</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $pets->mes_reajuste === $i ? "selected" : "" }}>{{ $i }} - {{ \App\Helpers\Utils::getMonthName($i) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{--<div class="form-group  col-md-4">--}}
                    {{--<label class="control-label">Valor--}}
                    {{--<span class="required"> * </span>--}}
                    {{--</label>--}}
                    {{--<div>--}}
                    {{--<div class="input-group">--}}
                    {{--<span class="input-group-addon">R$</span>--}}
                    {{--<input name="valor" value="{!! number_format($pets->valor, 2, ',', '') !!}"" type="text" class="form-control money" />--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                @endif

            </div>
        </div>
    </div>
</form>

<div class='mb-2'>
    <div class="actions text-right" data-target="#pets">
        <div class="btn-group btn-group-devided" data-toggle="buttons">
            <button type="submit" id="save" class="btn green-jungle">Salvar</button>
            <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExemplo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cadastro do microchip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label class="control-label">Microchip
                                <span class="required"> * </span>
                            </label>
                            <div>
                                <input type="text" value="{{ $pets->numero_microchip }}" name="numero_microchip" required="required" data-required="1" class="form-control" />
                                <small >
                                    Verifique corretamente o número digitado.
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Salvar mudanças</button>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}