
<div class="form-group">
    <div class="col-sm-12">

        @if(\Request::route()->getName() == 'pets.edit')
            <div class="form-group">
                <label class="control-label col-md-3">
                </label>
                <div class="col-md-4 text-center">
                    @if(empty($pets->foto))
                        <div class="pet_avatar" style="background: url({{ $pets->avatar() }}) no-repeat center center / cover;width: 140px;height: 140px;background-size: cover;border-radius: 50%;margin: 0 auto;"></div>
                    @else
                        <a href="{{ $pets->avatar() }}" target="_blank">
                            <div class="pet_avatar" style="background: url({{ $pets->avatar() }}) no-repeat center center / cover;width: 140px;height: 140px;background-size: cover;border-radius: 50%;margin: 0 auto;"></div>
                        </a>
                    @endif
                    <h3><strong>{{ $pets->nome_pet }}</strong></h3>
                </div>
            </div>
            <br>
        @endif
        <div class="form-group">
            <label class="control-label col-md-3">Número interno do Pet
            </label>
            <div class="col-md-4">
                <input type="text" value="{{ $pets->id }}"  placeholder="Gerado Automaticamente" disabled  class="form-control" />
            </div>
        </div>
        @if(\Request::route()->getName() == 'pets.edit')
            <div class="form-group">
                <label class="control-label col-md-3">Ativo
                    <span class="required"> * </span>
                </label>
                <div class="col-md-4">
                    {{ Form::hidden('ativo',0) }}
                    <input readonly type="checkbox" {{ $pets->ativo ? "checked" : "" }} name="ativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
                </div>
            </div>
        @endif

        {{-- Change to save pet with active status default to false, pet should be active when the subscription is paid --}}
        {{ Form::hidden('ativo', 0) }}

        <div class="form-group">
            <label class="control-label col-md-3">Nome do Pet
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
                <input type="text" required value="{{ $pets->nome_pet }}" name="nome_pet" data-required="1" class="form-control" />
            </div>
        </div>
        @if(!\Entrust::hasRole(['CLIENTE']))
        <div class="form-group">
            <label class="control-label col-md-3">
                @if($pets->id_cliente)
                    <a href="{{ route('clientes.edit', $pets->id_cliente) }}">
                        Tutor
                    </a>
                @else
                    Tutor
                @endif
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
                <select id="id_cliente" name="id_cliente" required placeholder="Selecione um cadastro" class="form-control select2">
                    <option></option>
                    @foreach(\App\Models\Clientes::orderBy('nome_cliente', 'asc')->get() as $c)
                        <option
                                value="{{ $c->id }}"
                                {{ $c->id == $pets->id_cliente ? "selected" : "" }}
                        >{{ $c->id . " - " . $c->nome_cliente }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
        <div class="form-group">
            <label class="control-label col-md-3">Microchip
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
                <input type="text" value="{{ $pets->numero_microchip }}" name="numero_microchip" required="required" data-required="1" class="form-control" />
                <small>
                    Verifique corretamente o número digitado.
                </small>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3" for="tipo">Tipo
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
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
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3" for="sexo">Sexo
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
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
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Raça
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
                <select id="id_raca" name="id_raca" required placeholder="Selecione uma raça" class="form-control select2">
                    <option></option>
                    @foreach(\App\Models\Raca::orderBy('nome', 'asc')->get() as $r)
                        <option
                                value="{{ $r->id }}"
                                {{ $r->id == $pets->id_raca ? "selected" : "" }}
                        >{{ $r->nome . " - " . $r->tipo }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Data de Nasc.
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
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
        <div class="form-group">
            <label class="control-label col-md-3">Doenças Pré-existentes
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
                <input type="checkbox" {{ $pets->contem_doenca_pre_existente ? "checked" : "" }} name="contem_doenca_pre_existente" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Cite as doenças
                <span class="required"> </span>
            </label>
            <div class="col-md-4">
                <textarea name="doencas_pre_existentes" type="text" class="form-control">{{ $pets->doencas_pre_existentes }}</textarea>
            </div>
        </div>
        @if(!\Entrust::hasRole(['CLIENTE']))
        <div class="form-group">
            <label class="control-label col-md-3">Observações
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
                <textarea name="observacoes" type="text" class="form-control" >{{ $pets->observacoes }}</textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Id Externo
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
                <input type="text" value="{{ $pets->id_externo }}" name="id_externo" data-required="1" class="form-control" />
            </div>
        </div>
        @endif
    </div>
</div>
@if(!\Entrust::hasRole(['CLIENTE']))
<div class="form-group">
    <div class="col-md-12" >
        <div class="col-md-12" style="margin-bottom: 20px;">
            <h3 class="block" style="margin-top: 30px;">Dados de Cobrança</h3>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Participativo?
                <span class="required"> * </span>
            </label>
            <div class="col-md-4">
                {{Form::hidden('participativo',0)}}
                <input type="checkbox" name="participativo" {{ $pets->participativo ? "checked" : "" }} class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Conveniado

            </label>
            <div class="col-md-2">
                <select name="id_conveniado" id="id_conveniado" class="form-control">
                    <option value=""></option>
                    @foreach(\App\Models\Conveniados::all() as $conveniado)
                        <option value="{{ $conveniado->id }}" {{ $pets->id_conveniado === $conveniado->id ? "selected" : "" }}>{{ $conveniado->nome_conveniado }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Vencimento
                <span class="required"> * </span>
            </label>
            <div class="col-md-2">
                <select name="vencimento" id="vencimento" class="form-control" required>
                    @for($i = 1; $i < 32; $i++)
                        <option value="{{ $i }}" {{ $pets->vencimento === $i ? "selected" : "" }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Mês do Reajuste
                <span class="required"> * </span>
            </label>
            <div class="col-md-2">
                <select name="mes_reajuste" id="mes_reajuste" class="form-control" required>
                    <option value="">Selecione um mês</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $pets->mes_reajuste === $i ? "selected" : "" }}>{{ $i }} - {{ \App\Helpers\Utils::getMonthName($i) }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Regime

            </label>
            <div class="col-md-2">
                <select name="regime" id="regime" class="form-control">
                    @foreach(\App\Models\Pets::$regimes as $regime)
                        <option value="{{ $regime }}" {{ $pets->regime === $regime ? "selected=selected" : "" }}>{{ $regime }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3">Valor
                <span class="required"> * </span>
            </label>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon">R$</span>
                    <input name="valor" value="{{ $pets->valor }}" type="number" class="form-control" />
                </div>
            </div>
        </div>

    </div>
</div>
@endif


<!-- Nome Pet Field -->


<!-- Submit Field -->
<!--
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('pets.index') !!}" class="btn btn-default">Cancel</a>
</div>
-->
