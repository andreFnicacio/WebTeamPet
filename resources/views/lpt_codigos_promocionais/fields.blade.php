
<!-- Id Plano Field -->
@if(!$lptCodigosPromocionais || !$lptCodigosPromocionais->plano)
<div class="form-group">
    <label class="control-label col-md-3" for="id_plano">
        Plano
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_plano" class="select2 form-control"  id="" required>
            <option value=""></option>
            @foreach(\App\Models\Planos::lpt()->get() as $lpt)
                <option value="{{ $lpt->id }}">{{ $lpt->id }} - {{ $lpt->nome_plano }}</option>
            @endforeach
        </select>
    </div>
</div>
@else
    <div class="form-group">
        <label class="control-label col-md-3" for="id_plano">
            Plano:
        </label>
        <div class="col-md-4">
            <strong class="info-only">{{ $lptCodigosPromocionais->plano->id . ' - ' . $lptCodigosPromocionais->plano->nome_plano }}</strong>
        </div>
    </div>
@endif

@if(!$lptCodigosPromocionais)
<!-- Codigo Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="codigo">
        Codigo
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="codigo" data-required="1" class="form-control" required/>
    </div>
</div>
@else
    <div class="form-group">
        <label class="control-label col-md-3" for="id_plano">
            Código:
        </label>
        <div class="col-md-4">
            <strong class="info-only">{{ $lptCodigosPromocionais->codigo }}</strong>
        </div>
    </div>
@endif

@if(!$lptCodigosPromocionais)
<div class="form-group">
    <label class="control-label col-md-3" for="id_plano">
        Tipo de desconto
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="tipo_desconto" class="select2 form-control" required>
            <option value=""></option>
            <option value="percentual">PERCENTUAL (%)</option>
            <option value="fixo">FIXO ($)</option>
        </select>
    </div>
</div>
@else
    <div class="form-group">
        <label class="control-label col-md-3" for="id_plano">
            Tipo de desconto:
        </label>
        <div class="col-md-4">
            <strong class="info-only">{{ strtoupper($lptCodigosPromocionais->tipo_desconto) }}</strong>
        </div>
    </div>
@endif

@if(!$lptCodigosPromocionais)
<!-- Desconto Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="desconto">
        Valor de desconto
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="desconto" data-required="1" class="form-control" required/>
    </div>
</div>
@else
    <div class="form-group">
        <label class="control-label col-md-3" for="id_plano">
            Valor de desconto:
        </label>
        <div class="col-md-4">
            <strong class="info-only">{{ $lptCodigosPromocionais->tipo_desconto === 'percentual' ? $lptCodigosPromocionais->desconto . '%' : \App\Helpers\Utils::money($lptCodigosPromocionais->desconto) }}</strong>
        </div>
    </div>
@endif
<!-- Expira Em Field -->
<div class="form-group">
    <label class="control-label col-md-3">Expira Em
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
            <input type="text" name='expira_em' class="form-control" value="{{ $lptCodigosPromocionais ? $lptCodigosPromocionais->expira_em->format('d/m/Y') : '' }}" readonly required>
            <span class="input-group-btn">
            <button class="btn default" type="button">
                <i class="fa fa-calendar"></i>
            </button>
        </span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="id_plano">
        Aplicabilidade de regime
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="aplicabilidade" class="select2 form-control" required>
            <option value="T" {{ ($lptCodigosPromocionais && $lptCodigosPromocionais->aplicabilidade) === 'T' ? 'selected' : '' }}>Todos</option>
            <option value="A" {{ ($lptCodigosPromocionais && $lptCodigosPromocionais->aplicabilidade) === 'A' ? 'selected' : '' }}>Anual</option>
            <option value="M" {{ ($lptCodigosPromocionais && $lptCodigosPromocionais->aplicabilidade) === 'M' ? 'selected' : '' }}>Mensal</option>
        </select>
    </div>
</div>

@if(!$lptCodigosPromocionais)
<div class="form-group">
    <label class="control-label col-md-3">Desconto permanente?
        <span class="required"> * </span>
    </label>
    <div class="col-md-6" data-toggle="tooltip" data-original-title="Define se o desconto permanece nas parcelas do plano">
        {{Form::hidden('permanente', 0)}}
        <input type="checkbox" name="permanente" value="1" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1"  >
    </div>
</div>
@else
    <div class="form-group">
        <label class="control-label col-md-3" for="id_plano">
            Desconto permanente?
        </label>
        <div class="col-md-4">
            <strong class="info-only">{{ $lptCodigosPromocionais->permanente ? 'SIM' : 'NÃO' }}</strong>
        </div>
    </div>
@endif