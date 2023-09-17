<div class="m-portlet m-portlet--tab">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
						<span class="m-portlet__head-icon m--hide">
						<i class="la la-gear"></i>
						</span>
                <h3 class="m-portlet__head-text">
                    Dados Gerais
                </h3>
            </div>
        </div>
    </div>
    <!--begin::Form-->
    <form class="m-form m-form--fit m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group">
                <label>Matrícula</label>
                <input type="email" class="form-control m-input m-input--square" value="{{ $cliente->id }}">
            </div>
            <div class="form-group m-form__group">
                <label>Número do Contrato</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->numero_contrato }}">
            </div>
            <div class="form-group m-form__group">
                <label>RG</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->rg ? $cliente->rg : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Email</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->email ? $cliente->email : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Celular</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->celular ? $cliente->celular : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Telefone</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->telefone ? $cliente->telefone : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Observações</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->observacoes ? $cliente->observacoes : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Data de Nascimento</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->data_nascimento ? $cliente->data_nascimento->format('d/m/Y') : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Nome ou Razão Social</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->nome_cliente ? $cliente->nome_cliente : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Nome ou Razão Social</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->nome_cliente ? $cliente->nome_cliente : "" }}">
            </div>
        </div>
    </form>
    <!--end::Form-->
</div>
<div class="m-portlet m-portlet--tab">
    <div class="m-portlet__head">
        <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
						<span class="m-portlet__head-icon m--hide">
						<i class="la la-gear"></i>
						</span>
                <h3 class="m-portlet__head-text">
                    Endereço
                </h3>
            </div>
        </div>
    </div>
    <!--begin::Form-->
    <form class="m-form m-form--fit m-form--label-align-right">
        <div class="m-portlet__body">
            <div class="form-group m-form__group">
                <label>CEP</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->cep ? $cliente->cep : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Logradouro</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->rua ? $cliente->rua : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Número</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->numero_endereco ? $cliente->numero_endereco : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Bairro</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->bairro ? $cliente->bairro : "" }}">
            </div>
            <div class="form-group m-form__group">
                <label>Cidade/UF</label>
                <input type="text" class="form-control m-input m-input--square" value="{{ $cliente->cidade ? $cliente->cidade . '/' . $cliente->estado : "" }}">
            </div>
        </div>
    </form>
    <!--end::Form-->
</div>