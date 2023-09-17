<!-- Nome Urh Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="nome_urh">
        Nome
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input value="{!! $urh->nome_urh !!}" type="text" name="nome_urh" data-required="1" class="form-control" required />
    </div>
</div>

<!-- Valor Urh Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="valor_urh">
        Valor
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input value="{!! number_format($urh->valor_urh, 2, ',', '') !!}" type="text" name="valor_urh" data-required="1" class="form-control money" required />
    </div>
</div>

@if($urh->ativo !== null)
    <div class="form-group">
        <label class="control-label col-md-3">Ativo
            <span class="required"> * </span>
        </label>
        <div class="col-md-4">
            {{ Form::hidden('ativo',0) }}
            <input type="checkbox" {{ $urh->ativo ? "checked" : "" }} name="ativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
        </div>
    </div>
@endif