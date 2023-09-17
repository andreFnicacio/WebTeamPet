<html>
    <body>
        <form name="indique" class="indique" action="./formArray" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <h2>Cadastro:</h2>
            <input name="cliente[nome_cliente]" type="text" placeholder="Nome" >
            <input name="cliente[email]" type="text"  placeholder="E-mail" >
            <input name="cliente[telefone_fixo]" class="phone" type="text"  placeholder="Telefone com DDD" >
            <input name="cliente[celular]" class="sp_celphones" type="text" placeholder="Celular com DDD" >
            <select name="cliente[sexo]"  >
                <option selected="true" value="" disabled="disabled">Sexo</option>
                <option value="Masculino" >Masculino</option>
                <option value="Feminino">Feminino</option>
            </select>

            <input name="cliente[rg]" type="text" placeholder="RG" >

            <input class="cpf" id="cpf" maxlength="14" name="cliente[cpf]" placeholder="CPF" type="text">
            <input name="cliente[data_nascimento]" class="date" id="data_nascimento"  type="text" placeholder="Data de Nasc." >

            <h3 style="margin-top: 10px;">Foto do RH ou CNH Frente:</h3>
            <input type="file" id="rg_frente" name="cliente[rg_frente]" style="width: 80%;" accept="image/*" capture="camera" placeholder="Carteira de Vacinação">

            <h3 style="margin-top: 10px;">Foto do RH ou CNH Verso:</h3>
            <input type="file" id="rg_verso" name="cliente[rg_verso]" style="width: 80%;" accept="image/*" capture="camera" placeholder="Carteira de Vacinação">

            <div class="avancar col-md-12">
                <a class="btlaranja avanca dadospet" id="avanca-dadospet">Avançar</a>
                <a class="btbranco volta">Voltar</a>
            </div>

            {{--<h3 style="padding: 20px 0px 0px 0px;">Indique amigo 2:</h3>--}}
            {{--<input name="nome_amgigo2" type="text" placeholder="Nome" >--}}
            {{--<input name="celular_amigo2" type="text"  placeholder="Celular" >--}}
            {{--<input name="email_amigo2" type="text"  placeholder="E-mail" >--}}

            {{--<h3 style="padding: 20px 0px 0px 0px;">Indique amigo 3:</h3>--}}
            {{--<input name="nome_amgigo3" type="text" placeholder="Nome" >--}}
            {{--<input name="celular_amigo3" type="text"  placeholder="Celular" >--}}
            {{--<input name="email_amigo2" type="text"  placeholder="E-mail" >--}}

            <input type="submit">
        </form>
    </body>
</html>