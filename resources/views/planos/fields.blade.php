<!-- Nome Plano Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="nome_plano">
        Nome do Plano
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input value="{{ $planos->nome_plano }}" type="text" name="nome_plano" data-required="1" class="form-control" required="required"/>
    </div>
</div>

<!-- Preco Plano Familiar Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="preco_plano_familiar">
        Preco Plano Familiar
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input value="{{ $planos->preco_plano_familiar }}" type="text" name="preco_plano_familiar" data-required="1" class="form-control" required />
    </div>
</div>

<!-- Preco Plano Individual Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="preco_plano_individual">
        Preco Plano Individual
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input value="{{ $planos->preco_plano_individual }}" type="text" name="preco_plano_individual" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Teto Participativo Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="preco_plano_individual">
        Teto Participativo (< 2020)
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input value="{{ $planos->teto_participativo }}" type="text" name="teto_participativo" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Pontos Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="preco_plano_individual">
        Pontos
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input value="{{ $planos->pontos }}" type="text" name="pontos" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Data Vigencia Field -->
<div class="form-group">
    <label class="control-label col-md-3">Data de Vigencia
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
       <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
        <input type="text" value="{{ $planos->data_vigencia ? $planos->data_vigencia->format('d/m/Y') : "" }}" name='data_vigencia' class="form-control" readonly required>
        <span class="input-group-btn">
            <button class="btn default" type="button">
                <i class="fa fa-calendar"></i>
            </button>
        </span>
       </div>
    </div>
</div>
<!-- Data Inatividade Field -->
<div class="form-group">
    <label class="control-label col-md-3">Data de Inatividade
    </label>
    <div class="col-md-4">
       <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
            <input type="text" value="{{ $planos->data_inatividade ? $planos->data_inatividade->format('d/m/Y') : "" }}" name='data_inatividade' class="form-control" readonly>
            <span class="input-group-btn">
                <button class="btn default" type="button">
                    <i class="fa fa-calendar"></i>
                </button>
            </span>
       </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Ativo?
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="hidden" name="ativo" value="0">
        <input type="checkbox" {{ $planos->ativo ? "checked" : "" }} name="ativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Plano Bichos?
        <span class="required"> * </span>
        <br>

    </label>

    <div class="col-md-4">
        <input type="hidden" name="bichos" value="0">
        <input type="checkbox" {{ $planos->bichos ? "checked" : "" }} name="bichos" class="make-switch" data-on-color="info" data-off-color="default" data-on-text="Sim" data-off-text="Não" value="1"><br>
        <small>Define se o plano é referente à Companhia dos Bichos</small>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Faixa de Plano

    </label>

    <div class="col-md-4">
        <select id="id_faixa" name="id_faixa" placeholder="Selecione uma faixa de plano" class="form-control select2">
            <option></option>
            @foreach(\App\Models\FaixasPlanos::orderBy('valor', 'asc')->get() as $f)
                <option value="{{ $f->id }}" {{ $f->id == $planos->id_faixa ? "selected" : "" }}>
                    {{ $f->descricao . "  ( +" . ($f->valor-1)*100 . "%)" }}
                </option>
            @endforeach
        </select>
        <small>Define a faixa do plano</small>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Plano sem cobrança?
        <span class="required"> * </span>
        <br>

    </label>

    <div class="col-md-4">
        <input type="hidden" name="isento" value="0">
        <input type="checkbox" {{ $planos->isento ? "checked" : "" }} name="isento" class="make-switch" data-on-color="info" data-off-color="default" data-on-text="Sim" data-off-text="Não" value="1"><br>
        <small>Define se o plano possui cobrança na sinistralidade.</small>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Participativo? (> 2020)
        <span class="required"> * </span>
        <br>

    </label>

    <div class="col-md-4">
        <input type="hidden" name="participativo" value="0">
        <input type="checkbox" {{ $planos->participativo ? "checked" : "" }} name="participativo" class="make-switch" data-on-color="info" data-off-color="default" data-on-text="Sim" data-off-text="Não" value="1"><br>
        <small>Define se o plano fará cobrança instantânea no momento da consulta para liberar a guia.</small>
    </div>
</div>