<!-- Id Cliente Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_cliente">
        Cliente
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" class="form-control" readonly="" value="{{ $renovacao->cliente->nome_cliente }}">
    </div>
</div>

<!-- Id Pet Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_pet">
        Pet
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" class="form-control" readonly="" value="{{ $renovacao->pet->nome_pet }}">
    </div>
</div>

<!-- Id Plano Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_plano">
        Plano
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_plano" class="form-control select2" id="id_plano">
            @foreach($planos as $plano)
                <option value="{{ $plano->id }}" {{ $plano->ativo ? '' : 'disabled' }} {{ $plano->id == $renovacao->plano->id ? 'selected' : '' }}>{{ $plano->id }} - {{ $plano->nome_plano }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Status Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="status">
        Status
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="status" class="form-control select2" id="status">
            <option value="{{ \App\Models\Renovacao::STATUS_NOVO }}">Novo</option>
            <option value="{{ \App\Models\Renovacao::STATUS_EM_NEGOCIACAO }}">Em negociação</option>
            <option value="{{ \App\Models\Renovacao::STATUS_PAGO }}">Pago</option>
            <option value="{{ \App\Models\Renovacao::STATUS_CANCELADO }}">Cancelado</option>
        </select>
    </div>
</div>

<!-- Id Link Pagamento Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_link_pagamento">
        Link de pagamento
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        @if($renovacao->link)
        <a href="{{ $renovacao->link->link() }}" target="_blank">{{ $renovacao->link->link() }}</a>
        @else
            <span>Link de pagamento não encontrado</span>
        @endif
    </div>
</div>


<!-- Regime Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="regime">
        Regime
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select class="select2 form-control" name="regime">
            <option value="ANUAL" {{ $renovacao->regime === \App\Models\Pets::REGIME_ANUAL ? 'selected' : '' }}>ANUAL</option>
            <option value="MENSAL" {{ $renovacao->regime === \App\Models\Pets::REGIME_MENSAL ? 'selected' : '' }}>MENSAL</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="valor">
        Valor pagamento
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" class="form-control" name="valor" value="{{ $renovacao->valor }}">
        <small>Alterar o valor gerará um novo link de pagamento com o valor atualizado.</small>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="parcelas">
        Parcelas
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="number" min="1" max="12" class="form-control" name="parcelas" value="{{ $renovacao->link ? $renovacao->link->parcelas : 1}}">
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="justificativa">
        Justificativa
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <textarea required class="form-control" name="justificativa"></textarea>
        <small>Necessário informar a circunstância da alteração.</small>
    </div>
</div>
<!-- Submit Field -->
<!--
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('renovacao.index') !!}" class="btn btn-default">Cancel</a>
</div>
-->
