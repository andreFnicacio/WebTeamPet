<!-- Id Cliente Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_cliente">
        Cliente
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_cliente" class="form-control select2" id="id_cliente">
            @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}">{{ $cliente->id }} - {{ $cliente->nome_cliente }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Id Pet Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_pet">
        Pet
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_pet" disabled class="form-control select2" id="id_pet">

        </select>
    </div>
</div>

<!-- Id Plano Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_plano">
        Plano
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_plano" disabled class="form-control select2" id="id_plano">
            <option value=""></option>
            @foreach($planos as $plano)
                <option value="{{ $plano->id }}" {{ $plano->ativo ? '' : 'disabled' }}>{{ $plano->id }} - {{ $plano->nome_plano }}</option>
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
        <input type="text" name="status" data-required="1" class="form-control" /> 
    </div>
</div>

<!-- Id Link Pagamento Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_link_pagamento">
        Link de pagamento
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="id_link_pagamento" data-required="1" class="form-control" />
    </div>
</div>


<!-- Regime Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="regime">
        Regime
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select class="select2 form-control">
            <option value="ANUAL">ANUAL</option>
            <option value="MENSAL">MENSAL</option>
        </select>
    </div>
</div>

<!-- Submit Field -->
<!--
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('renovacao.index') !!}" class="btn btn-default">Cancel</a>
</div>
-->
