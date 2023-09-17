{!! Form::model($clinica, [
    'route' => [
        'clinicas.update',
        $clinica->id
    ],
    'method' => 'patch',
    'class' => 'form-horizontal',
    'id' => 'clinicas'
]);
!!}
<div class="form-body">

    <div class="alert alert-danger display-hide">
        <button class="close" data-close="alert"></button>
        Verifique se você preencheu todos os campos.
    </div>
    <div class="alert alert-success display-hide">
        <button class="close" data-close="alert"></button>
        Validado com sucesso.
    </div>
    <div class="form-body">

        <div class="row">
            <div class="col-xs-6">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i>Dados Gerais
                        </div>
                    </div>
                    <div class="portlet-body">
                        <!-- Tipo Pessoa Field -->
                        <div class="form-group">
                            <label class="col-md-12">Ativo?
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                {{ Form::hidden('ativo',0) }}
                                <input type="checkbox" {{ $clinica->ativo ? "checked" : "" }} name="ativo"
                                       class="make-switch" data-on-color="success" data-off-color="danger"
                                       data-on-text="Sim" data-off-text="Não" value="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12" for="tipo_pessoa">
                                Tipo de Pessoa
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                <select name="tipo_pessoa" id="tipo_pessoa" class="select2" required>
                                    <option value="PF" {{ $clinica->tipo_pessoa == "PF" ? "selected" : "" }}>Pessoa
                                        Física
                                    </option>
                                    <option value="PJ" {{ $clinica->tipo_pessoa == "PJ" ? "selected" : "" }}>Pessoa
                                        Jurídica
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Cpf Cnpj Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="cpf_cnpj">
                                CPF/CNPJ
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->cpf_cnpj }}" name="cpf_cnpj" data-required="1"
                                       class="form-control i-cpf" required/>
                            </div>
                        </div>

                        <!-- Nome Clinica Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="nome_clinica">
                                Nome do Credenciado
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->nome_clinica }}" name="nome_clinica"
                                       data-required="1" class="form-control" required/>
                            </div>
                        </div>

                        <!-- Contato Principal Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="contato_principal">
                                Contato Principal
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->contato_principal }}" name="contato_principal"
                                       data-required="1" class="form-control"/>
                            </div>
                        </div>

                        <!-- Email Contato Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="email_contato">
                                Email de Contato
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->email_contato }}" name="email_contato"
                                       data-required="1" class="form-control"/>
                            </div>
                        </div>

                        <!-- Telefone Fixo Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="telefone_fixo">
                                Telefone Fixo
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->telefone_fixo }}" name="telefone_fixo"
                                       data-required="1" class="form-control i-telefone"/>
                            </div>
                        </div>

                        <!-- Celular Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="celular">
                                Celular
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->celular }}" name="celular" data-required="1"
                                       class="form-control i-celular" required/>
                            </div>
                        </div>

                        <!-- Email Secundario Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="email_secundario">
                                Email Secundario
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                <input type="email" name="email_secundario" value="{{ $clinica->email_secundario }}"
                                       data-required="1" class="form-control" required/>
                            </div>
                        </div>

                        <!-- Banco Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="banco">
                                Banco
                            </label>
                            <div class="col-md-12">
                                <select name="banco" id="banco" class="select2">
                                    @foreach(\App\Helpers\Utils::getBancos() as $codigo => $banco)
                                        <option value="{{ $codigo }}" {{ $codigo == $clinica->banco ? "selected" : "" }}>{{ $banco }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Agencia Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="agencia">
                                Agência
                            </label>
                            <div class="col-md-12">
                                <input type="text" name="agencia" value="{{ $clinica->agencia }}" data-required="1"
                                       class="form-control"/>
                            </div>
                        </div>

                        <!-- Numero Conta Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="numero_conta">
                                Conta
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->numero_conta }}" name="numero_conta"
                                       data-required="1" class="form-control"/>
                            </div>
                        </div>

                        <!-- Tipo Field -->
                        <div class="form-group">
                            <label class="col-md-12" for="tipo">
                                Tipo
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                <select name="tipo" id="tipo" class="select2" required>
                                    @foreach([
                                        "HOSPITAL" => "Hospital",
                                        "CLINICA"  => "Clínica",
                                        "AUTONOMO" => "Autônomo"
                                    ] as $value => $tipo)
                                        <option value="{{ $value }}" {{ $value == $clinica->tipo ? "selected" : "" }}>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12" for="id_tabela">
                                Tabela de Referência
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                <select name="id_tabela" id="id_tabela" class="select2" required>
                                    @foreach(\App\Models\TabelasReferencia::all() as $tabelaReferencia)
                                        <option value="{{ $tabelaReferencia->id }}" {{ $tabelaReferencia->id == $clinica->id_tabela ? "selected" : "" }}>{{ $tabelaReferencia->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Selecionável por outro credenciado para emissão de guias de exames?
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                {{ Form::hidden('selecionavel',0) }}
                                <input type="checkbox" {{ $clinica->selecionavel ? "checked" : "" }} name="selecionavel"
                                       class="make-switch" data-on-color="success" data-off-color="danger"
                                       data-on-text="Sim" data-off-text="Não" value="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12" for="id_urh">
                                URH
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                <select name="id_urh" id="id_urh" class="select2" required>
                                    <option value=""></option>
                                    @foreach(\App\Models\Urh::where('ativo', 1)->orderBy('nome_urh', 'ASC')->get() as $urh)
                                        <option value="{{ $urh->id }}" {{ $urh->id == $clinica->id_urh ? "selected" : "" }}>{{ $urh->nome_urh }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-12">Aceitou URH?
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-12">
                                {{ Form::hidden('aceite_urh',0) }}
                                <input type="checkbox" {{ $clinica->aceite_urh ? "checked" : "" }} name="aceite_urh"
                                       class="make-switch" data-on-color="success" data-off-color="danger"
                                       data-on-text="Sim" data-off-text="Não" value="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-map-marker"></i>Exibição no Site / Aplicativo
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="modal" id="coord-helper">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">Como obter as coordenadas?</div>
                                    <div class="modal-body">
                                        <h4>Obter as coordenadas de um lugar</h4>
                                        <ol>
                                            <li>No seu navegador, abra o <a href="https://www.google.com/maps"
                                                                            target="_blank" rel="noopener">Google Maps
                                                    <i class="fa fa-external-link"></i></a>.
                                            </li>
                                            <li>Pesquise o local e encontre-o no mapa.</li>
                                            <li>Clique com o botão direito do mouse no local.</li>
                                            <li>Selecione "o que há aqui?".</li>
                                            <li>Na parte inferior, você verá um cartão com as coordenadas.</li>
                                        </ol>
                                        <br>
                                        <p>Importante!</p>
                                        <ul>
                                            <li>O sinal de negativo e o ponto das coordenadas são extremamente
                                                importantes!
                                            </li>
                                            <li>A ordem das coordenadas sempre é: latitude, longitude.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">
                                <label class="">Exibir no site?
                                    <span class="required"> * </span>
                                </label>
                                {{ Form::hidden('exibir_site',0) }}
                                <input type="checkbox" {{ $clinica->exibir_site ? "checked" : "" }} name="exibir_site"
                                       class="make-switch" data-on-color="success" data-off-color="danger"
                                       data-on-text="Sim" data-off-text="Não" value="1">
                            </div>
                            <div class="col-md-8 text-right">
                                <label for="">
                                    Como obter as coordenadas?
                                </label>
                                <br>
                                <div class="label label-warning blue" data-toggle="modal" data-target="#coord-helper"
                                     style="cursor:pointer;">
                                    Ajuda
                                    <i class="fa fa-info-circle" style="margin-left:10px;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="" for="lat">
                                    Latitude
                                </label>
                                <input type="text" name="lat" value="{{ $clinica->lat }}" class="form-control"/>
                            </div>
                            <div class="col-md-6">
                                <label class="" for="lng">
                                    Longitude
                                </label>
                                <input type="text" name="lng" value="{{ $clinica->lng }}" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12" for="telefone_site">
                                Telefone a ser exibido no site/app
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->telefone_site }}" name="telefone_site"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12" for="celular_site">
                                Celular a ser exibido no site/app
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->celular_site }}" name="celular_site"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12" for="email_site">
                                Email a ser exibido no site/app
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->email_site }}" name="email_site"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12" for="nome_site">
                                Nome a ser exibido no /app
                            </label>
                            <div class="col-md-12">
                                <input type="text" value="{{ $clinica->nome_site }}" name="nome_site"
                                       class="form-control"/>
                            </div>
                        </div>

                        <!-- ATENDIMENTO TAG -->
                        <div class="form-group">

                            <label class="col-md-12">
                                O que atende (tags):
                            </label>
                            <div class='col-md-12'>
                                <select id="atendimento_tags" name="atendimento_tags[]" placeholder="Nenhuma"
                                        class="form-control select2-tag"
                                        multiple="multiple" {{!Entrust::can('editar_tags_atendimentos_clinicas') ? 'disabled' : '' }}>
                                    <option></option>
                                    @foreach(\Modules\Clinics\Entities\ClinicaAtendimentoTag::get() as $tag)
                                        <option value="{{ $tag->nome }}" {{in_array($tag->nome, $clinica->tagsSelecionadas()->with('tag')->get()->pluck('tag.nome')->toArray()) ? 'selected' : ''}}>{{ $tag->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-map-marker"></i>Endereço
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="form-group">
                            <label class="col-md-12">CEP
                            </label>
                            <div class="col-md-8">
                                <input name="cep" value="{{ $clinica->cep ? $clinica->cep : "" }}" type="text"
                                       class="form-control"/>
                            </div>
                            <div class="col-md-4">
                                <a class="btn green-jungle form-control sbold address-search-trigger">Buscar</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8">
                                <label class="">Endereço
                                    <span class="required"> * </span>
                                </label>
                                <input name="rua" id="logradouro" value="{{ $clinica->rua ? $clinica->rua : "" }}"
                                       placeholder="Rua" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label class="">Número
                                    <span class="required"> * </span>
                                </label>
                                <input name="numero_endereco"
                                       value="{{ $clinica->numero_endereco ? $clinica->numero_endereco : "" }}"
                                       placeholder="Número" class="form-control" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-12">Bairro
                            </label>
                            <div class="col-md-12">
                                <input name="bairro" id="bairro" value="{{ $clinica->bairro ? $clinica->bairro : "" }}"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8">
                                <label class="">Cidade
                                    <span class="required"> * </span>
                                </label>
                                <input name="cidade" id="cidade" value="{{ $clinica->cidade ? $clinica->cidade : "" }}"
                                       class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <label class="">UF
                                    <span class="required"> * </span>
                                </label>
                                <select name="estado" id="uf" class="form-control">
                                    @foreach($ufs as $uf)
                                        <option value="{{ $uf }}" {{ strtoupper($uf) == strtoupper($clinica->estado) ? "selected" : "" }}>{{ $uf }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="margin-top-10">
    <button class="btn green"> Salvar</button>
</div>
{!! Form::close() !!}