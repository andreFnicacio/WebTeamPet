<!-- Tipo Pessoa Field -->

<div class="form-group">
    <label class="control-label col-md-3">Ativo?
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        {{ Form::hidden('ativo',0) }}
        <input type="checkbox" {{ $clinicas->ativo ? "checked" : "" }} name="ativo" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="tipo_pessoa">
        Tipo de Pessoa
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="tipo_pessoa" id="tipo_pessoa" class="select2" required>
            <option value="PF" {{ $clinicas->tipo_pessoa == "PF" ? "selected" : "" }}>Pessoa Física</option>
            <option value="PJ" {{ $clinicas->tipo_pessoa == "PJ" ? "selected" : "" }}>Pessoa Jurídica</option>
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
        <input type="text" value="{{ $clinicas->cpf_cnpj }}" id="cpf_cnpj" name="cpf_cnpj" data-required="1" class="form-control i-cpf" required/>
    </div>
</div>

<!-- Nome Clinica Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="nome_clinica">
        Nome do Credenciado
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $clinicas->nome_clinica }}" name="nome_clinica" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Contato Principal Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="contato_principal">
        Contato Principal
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $clinicas->contato_principal }}" name="contato_principal" data-required="1" class="form-control" />
    </div>
</div>

<!-- Email Contato Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="email_contato">
        Email de Contato
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $clinicas->email_contato }}" id="email_contato" name="email_contato" data-required="1" class="form-control" />
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3">CEP
    </label>
    <div class="col-md-2">
        <input name="cep" value="{{ $clinicas->cep ? $clinicas->cep : "" }}" type="text" class="form-control" />
    </div>
    <div class="col-md-2">
        <a class="btn green-jungle btn-outline sbold address-search-trigger">Buscar</a>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3">Endereço
    </label>
    <div class="col-md-3">
        <input name="rua" id="logradouro" value="{{ $clinicas->rua ? $clinicas->rua : "" }}" placeholder="Rua"  class="form-control" />
    </div>
    <div class="col-md-1">
        <input name="numero_endereco" value="{{ $clinicas->numero_endereco ? $clinicas->numero_endereco : "" }}" placeholder="Numero" class="form-control" />
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3">Bairro
    </label>
    <div class="col-md-4">
        <input name="bairro" id="bairro" value="{{ $clinicas->bairro ? $clinicas->bairro : "" }}" class="form-control" />
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3" for="cidade">Cidade/UF
    </label>
    <div class="col-md-3">
        <input name="cidade" id="cidade" value="{{ $clinicas->cidade ? $clinicas->cidade : "" }}" class="form-control" />
    </div>
    <div class="col-md-1">
        <select name="estado" id="uf" class="form-control" >
            @foreach($ufs as $uf)
                <option value="{{ $uf }}" {{ strtoupper($uf) == strtoupper($clinicas->estado) ? "selected" : "" }}>{{ $uf }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Telefone Fixo Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="telefone_fixo">
        Telefone Fixo
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $clinicas->telefone_fixo }}" name="telefone_fixo" data-required="1" class="form-control i-telefone" />
    </div>
</div>

<!-- Celular Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="celular">
        Celular
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $clinicas->celular }}" name="celular" data-required="1" class="form-control i-celular" required />
    </div>
</div>

<!-- Email Secundario Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="email_secundario">
        Email Secundario
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="email" name="email_secundario" value="{{ $clinicas->email_secundario }}" data-required="1" class="form-control" required />
    </div>
</div>

<!-- Banco Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="banco">
        Banco
    </label>
    <div class="col-md-4">
        <select name="banco" id="banco" class="select2">
            @foreach(\App\Helpers\Utils::getBancos() as $codigo => $banco)
                <option value="{{ $codigo }}" {{ $codigo == $clinicas->banco ? "selected" : "" }}>{{ $banco }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Agencia Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="agencia">
        Agência
    </label>
    <div class="col-md-4">
        <input type="text" name="agencia" value="{{ $clinicas->agencia }}" data-required="1" class="form-control" />
    </div>
</div>

<!-- Numero Conta Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="numero_conta">
        Conta
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $clinicas->numero_conta }}" name="numero_conta" data-required="1" class="form-control" />
    </div>
</div>

<!-- Tipo Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="tipo">
        Tipo
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="tipo" id="tipo" class="select2" required>
            @foreach([
                "HOSPITAL" => "Hospital",
                "CLINICA"  => "Clínica",
                "AUTONOMO" => "Autônomo"
            ] as $value => $tipo)
                <option value="{{ $value }}" {{ $value == $clinicas->tipo ? "selected" : "" }}>{{ $tipo }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="id_tabela">
        Tabela de Referência
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_tabela" id="id_tabela" class="select2" required>
            @foreach(\App\Models\TabelasReferencia::all() as $tabelaReferencia)
                <option value="{{ $tabelaReferencia->id }}" {{ $tabelaReferencia->id == $clinicas->id_tabela ? "selected" : "" }}>{{ $tabelaReferencia->nome }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Selecionável por outro credenciado para emissão de guias de exames?
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        {{ Form::hidden('selecionavel',0) }}
        <input type="checkbox" {{ $clinicas->selecionavel ? "checked" : "" }} name="selecionavel" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="id_urh">
        URH
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_urh" id="id_urh" class="select2" required>
            <option value=""></option>
            @foreach(\App\Models\Urh::where('ativo', 1)->orderBy('nome_urh', 'ASC')->get() as $urh)
                <option value="{{ $urh->id }}" {{ $urh->id == $clinicas->id_urh ? "selected" : "" }}>{{ $urh->nome_urh }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Aceitou URH?
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        {{ Form::hidden('aceite_urh',0) }}
        <input type="checkbox" {{ $clinicas->aceite_urh ? "checked" : "" }} name="aceite_urh" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
    </div>
</div>

<div class="col-md-12" style="margin-bottom: 20px;"> 
    <h3 class="block" style="margin-top: 0px;">
        Exibição no site
        <div class="btn btn-icon-only blue" data-toggle="modal" data-target="#coord-helper">
            <i class="fa fa-map-marker"></i>
        </div>
    </h3>
</div>
<div class="modal" id="coord-helper">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">Como obter as coordenadas?</div>
            <div class="modal-body">
                <h4>Obter as coordenadas de um lugar</h4>
                <ol>
                    <li>No seu navegador, abra o <a href="https://www.google.com/maps" target="_blank" rel="noopener">Google Maps <i class="fa fa-external-link"></i></a>.</li>
                    <li>Pesquise o local e encontre-o no mapa.</li>
                    <li>Clique com o botão direito do mouse no local.</li>
                    <li>Selecione "o que há aqui?".</li>
                    <li>Na parte inferior, você verá um cartão com as coordenadas.</li>
                </ol>
                <br>
                <p>Importante!</p>
                <ul>
                    <li>O sinal de negativo e o ponto das coordenadas são extremamente importantes!</li>
                    <li>A ordem das coordenadas sempre é: latitude, longitude.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3">Exibir no site?
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        {{ Form::hidden('exibir_site',0) }}
        <input type="checkbox" {{ $clinicas->exibir_site ? "checked" : "" }} name="exibir_site" class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não" value="1">
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3" for="lat">
        Latitude
    </label>
    <div class="col-md-4">
        <input type="text" name="lat" value="{{ $clinicas->lat }}" class="form-control" />
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3" for="lng">
        Longitude
    </label>
    <div class="col-md-4">
        <input type="text" name="lng" value="{{ $clinicas->lng }}" class="form-control" />
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3" for="telefone_site">
        Telefone a ser exibido no site
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $clinicas->telefone_site }}" name="telefone_site" class="form-control"/>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3" for="email_site">
        Email a ser exibido no site
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $clinicas->email_site }}" name="email_site" class="form-control"/>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-3" for="nome_site">
        Nome a ser exibido no site
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $clinicas->nome_site }}" name="nome_site" class="form-control"/>
    </div>
</div>