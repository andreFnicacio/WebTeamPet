<form action="#" method="post" id="dadospessoais" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input autocomplete="off" type="hidden" name="assinatura" class="assinatura">
    <input autocomplete="off" type="hidden" name="id_cliente">

    <div class="tela">

        <div class="modal fade" id="modal-dadospessoais" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog resumo-modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle">Confirmação de dados pessoais</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <p style="margin-bottom: 0;" class="cliente[nome_cliente]"><strong>Nome:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[sexo]"><strong>Sexo:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[cpf]"><strong>CPF ou CNPJ:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[rg]"><strong>RG:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[email]"><strong>Email:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[celular]"><strong>Celular com DDD:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[telefone_fixo]"><strong>Telefone com DDD:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[data_nascimento]"><strong>Data de Nascimento ou Fundação:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[observacoes]"><strong>Observações:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[cep]"><strong>CEP:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[rua]"><strong>Rua:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[numero_endereco]"><strong>Número:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[complemento_endereco]"><strong>Complemento:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[bairro]"><strong>Bairro:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[cidade]"><strong>Cidade:</strong> <span></span></p>
                        <p style="margin-bottom: 0;" class="cliente[estado]"><strong>Estado:</strong> <span></span></p>

                        <h4 style="margin-top: 50px;color: #000;">Confirme os dados com a Assinatura Digital:*</h4>
                        <div class="wrapper-signature">
                            <canvas id="signature-pad-cliente" class="signature-pad" width=700 height=200></canvas>
                            <div style="margin-top: 5px;">
                                <a class="btn btn-default signature-save" onclick="signatureSave(signaturePadCliente, $(this))"><i class="fa fa-check"></i> Confirmar</a>
                                <a class="btn btn-default signature-remake" onclick="signatureRemake(signaturePadCliente, $(this))" style="display: none;"><i class="fa fa-retweet"></i> Refazer</a>
                                <a class="btn btn-default signature-clear" onclick="signatureClear(signaturePadCliente)"><i class="fa fa-eraser"></i> Limpar Tudo</a>
                                <a class="btn btn-default signature-undo" onclick="signatureUndo(signaturePadCliente)"><i class="fa fa-undo"></i> Refazer</a>
                            </div>
                        </div>

                        {{--<h4 style="padding-top: 30px;color: #000;">Anexar arquivo da Assinatura*</h4>--}}
                        {{--<input autocomplete=off" type="file" id="assinatura" name="assinatura" style="width: 80%;" accept="image/*" data-text="Foto..." data-required=true" data-buttonName="btn-primary" data-description="Arquivo da Assinatura">--}}

                        <h4 style="padding-top: 30px;color: #000;">Quantidade de pets*</h4>
                        <div class="form-group">
                            <select autocomplete="off" class="" name="qtd_pets" data-required="true" data-description="Quantidade de pets">
                                <option value="1" >1</option>
                                <option value="2" >2</option>
                                <option value="3" >3</option>
                                <option value="4" >4</option>
                                <option value="5" >5</option>
                                <option value="6" >6</option>
                                <option value="7" >7</option>
                                <option value="8" >8</option>
                                <option value="9" >9</option>
                                <option value="10" >10</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{--<a class="btn btn-" data-dismiss="modal">Close</a>--}}
                        <a class="btn btlaranja" onclick="salvarDadosCliente(signaturePadCliente, event)">Confirmar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="conheca col-md-12">

            <h2>Dados Pessoais:</h2>


            <h3 class="form-label">CPF ou CNPJ*</h3>
            <div class="form-inline group-buscacpf">
                <div class="form-group" style="width: 70%; ">
                    <input autocomplete="off" class="cpf" name="cliente[cpf]" type="tel" id="cpf" data-description="CPF ou CNPJ" maxlength="18" data-required="true">
                </div>
                <a class="btlaranja btnBuscaCPF">
                    <i class="fa fa-search"></i> Buscar
                </a>
            </div>


            <div class="form-group">
                <label class="form-label">Nome ou Razão Social*</label>
                <input autocomplete="off" class="" name="cliente[nome_cliente]" type="text" data-required="true" data-description="Nome ou Razão Social" maxlength="80">
            </div>

            <div class="form-group">
                <label class="form-label">Sexo*</label>
                <select autocomplete="off" class="" name="cliente[sexo]" data-required="true" data-description="Sexo">
                    <option selected="true" value="" disabled="disabled"></option>
                    <option value="Masculino" >Masculino</option>
                    <option value="Feminino">Feminino</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">RG</label>
                <input autocomplete="off" class="" name="cliente[rg]" type="tel" data-description="RG">
            </div>

            <div class="form-group">
                <label class="form-label">E-mail*</label>
                <input autocomplete="off" class="" name="cliente[email]" type="email"  data-required="true" data-description="Email">
            </div>

            <div class="form-group">
                <label class="form-label">Celular com DDD*</label>
                <input autocomplete="off" class=" sp_celphones" name="cliente[celular]" type="tel" data-required="true" data-description="Telefone celular">
            </div>

            <div class="form-group">
                <label class="form-label">Telefone com DDD</label>
                <input autocomplete="off" class=" phone" name="cliente[telefone_fixo]" type="tel" data-description="Telefone">
            </div>

            <div class="form-group">
                <label class="form-label">Data de Nasc. ou Fundação*</label>
                <input autocomplete="off" class=" date" name="cliente[data_nascimento]" type="tel" data-description="Data de Nascimento ou Fundação" id="data_nascimento" data-required="true"d>
            </div>

            {{--<div class="form-group">--}}
                {{--<label class="form-label">Observações</label>--}}
                {{--<input autocomplete="off" class="" name="cliente[observacoes]" type="text" data-description="Observações">--}}
            {{--</div>--}}

            <h3>Foto do RG ou CNH Frente*</h3>
            <input autocomplete="off" type="file" id="rg_frente" name="cliente[rg_frente]" data-style="color: #FFF" style="width: 80%;" accept="image/*" data-text="Foto..." placeholder="Frente do RG" data-required="true" data-description="Foto da frente do RG">

            <h3>Foto do RG ou CNH Verso*</h3>
            <input autocomplete="off" type="file" id="rg_verso" name="cliente[rg_verso]" data-style="color: #FFF" style="width: 80%;" accept="image/*" data-text="Foto..." placeholder="Verso do RG" data-required="true" data-description="Foto do verso do RG">
            <br>

            <h2>Endereço:</h2>

            <div class="form-group">
                <label class="form-label">CEP*</label>
                <input autocomplete="off" class=" cep address-search-trigger-blur" name="cliente[cep]" type="tel" data-required="true" data-description="CEP">
            </div>
            <div class="form-group">
                <label class="form-label">Núm.*</label>
                <input autocomplete="off" class="" name="cliente[numero_endereco]" type="tel" data-required="true" data-description="Número da casa">
            </div>
            <div class="form-group">
                <label class="form-label">Complemento</label>
                <input autocomplete="off" class="" name="cliente[complemento_endereco]" type="text" data-description="Complemento de endereço">
            </div>
            <div class="form-group">
                <label class="form-label">Rua*</label>
                <input autocomplete="off" class="" name="cliente[rua]" id="logradouro" type="text" data-required="true" data-description="Rua">
            </div>
            <div class="form-group">
                <label class="form-label">Bairro*</label>
                <input autocomplete="off" class="" name="cliente[bairro]" id="bairro" type="text" data-description="Bairro" data-required="true"d>
            </div>
            <div class="form-group">
                <label class="form-label">Cidade*</label>
                <input autocomplete="off" class="" name="cliente[cidade]" id="cidade" type="text" data-required="true" data-description="Cidade">
            </div>

            <div class="form-group">
                <label class="form-label">Estado*</label>
                <select autocomplete="off" class="" name="cliente[estado]" id="uf" data-required="true" data-description="Estado">
                    <option value="AC">AC</option>
                    <option value="AL">AL</option>
                    <option value="AP">AP</option>
                    <option value="AM">AM</option>
                    <option value="BA">BA</option>
                    <option value="CE">CE</option>
                    <option value="DF">DF</option>
                    <option value="ES" selected>ES</option>
                    <option value="GO">GO</option>
                    <option value="MA">MA</option>
                    <option value="MT">MT</option>
                    <option value="MS">MS</option>
                    <option value="MG">MG</option>
                    <option value="PA">PA</option>
                    <option value="PB">PB</option>
                    <option value="PR">PR</option>
                    <option value="PE">PE</option>
                    <option value="PI">PI</option>
                    <option value="RJ">RJ</option>
                    <option value="RN">RN</option>
                    <option value="RS">RS</option>
                    <option value="RO">RO</option>
                    <option value="RR">RR</option>
                    <option value="SC">SC</option>
                    <option value="SP">SP</option>
                    <option value="SE">SE</option>
                    <option value="TO">TO</option>
                </select>
            </div>

            <h3>Foto do comprovante de residência</h3>
            <input autocomplete="off" type="file" id="comp_residencia" name="cliente[comp_residencia]" style="width: 80%;" accept="image/*" data-text="Foto..." data-description="Foto do comprovante de residência">

            {{--<div class="col-md-12" style="--}}
                                        {{--margin-bottom: 40px;--}}
                                        {{--display: table;--}}
                                        {{--text-align: center !important;--}}
                                    {{--">--}}
                {{--<div style="margin-top:5px; clear: both; height: 25px;">--}}
                    {{--<input autocomplete=off" type="checkbox" checked disabled name="aceite_contrato" id="aceite_contrato" value="1" style="--}}
                                        {{--float: left;--}}
                                        {{--width: auto;--}}
                                        {{--display: inline;--}}
                                        {{--vertical-align: middle;--}}
                                        {{--margin-bottom: 0 !important;--}}
                                        {{--margin-top: 8px;--}}
                                        {{--margin-right: 15px;--}}
                                    {{--">--}}
                    {{--<span style="--}}
                                        {{--display: inline-block;--}}
                                        {{--float: left;--}}
                                        {{--width: 80%;--}}
                                        {{--color: white;--}}
                                        {{--text-align: left;--}}
                                    {{--">Li e aceito o <a href="#contrato" data-toggle="modal" style="color: #ff8400">contrato.</a></span>--}}
                {{--</div>--}}
                {{--<div style="margin-top:5px; clear: both;  height: 25px;">--}}
                    {{--<input autocomplete=off" type="checkbox" checked disabled name="aceite_contrato" id="aceite_contrato" value="1" style="--}}
                                        {{--float: left;--}}
                                        {{--width: auto;--}}
                                        {{--display: inline;--}}
                                        {{--vertical-align: middle;--}}
                                        {{--margin-bottom: 0 !important;--}}
                                        {{--margin-top: 8px;--}}
                                        {{--margin-right: 15px;--}}
                                    {{--">--}}
                    {{--<span style="--}}
                                        {{--display: inline-block;--}}
                                        {{--float: left;--}}
                                        {{--width: 80%;--}}
                                        {{--color: white;--}}
                                        {{--text-align: left;--}}
                                    {{--">Li e aceito o <a href="#regulamento" data-toggle="modal" style="color: #ff8400">regulamento.</a></span>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="avancar col-md-12">--}}
                {{--<a class="btlaranja" id="pagar">Cadastrar</a>--}}
            {{--</div>--}}
            <a class="btlaranja" onclick="launchModal($('#dadospessoais'))">
                Confirmar
            </a>
            {{--<div class="avancar col-md-12">--}}
                {{--<a class="btlaranja avanca dadospet" id="avanca-dadospet" data-context="#dadospessoais">Avançar</a>--}}
                {{--<a class="btbranco volta">Voltar</a>--}}
            {{--</div>--}}

        </div>

    </div>
</form>