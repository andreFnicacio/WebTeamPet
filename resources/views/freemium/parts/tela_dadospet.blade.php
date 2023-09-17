<div class="tela" id="dadospet">
    <div class="conheca col-md-12">
        <ul id="lista_pets"></ul>
        <div class="pets-container" id="pets_container">

        </div>

        <h2>Dados do pet:</h2>
        <div id="dados_pet">
            <select name="tipo" id="tipo_pet" required data-description="Tipo de pet">
                <option selected="true" value="" disabled="disabled">Tipo*</option>
                <option value="cachorro" >Cão</option>
                <option value="gato">Gato</option>
            </select>
            <input name="raca" type="text" placeholder="Raça*" required data-description="Raça do pet">
            <input name="nome_pet" id="nome_pet" type="text"  placeholder="Nome do Pet" required data-description="Nome do pet">
            <select name="contem_doenca_pre_existente" required>
                <option selected="true" value="0" disabled="disabled">Possui alguma doença?</option>
                <option value="0" >Não</option>
                <option value="1">Sim</option>
            </select>
            <input class="" type="text"  placeholder="Cite as doenças (caso existam)." name="doencas_pre_existentes" >
            <h3 style="margin-top: 10px;">Foto da carteirinha de vacinação (caso tenha)</h3>
            <input type="file" id="foto_carteirinha_vacinacao" style="width: 80%;" accept="image/*" capture="camera" placeholder="Carteira de Vacinação" name="carteira_vacinacao" data-description="Foto da carteirinha de vacinação do pet">
        </div>
        <div class="avancar col-md-12">
            <a class="btlaranja addpet dadospet" >Salvar Pet</a>
        </div>

    </div>

</div>