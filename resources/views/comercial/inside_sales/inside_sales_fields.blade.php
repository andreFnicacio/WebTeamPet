@php
    $readonly = isset($readonly) ? $readonly : false;
    $isCliente = \Entrust::hasRole(['CLIENTE']);
@endphp
<div class="row form">
    <div class="col-sm-12">

        <div style="padding: 0px 20px;">

            <div class="form-body">
                <ul class="nav nav-pills nav-justified steps">
                    <li>
                        <a href="#tab1" data-toggle="tab" class="step">
                            <span class="number">
                                <i class="fa fa-user"></i>
                            </span>
                            <span class="desc">
                                <i class="fa fa-check"></i> Cliente
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="#tab2" data-toggle="tab" class="step">
                            <span class="number">
                                <i class="fa fa-paw"></i>
                            </span>
                            <span class="desc">
                                <i class="fa fa-check"></i> Pets
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="#tab3" data-toggle="tab" class="step active">
                            <span class="number">
                                <i class="fa fa-list"></i>
                            </span>
                            <span class="desc">
                                <i class="fa fa-check"></i> Checklist
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="#tab4" data-toggle="tab" class="step">
                            <span class="number">
                                <i class="fa fa-dollar"></i>
                            </span>
                            <span class="desc">
                                <i class="fa fa-check"></i> Pagamento
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="#tab5" data-toggle="tab" class="step">
                            <span class="number">
                                <i class="fa fa-check"></i>
                            </span>
                            <span class="desc">
                                <i class="fa fa-check"></i> Confirmação
                            </span>
                        </a>
                    </li>
                </ul>
                <div id="bar" class="progress progress-striped" role="progressbar">
                    <div class="progress-bar progress-bar-success"> </div>
                </div>
                <div class="tab-content">
                    <div class="alert alert-danger display-none">
                        <button class="close" data-dismiss="alert"></button>
                        Preencha os campos corretamente. Verifique abaixo:
                    </div>
                    <div class="alert alert-success display-none">
                        <button class="close" data-dismiss="alert"></button>
                        Campos preenchidos corretamente!
                    </div>
                    <div class="alert alert-warning alert-pet display-none">
                        <button class="close" data-dismiss="alert"></button>
                        Pelo menos um <strong>PET</strong> deve ser adicionado!
                    </div>
                    <div class="tab-pane active" id="tab1">
                        <h3 class="block">Dados do Cliente</h3>
                        <div class="form-group">
                            <label class="control-label col-md-3">Nome do Cliente
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="cliente[dados][nome_cliente]" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Sexo
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <select name="cliente[dados][sexo]" id="sexo" class="form-control select2" required>
                                    <option value=""></option>
                                    @foreach(["M", "F", "0"] as $s)
                                        <option value="{{ $s }}">
                                            @php
                                                switch ($s) {
                                                    case "M":
                                                        echo "Masculino";
                                                        break;
                                                    case "F":
                                                        echo "Feminino";
                                                        break;
                                                    default:
                                                        echo "Outro";
                                                        break;
                                                }
                                            @endphp
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">CPF
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input name="cliente[dados][cpf]" id="cpf" type="text" class="form-control cpf" required/>
                                <small> Somente números. Sem traços, pontos ou barras.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">RG
                            </label>
                            <div class="col-md-6">
                                <input name="cliente[dados][rg]" id="rg" type="text" class="form-control"/>
                                <small> Somente números. Sem traços, pontos ou barras.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">E-mail
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input name="cliente[dados][email]" id="email" type="text" class="form-control" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Celular
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <input name="cliente[dados][celular]" id="celular" type="text" class="form-control tel" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Telefone
                            </label>
                            <div class="col-md-6">
                                <input name="cliente[dados][telefone_fixo]" id="telefone_fixo" type="text" class="form-control tel"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Data de Nascimento
                                <span class="required"> * </span>
                            </label>
                            <div class="col-md-6">
                                <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                    <input type="text" name="cliente[dados][data_nascimento]" class="form-control" readonly required>
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Observações
                            </label>
                            <div class="col-md-6">
                                <textarea name="cliente[dados][observacoes]" id="observacoes" type="text" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12" >
                            <div class="col-md-12" style="margin-bottom: 20px;">
                                <h3 class="block" style="margin-top: 30px;">Endereço</h3>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">CEP
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-2">
                                    <input name="cliente[endereco][cep]" type="text" class="form-control cep" required/>
                                    <input name="cep" class="address-search-trigger-blur" type="hidden" />
                                </div>
                                <div class="col-md-2">
                                    <a class="btn green-jungle btn-outline sbold cep-search">Buscar</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Endereço
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-6">
                                    <input name="cliente[endereco][rua]" id="logradouro" class="form-control" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Número
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-6">
                                    <input name="cliente[endereco][numero_endereco]" class="form-control" maxlength="10" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Bairro
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-6">
                                    <input name="cliente[endereco][bairro]" id="bairro" class="form-control" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Cidade
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-6">
                                    <input name="cliente[endereco][cidade]" id="cidade" class="form-control" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">UF
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-6">
                                    <select name="cliente[endereco][estado]" id="uf" class="form-control" required>
                                        <option value=""></option>
                                        @foreach($ufs as $uf)
                                            <option value="{{ $uf }}">{{ $uf }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Complemento

                                </label>
                                <div class="col-md-6">
                                    <input name="cliente[endereco][complemento_endereco]" value="{{ $cliente->complemento_endereco ? $cliente->complemento_endereco : "" }}" class="form-control"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" >
                            <div class="col-md-12" style="margin-bottom: 20px;">
                                <h3 class="block" style="margin-top: 30px;">Dados para pagamento (mensal ou anual)</h3>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Dia de vencimento
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-6">
                                    <input type="number" name="cliente[dados][dia_vencimento]" value="{{date('d')}}" id="dia_vencimento" class="form-control" required/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">Forma de pagamento
                                    <span class="required"> * </span>
                                </label>
                                <div class="col-md-6">
                                    <select name="cliente[dados][forma_pagamento]" id="forma_pagamento" class="form-control" required>
                                        <option value=""></option>
                                        <option value="cartao">Cartão de crédito</option>
                                        <option value="boleto">Boleto</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab2">
                        <div class="row">
                            <div class="col-sm-5">
                                <h3 class="block">Pets</h3>
                            </div>
                            <div class="col-sm-7">
                                <h3 class="block">Doenças pré-existentes</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="mt-element-list">
                                    <div class="mt-list-head list-default blue">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="list-head-title-container">
                                                    <h3 class="list-title uppercase sbold">Pets Adicionados</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-list-container list-default Pets List" data-numpets="0">
                                        <ul>
                                            <li class="mt-list-item">
                                                <div class="list-item-content">
                                                    <div id="btn-adicionar-pet" class="btn btn-block btn-default green-jungle" data-toggle="modal" data-target="#modal-addPet">
                                                        <i class="fa fa-plus-circle"></i> Adicionar Pet
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="modal" id="modal-addPet" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <h2>Adicionar novo pet</h2>
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <h3 class="block">Dados do Pet</h3>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Nome do Pet
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input class="form-control" name="pet-nome_pet" type="text" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Tipo
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" name="pet-tipo" required>
                                                            <option selected="true" disabled="disabled" value=""></option>
                                                            <option value="CACHORRO">Cão</option>
                                                            <option value="GATO">Gato</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Sexo
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" name="pet-sexo" required>
                                                            <option selected="true" disabled="disabled" value=""></option>
                                                            @foreach([
                                                                    'M'     => 'Macho',
                                                                    'F'     => 'Fêmea'
                                                                ] as $value => $option)
                                                                <option value="{{ $value }}">{{ $option }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Raça do pet
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" name="pet-id_raca" required>
                                                            <option selected="true" disabled="disabled" value=""></option>
                                                            <option value="1">SRD - Sem Raça Definida</option>
                                                            @foreach(\App\Models\Raca::orderBy('tipo', 'asc')->orderBy('nome', 'asc')->get() as $r)
                                                                <option value="{{ $r->id }}">{{ $r->nome . " - " . $r->tipo }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Data de Nascimento
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                                            <input type="text" name="pet-data_nascimento" class="form-control" readonly required>
                                                            <span class="input-group-btn">
                                                    <button class="btn default" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Possui alguma doença?
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" name="pet-contem_doenca_pre_existente" required>
                                                            <option selected="true" disabled="disabled" value=""></option>
                                                            <option value="0" >Não</option>
                                                            <option value="1">Sim</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Cite as doenças (caso existam)
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input class="form-control" name="pet-doencas_pre_existentes" type="text" >
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Observações
                                                    </label>
                                                    <div class="col-md-6">
                                                        <textarea class="form-control" name="pet-observacoes" type="text" ></textarea>
                                                    </div>
                                                </div>


                                                <br>
                                                <h2>Dados do plano:</h2>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Plano
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" name="plano-id_plano" required>
                                                            <option selected="true" disabled="disabled" value=""></option>
                                                            @foreach(\App\Models\Planos::where('ativo', 1)->orderBy('ativo', 'desc')->get() as $plano)
                                                                <option value="{{ $plano->id }}" >{{ $plano->nome_plano }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Participativo?
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" name="plano-participativo" required>
                                                            <option selected="true" disabled="disabled" value=""></option>
                                                            <option value="0" >Não</option>
                                                            <option value="1">Sim</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Familiar?
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" name="plano-familiar" required>
                                                            <option selected="true" disabled="disabled" value=""></option>
                                                            <option value="0">Não</option>
                                                            <option value="1">Sim</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Data de Início do Contrato
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                                            <input type="text" name="plano-data_inicio_contrato" class="form-control" readonly required>
                                                            <span class="input-group-btn">
                                                                <button class="btn default" type="button">
                                                                    <i class="fa fa-calendar"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <input name="plano-id_vendedor" type="hidden" value="{{ $vendedor->id }}">

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Valor da Adesão (R$)
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input class="form-control mask-money" name="plano-valor_adesao" type="tel" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Valor do Plano (R$)
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <input class="form-control mask-money" name="plano-valor_plano" type="tel" required>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3">Regime
                                                        <span class="required"> * </span>
                                                    </label>
                                                    <div class="col-md-6">
                                                        <select class="form-control" name="plano-regime" required>
                                                            <option selected="true" disabled="disabled" value=""></option>
                                                            @foreach(\App\Models\Pets::$regimes as $regime)
                                                                <option value="{{ $regime }}">{{ $regime }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                <div class="btn btn-sm btn-default green-jungle" onclick="addPet()">
                                                    Adicionar
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="addPet-form">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mt-element-list">
                                                <div class="mt-list-head list-simple ext-1 font-white bg-green-sharp">
                                                    <div class="list-head-title-container">
                                                        <h3 class="list-title">Checklist</h3>
                                                    </div>
                                                </div>
                                                <div class="mt-list-container list-simple ext-1" id="doencas_pre_existentes">
                                                    <ul>
                                                        @php
                                                            $i = 0;
                                                        @endphp
                                                        @foreach($checklistDoencasProposta as $check)
                                                            <li class="mt-list-item" style="border-left: none;">
                                                                <div class="list-item-content" style="padding-left: 0;padding-right: 0">

                                                                    <input type="hidden" name="doencas_pre_existentes[{{ $i }}][doenca]" value="{{ $check }}">
                                                                    <p>{{ $check }}</p>
                                                                    <div class="pets"></div>

                                                                </div>
                                                            </li>
                                                            @php
                                                                $i++;
                                                            @endphp
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab3">
                        <h3 class="block">Marque o checklist</h3>
                        <div class="alert alert-warning">
                            <strong>Atenção!</strong>
                            <br>
                            Os itens do checklist devem ser lidos para o cliente por telefone
                            e é obrigatório que o cliente diga <strong>"SIM"</strong> para cada item lido
                        </div>

                        <div class="mt-element-list">
                            <div class="mt-list-head list-simple ext-1 font-white bg-green-sharp">
                                <div class="list-head-title-container">
                                    <h3 class="list-title">Checklist</h3>
                                </div>
                            </div>
                            <div class="mt-list-container list-simple ext-1">
                                <ul>
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach($checklistProposta as $check)
                                        <li class="mt-list-item done" style="border-left: none;">
                                            <div class="list-item-content" style="padding-left: 0;padding-right: 0">
                                                <h3 class="uppercase">
                                                    <div class="mt-checkbox-list">
                                                        <input type="hidden" name="checklist[{{ $i }}][item]" value="{{ $check }}">
                                                        <label class="mt-checkbox">
                                                            <input type="checkbox" id="checklist-{{ $i }}" name="checklist[{{ $i }}][ok]" class="md-check" required> {{ $check }}
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                    {{--<div class="md-checkbox">--}}
                                                        {{--<input type="hidden" name="checklist[{{ $i }}][item]" >--}}
                                                        {{--<input type="checkbox" id="checklist-{{ $i }}" name="checklist[{{ $i }}][ok]" class="md-check" required>--}}
                                                        {{--<label for="checklist-{{ $i }}">--}}
                                                            {{--<span></span>--}}
                                                            {{--<span class="check"></span>--}}
                                                            {{--<span class="box"></span>--}}
                                                            {{--{{ $check }}--}}
                                                        {{--</label>--}}
                                                    {{--</div>--}}
                                                </h3>
                                            </div>
                                        </li>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="tab4">
                        <h3 class="block">Pagamento</h3>
                        <div class="row">

                            <div class="col-md-12" >
                                <div class="form-group">
                                    <label class="control-label col-md-3">Valor</label>
                                    <span class="required"> * </span>
                                    <div class="col-md-5">
                                        <input id="valor_pagamento" name="cliente[pagamento][valor]" class="form-control mask-money" type="text" placeholder="" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">
                                        Forma de Pagamento
                                        <span class="required"> * </span>
                                    </label>
                                    <div class="col-md-2">
                                        <select name="cliente[pagamento][forma]" id="forma_primeiro_pagamento" class="form-control select-forma" >
                                            <option value="Cartão">Cartão</option>
                                            <option value="Boleto">Boleto</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="dados_cartao">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Número do Cartão</label>
                                        <div class="col-md-5">
                                            <input name="cliente[cartao][numero_cartao]" class="form-control" type="text" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Nome no Cartão</label>
                                        <div class="col-md-5">
                                            <input name="cliente[cartao][nome_cartao]" class="form-control" type="text"  placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Validade - MM/AA</label>
                                        <div class="col-md-5">
                                            <input name="cliente[cartao][validade]" class="form-control datec" type="text"  placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">CVV - 3 dígitos no verso</label>
                                        <div class="col-md-5">
                                            <input name="cliente[cartao][cvv]" class="form-control" type="text"  placeholder="" maxlength="3">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3" for="cliente[cartao][parcelas]">Parcelas</label>
                                        <div class="col-md-3">
                                            <select name="cliente[cartao][parcelas]" id="parcelas" class="form-control" required>
                                                @for($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}">{{ $i }}x</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <div class="alert alert-warning text-center">
                                        <strong>Atenção!</strong>
                                        {{--<br>--}}
                                        {{--O sistema financeiro ainda não tem a funcionalidade de parcelar uma compra. --}}
                                        {{--<br>--}}
                                        <br>
                                        Caso esta seja uma venda à vista, <b>selecione "Sim"</b> abaixo para gerar uma cobrança.
                                        <br>
                                        <br>
                                        Se a venda for parcelada, preencha os dados do cartão do cliente para futuras cobranças, 
                                        <br>
                                        <b>selecione "Não"</b> abaixo e use a <b>maquininha</b>.
                                        <br>
                                        Neste caso, apenas clique em cadastrar
                                        <br>
                                        Se a venda na maquininha for confirmada.
                                    </div>
    
                                    <div class="form-group">
                                        <label class="control-label col-md-3">
                                            Gerar cobrança?
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-3">
                                            <select 
                                                name="cliente[pagamento][gerar_cobranca]"
                                                id="gerar_cobranca"
                                                class='form-control'
                                                required
                                            >
                                                <option value="">Selecione uma opção abaixo:</option>
                                                <option value=""></option>
                                                <option value="nao">Não, não gerar uma cobrança</option>
                                                <option value="sim">Sim, gerar uma cobrança</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class='dados_boleto' style="display:none;">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Data de Vencimento
                                            <span class="required"> * </span>
                                        </label>
                                        <div class="col-md-6">
                                            <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                                                <input type="text" name="cliente[boleto][vencimento]" class="form-control" readonly required>
                                                <span class="input-group-btn">
                                                    <button class="btn default" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab5">
                        <h3 class="block">Cliente cadastrado!</h3>
                        <div class="row">
                            <div class="col-sm-offset-3 col-sm-6">
                                {{--<a href="#" class="btn green-haze btn-block btn-lg btn-download-proposta" target="_blank">--}}
                                    {{--<i class="fa fa-download"></i>--}}
                                    {{--Download da Proposta--}}
                                {{--</a>--}}
                                <div class="alert alert-warning">
                                    <strong>Atenção!</strong>
                                    <br>
                                    Não esqueça de conferir os dados do cliente, tanto no sistema (ERP) quanto no Sistema financeiro.
                                </div>
                                <a href="#" class="btn green-haze btn-block btn-lg btn-ver-proposta" target="_blank">
                                    <i class="fa fa-file-text-o"></i>
                                    Ver proposta do cliente
                                </a>
                                <a href="#" class="btn green-haze btn-block btn-lg btn-cliente-edit" target="_blank">
                                    <i class="fa fa-user"></i>
                                    Ver página do cliente
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-6">
                        <div class="btn-group btn-group-justified btn-group-actions">
                            <a href="javascript:;" class="btn form-control default button-previous">
                                <i class="fa fa-angle-left"></i>
                                Voltar
                            </a>
                            <a href="javascript:;" class="btn form-control green button-next">
                                Avançar
                                <i class="fa fa-angle-right"></i>
                            </a>
                            <a type="submit" class="btn form-control green-meadow button-submit">
                                Cadastrar!
                                <i class="fa fa-check"></i>
                            </a>
                            <a href="" class="btn btn-lg form-control blue button-end">
                                Finalizar e enviar email de boas vindas!
                                <i class="fa fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>