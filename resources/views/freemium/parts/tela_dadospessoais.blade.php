<div class="tela" id="dadospessoais">

    <div class="conheca col-md-12">

        <h3 style="padding-top: 5px; padding-bottom: 30px;"><strong>Bem-vindo(a) ao Plano Fácil Participativo</strong>
            <br><br>Para fazer sua adesão é rápido, simples e seguro: basta preencher os campos abaixo e aguardar nossa aprovação.</h3>

        <h2>Dados Pessoais:</h2>


        <select name="pets" id="pets">
            <option value="1">QTD. DE PETS</option>
            @for($i = 1; $i < 11; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>
        <input name="cliente[nome_cliente]" type="text" placeholder="Nome*" required data-description="Nome">
        <input name="cliente[email]" type="text"  placeholder="E-mail*" required data-description="Email">
        <input name="cliente[celular]" class="sp_celphones" type="text" placeholder="Celular com DDD*" required data-description="Telefone celular">
        <select name="cliente[sexo]" required data-description="Sexo">
            <option selected="true" value="" disabled="disabled">Sexo*</option>
            <option value="Masculino" >Masculino</option>
            <option value="Feminino">Feminino</option>
        </select>

        <input name="cliente[rg]" type="text" placeholder="RG*" required data-description="RG">

        <input class="cpf" id="cpf" data-description="CPF" maxlength="14" name="cliente[cpf]" placeholder="CPF*" type="text" required>
        <input name="cliente[data_nascimento]" data-description="Data de Nascimento" class="date" id="data_nascimento"  type="text" placeholder="Data de Nasc.*" required>

        {{--<h3 style="margin-top: 10px;">Foto do RG ou CNH Frente*</h3>--}}
        {{--<input type="file" id="rg_frente" name="cliente[rg_frente]" style="width: 80%;" accept="image/*" capture="camera" placeholder="Frente do RG" required data-description="Foto da frente do RG">--}}

        {{--<h3 style="margin-top: 10px;">Foto do RG ou CNH Verso*</h3>--}}
        {{--<input type="file" id="rg_verso" name="cliente[rg_verso]" style="width: 80%;" accept="image/*" capture="camera" placeholder="Verso do RG" required data-description="Foto do verso do RG">--}}
        <br><br>
        <h2>Indique um amigo:</h2>
        <input type="text" name="indicacao[nome]" placeholder="Nome do amigo" required data-description="Nome do amigo indicado">
        <input type="email" name="indicacao[email]" placeholder="Email do amigo" required data-description="Email do amigo indicado">
        <input type="text" name="indicacao[celular]" placeholder="Celular do amigo" required data-description="Celular do amigo indicado">
        <h2>Dados do Cartão:</h2>
        <input name="cartao[numero_cartao]" type="text" class="dated"  placeholder="Número do Cartão" required data-description="Número do cartão">
        <input name="cartao[nome_cartao]" type="text"  placeholder="Nome no Cartão" required data-description="Nome no cartão">
        <input name="cartao[validade]" class="datec" type="text"  placeholder="Validade - MM/AA" required data-description="Validade do cartão">
        <input name="cartao[cvv]" class="" type="text"  placeholder="CVV - 3 dígitos no verso" required data-description="Código de segurança do cartão">
        <div style="background-color:#fff;color: #999;font-size:10px;padding: 5px 21px 5px 20px;text-align: center !important;border-radius:10px;">
            <p style="
    margin-bottom: 0px;
">Cadastro Seguro:</p>
            <img src="https://www.lifepet.com.br/wp-content/uploads/2018/02/seguro.png" width="100%" style="max-width: 200px;text-align: center;margin-left: 42px;">
        </div>
        <h2 style="margin-top: 30px;"><b style="font-weight: 200;">Total da Adesão:</b>
            <span id="valor-adesao">
                @if(!(new \Carbon\Carbon())->gt(\Carbon\Carbon::createFromFormat('Y-m-d H:i', '2018-05-11 23:59')))
                    GRÁTIS
                @else
                    {{ \App\Helpers\Utils::money(\App\Http\Controllers\FreemiumController::VALOR_ADESAO) }}
                @endif
            </span>
        </h2>
        <h3 style="padding-top: 5px; padding-bottom: 30px;"><strong>Por que devo fornecer os dados do meu cartão?</strong>
            <br><br>A Lifepet cobrará apenas a adesão agora e não cobrará nenhum valor de mensalidade no seu cartão durante um ano, salvo em caso de uso do plano. A solicitação do cartão é uma segurança para que você tenha uma liberação imediata do atendimento quando necessário.</h3>

        <div class="col-md-12" style="
                                    margin-bottom: 40px;
                                    display: table;
                                    text-align: center !important;
                                ">
            <div style="margin-top:5px; clear: both; height: 25px;"><input type="checkbox" checked disabled name="aceite_contrato" id="aceite_contrato" value="1" class="" style="
                                    float: left;
                                    width: auto;
                                    display: inline;
                                    vertical-align: middle;
                                    margin-bottom: 0 !important;
                                    margin-top: 8px;
                                    margin-right: 15px;

                                ">
            <span style="
                                    display: inline-block;
                                    float: left;
                                    width: 80%;
                                    color: white;
                                    text-align: left;
                                ">Li e aceito o <a href="#contrato" data-toggle="modal" style="color: #ff8400">contrato.</a></span></div>
            <div style="margin-top:5px; clear: both;  height: 25px;"><input type="checkbox" checked disabled name="aceite_contrato" id="aceite_contrato" value="1" class="" style="
                                    float: left;
                                    width: auto;
                                    display: inline;
                                    vertical-align: middle;
                                    margin-bottom: 0 !important;
                                    margin-top: 8px;
                                    margin-right: 15px;
                                ">
            <span style="
                                    display: inline-block;
                                    float: left;
                                    width: 80%;
                                    color: white;
                                    text-align: left;
                                ">Li e aceito o <a href="#regulamento" data-toggle="modal" style="color: #ff8400">regulamento.</a></span></div>
        </div>
        <div class="avancar col-md-12">
            <a class="btlaranja pagar" id="#pagar">Cadastrar</a>
        </div>
        {{--<div class="avancar col-md-12">--}}
            {{--<a class="btlaranja avanca dadospet" id="avanca-dadospet" data-context="#dadospessoais">Avançar</a>--}}
            {{--<a class="btbranco volta">Voltar</a>--}}
        {{--</div>--}}

    </div>

</div>