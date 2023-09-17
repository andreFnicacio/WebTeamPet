<!-- Nome Grupo Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="nome_grupo">
        Nome
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" {{ $grupos->id ? "readonly=readonly" : "" }} value="{{ $grupos->nome_grupo }}" name="nome_grupo" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Submit Field -->
<!--
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('grupos.index') !!}" class="btn btn-default">Cancel</a>
</div>
-->
