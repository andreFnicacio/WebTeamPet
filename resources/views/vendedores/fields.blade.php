<!-- Ativo Field -->

<div class="form-group">
    <label class="control-label col-md-3">Ativo
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="checkbox" {{ $vendedores->ativo ? "checked" : "" }} name="ativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
    </div>
</div>

<!-- Inside Sales Permission Field -->

@if($vendedores->id)
    <div class="form-group">
        <label class="control-label col-md-3">Acesso ao Inside Sales?
            <span class="required"> * </span>
        </label>
        <div class="col-md-4">
            @if($vendedores->user)
                <input type="checkbox" {{ $vendedores->user->hasRole('INSIDE_SALES') ? "checked" : "" }} name="role_inside_sales" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
            @else
                <h5 class="font-red"> Este vendedor ainda não possui acesso ao sistema como vendedor! <br> Caso necessário, solicite o acesso para a TI. </h5>
            @endif
        </div>
    </div>
@endif

<!-- Tipo Pessoa Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="tipo_pessoa">
        Tipo de Pessoa
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="tipo_pessoa" id="tipo_pessoa" class="select2" required>
            <option value="PF" {{ $vendedores->tipo_pessoa == "PF" ? "selected" : "" }}>Pessoa Física</option>
            <option value="PJ" {{ $vendedores->tipo_pessoa == "PJ" ? "selected" : "" }}>Pessoa Jurídica</option>
        </select>
    </div>
</div>

<!-- Cpf Cnpj Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="cpf_cnpj">
        CPF/CNPJ
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $vendedores->cpf_cnpj }}" name="cpf_cnpj" data-required="1" class="form-control i-cpf" required/>
    </div>
</div>

<!-- Nome Clinica Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="nome">
        Nome do Vendedor
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $vendedores->nome }}" name="nome" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Email Contato Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="email_contato">
        Email de Contato
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $vendedores->email_contato }}" name="email_contato" data-required="1" class="form-control" required=""/>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3">CEP
        <span class="required"> * </span>
    </label>
    <div class="col-md-2">
        <input name="cep" value="{{ $vendedores->cep ? $vendedores->cep : "" }}" type="text" class="form-control" required=""/>
    </div>
    <div class="col-md-2">
        <a class="btn green-jungle btn-outline sbold address-search-trigger">Buscar</a>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3">Endereço
        <span class="required"> * </span>
    </label>
    <div class="col-md-3">
        <input name="rua" id="logradouro" value="{{ $vendedores->rua ? $vendedores->rua : "" }}" placeholder="Rua"  class="form-control" required=""/>
    </div>
    <div class="col-md-1">
        <input name="numero_endereco" value="{{ $vendedores->numero_endereco ? $vendedores->numero_endereco : "" }}" placeholder="Numero" class="form-control" required=""/>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3">Bairro
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input name="bairro" id="bairro" value="{{ $vendedores->bairro ? $vendedores->bairro : "" }}" class="form-control" required/>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3" for="cidade">Cidade/UF
        <span class="required"> * </span>
    </label>
    <div class="col-md-3">
        <input name="cidade" id="cidade" value="{{ $vendedores->cidade ? $vendedores->cidade : "" }}" class="form-control" required/>
    </div>
    <div class="col-md-1">
        <select name="estado" id="uf" class="form-control" required>
            @foreach($ufs as $uf)
                <option value="{{ $uf }}" {{ strtoupper($uf) == strtoupper($vendedores->estado) ? "selected" : "" }}>{{ $uf }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Telefone Fixo Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="telefone_fixo">
        Telefone Fixo
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $vendedores->telefone_fixo }}" name="telefone_fixo" data-required="1" class="form-control i-telefone" required/>
    </div>
</div>

<!-- Celular Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="celular">
        Celular
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $vendedores->celular }}" name="celular" data-required="1" class="form-control i-celular" />
    </div>
</div>

<!-- Email Secundario Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="email_secundario">
        Email Secundario
    </label>
    <div class="col-md-4">
        <input type="email" name="email_secundario" value="{{ $vendedores->email_secundario }}"data-required="1" class="form-control" />
    </div>
</div>

<!-- Banco Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="banco">
        Banco
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="banco" id="banco" class="select2" required>
            @foreach(\App\Helpers\Utils::getBancos() as $codigo => $banco)
                <option value="{{ $codigo }}" {{ $codigo == $vendedores->banco ? "selected" : "" }}>{{ $banco }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Agencia Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="agencia">
        Agência
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input required type="text" name="agencia" value="{{ $vendedores->agencia }}" data-required="1" class="form-control" />
    </div>
</div>

<!-- Numero Conta Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="numero_conta">
        Conta
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input required type="text" value="{{ $vendedores->numero_conta }}" name="numero_conta" data-required="1" class="form-control" />
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Avatar
        {{-- <span class="required"> * </span> --}}
    </label>
    <div class="col-md-4">
        <img src="{{ route('vendedores.avatar', $vendedores->id) }}" class="img-responsive" width="300" alt="">
        <br>
        <input type="file" class="form-control" name="avatar" accept="image/x-png,image/bmp,image/jpeg">
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Assinatura
        {{-- <span class="required"> * </span> --}}
    </label>
    <div class="col-md-4">
        <img src="{{ route('vendedores.assinatura', $vendedores->id) }}" class="img-responsive" width="300" alt="">
        <br>
        <input type="file" class="form-control" name="assinatura" accept="image/x-png,image/bmp,image/jpeg">
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">É vendedor direto?
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="hidden" name="direto" value="1">
        <input type="checkbox" value="{{ $vendedores->direto }}" class="make-switch" name="direto" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não">
    </div>
</div>