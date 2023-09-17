<div class="tela" id="endereco">

    <div class="conheca col-md-12">

        <h2>Endereço:</h2>
        <input name="cliente[cep]" type="text" class="cep" placeholder="CEP*" required data-description="CEP">
        <input name="cliente[rua]" type="text"  placeholder="Rua*" required data-description="Rua">
        <input name="cliente[numero_endereco]" class="" type="text"  placeholder="Núm.*" required data-description="Número da casa">
        <input name="cliente[complemento]" class="" type="text"  placeholder="Complemento*" required data-description="Complemento de endereço">
        <input name="cliente[bairro]" class="" type="text" placeholder="Bairro*" data-description="Bairro" required>
        <select name="cliente[cidade]" id="cidade" class=" col-md-12" style="float:left; padding-left: 5px;" required data-description="Cidade">
            <option value="Vitória">Vitória/ES</option>
            <option value="Vila Velha">Vila Velha/ES</option>
            <option value="Cariacica">Cariacica/ES</option>
            <option value="Serra">Serra/ES</option>
            <option value="Guarapari">Guarapari/ES</option>
        </select>
        <!--<input name="cliente[cidade]" class="col-md-9" style="float:left; padding-left: 5px;" type="text" placeholder="Cidade*" required data-description="Cidade">-->
        <select name="cliente[estado]" id="uf" class=" col-md-3" style="margin-top: 2px; display: none; " required data-description="Estado">
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

        <h3 style="margin-top: 10px;">Foto do comprovante de residência*</h3>
        <input type="file" id="comp_residencia" name="cliente[comp_residencia]" style="width: 80%;" accept="image/*" capture="camera" placeholder="" required data-description="Foto do comprovante de residência">

        <div class="avancar col-md-12">
            <a class="btlaranja cadastrapet avanca dadospet" data-context="#endereco">Cadastrar Pets</a>
            <a class="btbranco volta dadospet">Voltar</a>
        </div>

    </div>

</div>