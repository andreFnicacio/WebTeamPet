<!-- Tipo Pessoa Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="tipo_pessoa">
        Tipo de Pessoa
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="tipo_pessoa" id="tipo_pessoa" class="select2" required>
            <option value="PF" {{ $prestadores->tipo_pessoa == "PF" ? "selected" : "" }}>Pessoa Física</option>
            <option value="PJ" {{ $prestadores->tipo_pessoa == "PJ" ? "selected" : "" }}>Pessoa Jurídica</option>
        </select>
    </div>
</div>

<!-- Nome Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="nome">
        Nome
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $prestadores->nome }}" name="nome" data-required="1" class="form-control" required=""/>
    </div>
</div>

<!-- CPF Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="nome">
        CPF
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $prestadores->cpf }}" name="cpf" data-required="1" class="form-control cpf" required/>
    </div>
</div>


<!-- Email Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="email">
        Email
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="email" value="{{ $prestadores->email }}" name="email" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Telefone Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="telefone">
        Telefone
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $prestadores->telefone }}" name="telefone" data-required="1" class="form-control" />
    </div>
</div>

<!-- Crmv Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="crmv">
        CRMV
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="number" name="crmv" value="{{ $prestadores->crmv }}" data-required="1" class="form-control" required/>
        <small class="helper">Apenas números</small>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="crmv_uf">
        Estado do CRMV 
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="crmv_uf" id="crmv_uf"  class="select2">
            <option value="AC" {{ ($prestadores->crmv_uf == 'AC' ? 'selected' : '') }}>AC</option>
            <option value="AL" {{ ($prestadores->crmv_uf == 'AL' ? 'selected' : '') }}>AL</option>
            <option value="AP" {{ ($prestadores->crmv_uf == 'AP' ? 'selected' : '') }}>AP</option>
            <option value="AM" {{ ($prestadores->crmv_uf == 'AM' ? 'selected' : '') }}>AM</option>
            <option value="BA" {{ ($prestadores->crmv_uf == 'BA' ? 'selected' : '') }}>BA</option>
            <option value="CE" {{ ($prestadores->crmv_uf == 'CE' ? 'selected' : '') }}>CE</option>
            <option value="DF" {{ ($prestadores->crmv_uf == 'DF' ? 'selected' : '') }}>DF</option>
            <option value="ES" {{ ($prestadores->crmv_uf == 'ES' ? 'selected' : '') }}>ES</option>
            <option value="GO" {{ ($prestadores->crmv_uf == 'GO' ? 'selected' : '') }}>GO</option>
            <option value="MA" {{ ($prestadores->crmv_uf == 'MA' ? 'selected' : '') }}>MA</option>
            <option value="MT" {{ ($prestadores->crmv_uf == 'MT' ? 'selected' : '') }}>MT</option>
            <option value="MS" {{ ($prestadores->crmv_uf == 'MS' ? 'selected' : '') }}>MS</option>
            <option value="MG" {{ ($prestadores->crmv_uf == 'MG' ? 'selected' : '') }}>MG</option>
            <option value="PA" {{ ($prestadores->crmv_uf == 'PA' ? 'selected' : '') }}>PA</option>
            <option value="PB" {{ ($prestadores->crmv_uf == 'PB' ? 'selected' : '') }}>PB</option>
            <option value="PR" {{ ($prestadores->crmv_uf == 'PR' ? 'selected' : '') }}>PR</option>
            <option value="PE" {{ ($prestadores->crmv_uf == 'PE' ? 'selected' : '') }}>PE</option>
            <option value="PI" {{ ($prestadores->crmv_uf == 'PI' ? 'selected' : '') }}>PI</option>
            <option value="RJ" {{ ($prestadores->crmv_uf == 'RJ' ? 'selected' : '') }}>RJ</option>
            <option value="RN" {{ ($prestadores->crmv_uf == 'RN' ? 'selected' : '') }}>RN</option>
            <option value="RS" {{ ($prestadores->crmv_uf == 'RS' ? 'selected' : '') }}>RS</option>
            <option value="RO" {{ ($prestadores->crmv_uf == 'RO' ? 'selected' : '') }}>RO</option>
            <option value="RR" {{ ($prestadores->crmv_uf == 'RR' ? 'selected' : '') }}>RR</option>
            <option value="SC" {{ ($prestadores->crmv_uf == 'SC' ? 'selected' : '') }}>SC</option>
            <option value="SP" {{ ($prestadores->crmv_uf == 'SP' ? 'selected' : '') }}>SP</option>
            <option value="SE" {{ ($prestadores->crmv_uf == 'SE' ? 'selected' : '') }}>SE</option>
            <option value="TO" {{ ($prestadores->crmv_uf == 'TO' ? 'selected' : '') }}>TO</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Especialista?

    </label>
    <div class="col-md-4">
        <input type="checkbox" {{ $prestadores->especialista ? "checked" : "" }} name="especialista" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
    </div>
</div>

<!-- Id Especialidade Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_especialidade">
        Especialidade
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">

        <select name="id_especialidade" id="id_especialidade" class="select2">
            <option value=""></option>
            @foreach(\App\Models\Especialidades::all() as $e)
                <option value="{{ $e->id }}" {{ $e->id == $prestadores->id_especialidade ? 'selected' : '' }}>{{ $e->nome }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3">Data de Formação
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
            <input type="text" value="{{ $prestadores->data_formacao ? $prestadores->data_formacao->format('d/m/Y') : "" }}" name="data_formacao" class="form-control" readonly required>
            <span class="input-group-btn">
        <button class="btn default" type="button">
            <i class="fa fa-calendar"></i>
        </button>
        </span>
        </div>
    </div>
</div>