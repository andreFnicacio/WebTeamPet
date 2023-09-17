@php
    $readonly = isset($readonly) ? $readonly : false;
    $isCliente = \Entrust::hasRole(['CLIENTE']);
@endphp

<div class="form-body">

    <div class="row">
        <div class="col-xs-8">

            @unless($isCliente)
            <div class="form-group">
                <label class="control-label col-md-4">Status Financeiro
                </label>
                <div class="col-md-6">
                    @php
                        $statusContrato = $cliente->status_pagamento;
                    @endphp
                    @if(strtoupper($statusContrato) === strtoupper('Em dia'))
                        <span class="label label-sm label-success"> Contrato em dia </span>
                    @elseif(strtoupper($statusContrato) === strtoupper('Em atraso'))
                        <span class="label label-sm label-warning"> Em atraso </span>
                    @else
                        <span class="label label-sm label-danger"> Inadimplente +60 dias </span>
                    @endif
                </div>
            </div>
            @endunless
            <div class="form-group">
                <label class="control-label col-md-4">Matrícula
                </label>
                <div class="col-md-6">
                    <input type="text" value="{{ $cliente->id ? $cliente->id : "" }}" disabled readonly placeholder="Gerado Automaticamente"  class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Número do Contrato
                    <span class="required"> * </span>
                </label>
                <div class="col-md-6">
                    <input type="text" value="{{ $cliente->numero_contrato ? $cliente->numero_contrato : "" }}" name="numero_contrato"  class="form-control" required/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Nome ou Razão Social
                    <span class="required"> * </span>
                </label>
                <div class="col-md-6">
                    <input type="text" value="{{ $cliente->nome_cliente ? $cliente->nome_cliente : "" }}" name="nome_cliente" data-required="1" class="form-control" required/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Sexo
                    <span class="required"> * </span>
                </label>
                <div class="col-md-6">
                    <select name="sexo" id="sexo" class="form-control select2">
                        @foreach(["M", "F", "0"] as $s)
                            <option value="{{ $s }}" {{ $s === $cliente->sexo ? "selected" : "" }}>
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
                <label class="control-label col-md-4">CPF ou CNPJ
                    <span class="required"> * </span>
                </label>
                <div class="col-md-6">
                    <input value="{{ $cliente->cpf ? $cliente->cpf : "" }}" name="cpf" id="cpf" type="text" class="form-control cpf_cnpj" required/>
                    <small> Somente números. Sem traços, pontos ou barras.</small>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">RG
                </label>
                <div class="col-md-6">
                    <input value="{{ $cliente->rg ? $cliente->rg : "" }}" name="rg" type="text" class="form-control" />
                    <small> Somente números. Sem traços, pontos ou barras.</small>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">E-mail

                </label>
                <div class="col-md-6">
                    <input value="{{ $cliente->email ? $cliente->email : "" }}" name="email" type="text" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Celular
                    <span class="required"> * </span>
                </label>
                <div class="col-md-6">
                    <input value="{{ $cliente->celular ? $cliente->celular : "" }}" name="celular" type="text" class="form-control tel" required/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Telefone

                </label>
                <div class="col-md-6">
                    <input value="{{ $cliente->telefone ? $cliente->telefone : "" }}" name="telefone_fixo" type="text" class="form-control" />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Observações
                </label>
                <div class="col-md-6">
                    <textarea name="observacoes" type="text" class="form-control" >{{ $cliente->observacoes ? $cliente->observacoes : "" }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Data de Nasc. ou Fundação
                    <span class="required"> * </span>
                </label>
                <div class="col-md-6">
                    <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
                        <input type="text" value="{{ $cliente->data_nascimento ? $cliente->data_nascimento->format('d/m/Y') : "" }}" name="data_nascimento" class="form-control" readonly required>
                        <span class="input-group-btn">
                    <button class="btn default" type="button">
                        <i class="fa fa-calendar"></i>
                    </button>
                    </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4">Vencimento
                </label>
                <div class="col-md-6">
                    <select name="dia_vencimento" class="form-control" >
                        <option value=""></option>
                        @for($i = 1; $i <= 31; $i++)
                            <option value="{{ $i }}" {{ $cliente->dia_vencimento === $i ? "selected" : "" }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-4">Forma de Pagamento
                </label>
                <div class="col-md-6">
                    <select name="forma_pagamento" class="form-control" >
                        <option value=""></option>
                        <option {{ $cliente->forma_pagamento == "boleto" ? "selected" : "" }} value="boleto">Boleto</option>
                        <option {{ $cliente->forma_pagamento == "pix" ? "selected" : "" }} value="pix">Pix</option>
                        <option {{ $cliente->forma_pagamento == "cartao" ? "selected" : "" }} value="cartao">Cartão de Crédito</option>
                        <option {{ $cliente->forma_pagamento == "desconto_folha" ? "selected" : "" }} value="desconto_folha">Desconto em Folha</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12" >
                    <div class="col-md-12" style="margin-bottom: 20px;">
                        <h3 class="block" style="margin-top: 30px;">Dados do cadastro 
                            @if ($cliente->forma_pagamento == "cartao")
                                @if(!isset($info) || empty($info->data->cards))
                                <span class="label label-sm label-danger">Cartão não cadastrado</span>
                                @endif
                            @endif
                        </h3>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Status
                            <span class="required"> * </span>
                        </label>
                        @if($cliente->pets()->ativo()->count() && $cliente->ativo)
                            <div class="col-md-6">
                                <span class="badge badge-success">ATIVO</span>
                                <br>
                                <small>Clientes com PETS ativos não podem ser inativados.</small>
                            </div>
                        @else
                        <div class="col-md-6">
                            {{Form::hidden('ativo', 0)}}
                            <input type="checkbox" name="ativo" {{ $cliente->ativo ? "checked" : "" }} class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Ativo" data-off-text="Inativo" value="1" >
                        </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Data de Cadastro

                        </label>
                        <div class="col-md-6">
                            <div class="input-group input-medium date date-picker disabled disabled-hard"  data-date-format="dd/mm/yyyy">
                                <input type="text" class="form-control" value="{{ $cliente->created_at ? $cliente->created_at->format('d/m/Y') : "" }}" readonly>
                                <span class="input-group-btn">
                              <button class="btn default disabled" type="button">
                              <i class="fa fa-calendar"></i>
                              </button>
                          </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">ID Financeiro (SF)

                        </label>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input name="id_externo" value="{{ $cliente->id_externo ? $cliente->id_externo : "" }}" type="number" class="form-control" disabled/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            @if($cliente && $cliente->ativo)
                                <a class="btn btn-success" href="{{ route('clientes.finance.sync', ['id' => $cliente->id]) }}" type="submit" data-toggle="tooltip" data-original-title="Clique para sincronizar o cadastro com o SF."><i class="fa fa-refresh"></i></a>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">ID Superlógica

                        </label>
                        <div class="col-md-3 pr-0">
                            <div class="input-group">
                                <input id="id_superlogica" value="{{ $cliente->id_superlogica ? $cliente->id_superlogica : "Não sincronizado" }}" type="number" class="form-control" disabled/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            @if($cliente && $cliente->ativo)
                                <a class="btn btn-success" href="{{ route('clientes.superlogica.refresh', ['id' => $cliente->id]) }}" type="submit" data-toggle="tooltip" data-original-title="Clique para sincronizar o cadastro com o Superlógica."><i class="fa fa-refresh"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-12" >
                    <div class="col-md-12" style="margin-bottom: 20px;">
                        <h3 class="block" style="margin-top: 30px;">Endereço</h3>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">CEP
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-3">
                            <input name="cep" value="{{ $cliente->cep ? $cliente->cep : "" }}" type="text" class="form-control" required/>
                        </div>
                        <div class="col-md-2">
                            <a class="btn green-jungle btn-outline sbold address-search-trigger">Buscar</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Endereço
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input name="rua" id="logradouro" value="{{ $cliente->rua ? $cliente->rua : "" }}" placeholder="Rua"  class="form-control" required/>
                        </div>
                        <div class="col-md-2">
                            <input name="numero_endereco" value="{{ $cliente->numero_endereco ? $cliente->numero_endereco : "" }}" placeholder="Número" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Bairro
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-6">
                            <input name="bairro" id="bairro" value="{{ $cliente->bairro ? $cliente->bairro : "" }}" class="form-control" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Cidade/UF
                            <span class="required"> * </span>
                        </label>
                        <div class="col-md-4">
                            <input name="cidade" id="cidade" value="{{ $cliente->cidade ? $cliente->cidade : "" }}" class="form-control" required/>
                        </div>
                        <div class="col-md-2">
                            <select name="estado" id="uf" class="form-control">
                                @foreach($ufs as $uf)
                                    <option value="{{ $uf }}" {{ strtoupper($uf) == strtoupper($cliente->estado) ? "selected" : "" }}>{{ $uf }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Complemento

                        </label>
                        <div class="col-md-6">
                            <input name="complemento_endereco" id="cidade" value="{{ $cliente->complemento_endereco ? $cliente->complemento_endereco : "" }}" class="form-control"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-4">
            @unless($isCliente)
                <div class="well">
                    <span class="label label-danger">Não é Cliente</span>
                </div>
            @endunless
        </div>
    </div>
    <div class="alert alert-danger display-hide">
        <button class="close" data-close="alert"></button> Verifique se você preencheu todos os campos.
    </div>
    <div class="alert alert-success display-hide">
        <button class="close" data-close="alert"></button> Your form validation is successful!
    </div>

</div>
