<!-- Id Cliente Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_cliente">
        Cliente
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_cliente" id="id_cliente" class="form-control select2">
            <option value=""></option>
            @foreach(\App\Models\Clientes::orderBy('nome_cliente')->get() as $cliente)
                <option value="{{ $cliente->id }}" {{ $cliente->id == $cobrancas->id_cliente ? "selected" : "" }}>{{ $cliente->id }} - {{ $cliente->nome_cliente }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Competencia Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="competencia">
        Competencia
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="competencia" {!! \App\Helpers\Utils::value($cobrancas, 'competencia')  !!} data-required="1" class="form-control" />
    </div>
</div>

<!-- Valor Original Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="valor_original">
        Valor Original
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="valor_original" {!! \App\Helpers\Utils::value($cobrancas, 'valor_original') !!} data-required="1" class="form-control" />
    </div>
</div>

<!-- Data Vencimento Field -->
<div class="form-group">
    <label class="control-label col-md-3">Data Vencimento
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
       <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">

            <input type="text" name='data_vencimento' class="form-control" readonly value="{{ $cobrancas->data_vencimento ? $cobrancas->data_vencimento->format('d/m/Y') : '' }}">
            <span class="input-group-btn">
                <button class="btn default" type="button">
                    <i class="fa fa-calendar"></i>
                </button>
            </span>
        </div>
    </div>
</div>

<!-- Complemento Field -->
<div class="form-group col-sm-12 col-lg-12">
    <label class="control-label col-md-3">Complemento

    </label>
    <div class="col-md-4">
        {!! Form::textarea('complemento', null, ['class' => 'form-control', 'rows' => 3]) !!}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Status
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="status" id="status" class="select2 form-control">
            <option value="0" {{ $cobrancas->status == 0  ? "selected" : "" }}>CANCELADO</option>
            <option value="1" {{ $cobrancas->status == 1  ? "selected" : "" }}>ATIVO</option>
        </select>
    </div>
</div>

<!-- Submit Field -->
<!--
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('cobrancas.index') !!}" class="btn btn-default">Cancel</a>
</div>
-->
