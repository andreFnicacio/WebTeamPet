<!-- Chave Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="chave">
        Chave
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="chave" data-required="1" class="form-control" /> 
    </div>
</div>

<!-- Valor Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="valor">
        Valor
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="valor" data-required="1" class="form-control" /> 
    </div>
</div>

<!-- Tipo Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="tipo">
        Tipo
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="tipo" data-required="1" class="form-control" /> 
    </div>
</div>

<!-- Descricao Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('descricao', 'Descricao:') !!}
    {!! Form::textarea('descricao', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<!--
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('parametros.index') !!}" class="btn btn-default">Cancel</a>
</div>
-->
