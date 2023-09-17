
<!-- Id Plano Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_plano">
        Plano
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_plano" class="select2 form-control"  id="">
            <option value=""></option>
            @foreach(\App\Models\Planos::where('ativo', 1)->get() as $plano)
                <option value="{{ $plano->id }}">{{ $plano->id }} - {{ $plano->nome_plano }}</option>
            @endforeach
        </select>
    </div>
</div>
<!-- Codigo Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="codigo">
        Quantidade de pets
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="number" name="pets" data-required="1" required class="form-control" min="1" step="1"/>
    </div>
</div>


<!-- Desconto Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="desconto">
        Preço
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="number" name="preco" required data-required="1" class="form-control" />
    </div>
</div>

<!-- Desconto Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="desconto">
        Usuário
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ auth()->user()->name }}" readonly required data-required="1" class="form-control" />
    </div>
</div>