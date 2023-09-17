<div class="tela" id="bemvindo">
    <div class="dados-resumo text-white">

        <h1>
            <strong>Resumo do Cadastro</strong>
        </h1>

        <h2>
            <strong>Dados do Cliente</strong>
        </h2>

        <p><strong>Nome: </strong><span>{{ $cliente->nome_cliente }}</span></p>
        <p><strong>Sexo: </strong><span>{{ $cliente->sexo == 'M' ? 'Masculino' : 'Feminino' }}</span></p>
        <p><strong>CPF/CNPJ: </strong><span>{{ $cliente->cpf }}</span></p>
        <p><strong>RG: </strong><span>{{ $cliente->rg }}</span></p>
        <p><strong>Email: </strong><span>{{ $cliente->email }}</span></p>
        <p><strong>Celular: </strong><span>{{ $cliente->celular }}</span></p>
        <p><strong>Telefone Fixo: </strong><span>{{ $cliente->telefone_fixo }}</span></p>
        <p><strong>Data de Nascimento: </strong><span>{{ $cliente->data_nascimento->format('d/m/Y') }}</span></p>
        <p><strong>Foto do RG (Frente): </strong><span class="text-danger">INSERIR IMAGEM</span></p>
        <p><strong>Foto do RG (Verso): </strong><span class="text-danger">INSERIR IMAGEM</span></p>

        <h2>
            <strong>Endereço</strong>
        </h2>
        <p><strong>CEP: </strong><span>{{ $cliente->cep }}</span></p>
        <p><strong>Rua: </strong><span>{{ $cliente->rua }}</span></p>
        <p><strong>Número: </strong><span>{{ $cliente->numero_endereco }}</span></p>
        <p><strong>Complemento: </strong><span>{{ $cliente->complemento_endereco }}</span></p>
        <p><strong>Bairro: </strong><span>{{ $cliente->bairro }}</span></p>
        <p><strong>Cidade: </strong><span>{{ $cliente->cidade }}</span></p>
        <p><strong>Estado: </strong><span>{{ $cliente->estado }}</span></p>

    </div>

    <div class="wrapper-signature">
        <canvas id="signature-pad-cliente" class="signature-pad" width=700 height=200></canvas>
        <div>
            <button class="btn btn-default signature-save" onclick="signatureSave(signaturePadCliente, $(this))"><i class="fa fa-check"></i> Confirmar</button>
            <button class="btn btn-default signature-remake" onclick="signatureRemake(signaturePadCliente, $(this))" style="display: none;"><i class="fa fa-retweet"></i> Refazer</button>
            <button class="btn btn-default signature-clear" onclick="signatureClear(signaturePadCliente)"><i class="fa fa-eraser"></i> Limpar Tudo</button>
            <button class="btn btn-default signature-undo" onclick="signatureUndo(signaturePadCliente)"><i class="fa fa-undo"></i> Refazer</button>
        </div>
    </div>

    <div class="col-md-12">
        <a class="btlaranja" onclick="avanca_tela(signaturePadCliente)">Avançar</a>
    </div>
</div>