<div class="tela" id="bemvindo">
    <div class="dados-resumo text-white col-md-12">

        <h1>
            <strong>Proposta de Adesão</strong>
        </h1>

        <h2>
            <strong>1) Dados do Cliente</strong>
        </h2>

        <p><strong>Nome: </strong><span>{{ $cliente->nome_cliente }}</span></p>
        <p><strong>Sexo: </strong><span>{{ $cliente->sexo == 'M' ? 'Masculino' : 'Feminino' }}</span></p>
        <p><strong>CPF/CNPJ: </strong><span>{{ $cliente->cpf }}</span></p>
        <p><strong>RG: </strong><span>{{ $cliente->rg }}</span></p>
        <p><strong>Email: </strong><span>{{ $cliente->email }}</span></p>
        <p><strong>Celular: </strong><span>{{ $cliente->celular }}</span></p>
        <p><strong>Telefone Fixo: </strong><span>{{ $cliente->telefone_fixo }}</span></p>
        <p><strong>Data de Nascimento: </strong><span>{{ $cliente->data_nascimento->format('d/m/Y') }}</span></p>
        <p><strong>CEP: </strong><span>{{ $cliente->cep }}</span></p>
        <p><strong>Rua: </strong><span>{{ $cliente->rua }}</span></p>
        <p><strong>Número: </strong><span>{{ $cliente->numero_endereco }}</span></p>
        <p><strong>Complemento: </strong><span>{{ $cliente->complemento_endereco }}</span></p>
        <p><strong>Bairro: </strong><span>{{ $cliente->bairro }}</span></p>
        <p><strong>Cidade: </strong><span>{{ $cliente->cidade }}</span></p>
        <p><strong>Estado: </strong><span>{{ $cliente->estado }}</span></p>

        <h2>
            <strong>
                @if($pets->count() > 1)
                    2) Pets
                @else
                    2) Pet
                @endif
            </strong>
        </h2>

        @foreach($pets as $pet)
            <input type="hidden" name="pets[{{ $pet->id }}]" value="{{ $pet->id }}">
            <div class="card text-dark">
                <p><strong>Nome: </strong><span>{{ $pet->nome_pet }}</span></p>
                <p><strong>Tipo: </strong><span>{{ $pet->tipo }}</span></p>
                <p><strong>Sexo: </strong><span>{{ $pet->sexo }}</span></p>
                {{--<p><strong>Raça: </strong><span>{{ $pet->raca->nome }}</span></p>--}}
                <p><strong>Data de Nascimento: </strong><span>{{ $pet->data_nascimento->format('d/m/Y') }}</span></p>
                <p><strong>Contém Doença Pré Existente?: </strong><span>{{ $pet->contem_doenca_pre_existente ? 'Sim' : 'Não' }}</span></p>
                <p><strong>Doenças Pré Existentes: </strong><span>{{ $pet->doencas_pre_existentes }}</span></p>
                <p><strong>Observações: </strong><span>{{ $pet->observacoes }}</span></p>
                <p><strong>Plano: </strong><span>{{ $pet->plano()->nome_plano }}</span></p>
                <p><strong>Participativo?: </strong><span>{{ $pet->participativo ? 'Sim' : 'Não' }}</span></p>
                <p><strong>Familiar?: </strong><span>{{ $pet->familiar ? 'Sim' : 'Não' }}</span></p>
                <p><strong>Data de Início do Contrato: </strong><span>{{ $pet->petsPlanos()->first()->data_inicio_contrato->format('d/m/Y') }}</span></p>
                <p><strong>Vendedor: </strong><span>{{ $pet->petsPlanos()->first()->vendedor()->nome }}</span></p>
                <p><strong>Valor da Adesão: </strong><span>{{ \App\Helpers\Utils::money($pet->petsPlanos()->first()->adesao) }}</span></p>
                <p><strong>Valor do Plano: </strong><span>R$ {{ \App\Helpers\Utils::money($pet->petsPlanos()->first()->valor_momento) }}</span></p>
                <p><strong>Regime: </strong><span>{{ $pet->regime }}</span></p>
            </div>
        @endforeach

        <h2>
            <strong>3) Checklist</strong>
        </h2>
        <h3>
            Prezado cliente, solicitamos que leia atentamente estes itens, para que sejam esclarecidos os principais pontos sobre sua adesão à Lifepet. Vamos lá:
        </h3>
        @php
            $checklist = [
                "Em hipótese alguma o Pet será atendido sem o microchip (ainda que seja caso de emergência);",
                "Para a realização da microchipagem é necessário que o Pet esteja com a carteira de vacinação em dia. Sendo filhote, deve estar sadio e ter mais de 60 dias;",
                "Os atendimentos em caso de urgência e emergência só poderão ser realizados 48h após a microchipagem do Pet;",
                "Coberturas e Carências constam na Área do Cliente que está disponível no site: www.lifepet.com.br;",
                "Doenças e males preexistentes (CPT) terão cobertura após 12 meses de contrato ininterrupto;",
                "No caso de atraso de pagamento, o serviço é automaticamente SUSPENSO (inclusive para urgências e emergências);",
                "No caso de atraso superior a 60 dias, o plano é automaticamente CANCELADO, permanecendo as cobranças de mensalidades vencidas;",
                "Após 60 dias de inadimplência o débito em aberto poderá ser inscrito no SPC e SERASA (desde que previamente comunicado);",
                "Os pagamentos realizados demoram até 72 horas para serem reconhecidos pelo banco;",
                "REDE CREDENCIADA: www.lifepet.com.br/rede;",
                "No prazo de até 7 dias após pagamento será enviado ao e-mail de cadastro, os seguintes documentos: i) Carta de boas vindas; ii) Login e senha da Área do Cliente;",
                "Caso o animal venha a óbito ou haja desistência do plano não haverá restituição (caso seja beneficiado com algum desconto);",
                "Fidelidade: o contrato possui fidelidade de 12 meses, podendo ser cancelado sem multa rescisória caso não tenha utilizado qualquer procedimento.",
                "Reconheço que sou responsável por todas as informações declaradas.",
                "Declaro que estou ciente e de acordo com a Tabela de Cobertura e Carências e com o Contrato do Cliente.",
                "Reconheço que fui orientado pelo representante quanto às informações acima."
            ];
            $i = 0;
        @endphp
        <hr>

        <form action="#" method="post" id="dadosresumo" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="idCliente" class="idCliente" value="{{ $cliente->id }}">
            <input type="hidden" name="proposta" class="proposta">
            <input type="text" style="display: none;" name="checklist" value="" required data-description="Checklist">

            @foreach($pets as $pet)
                <input type="hidden" name="idPets[{{ $pet->id }}]" value="{{ $pet->id }}">
            @endforeach

            <div class="containerChecklist">
                @foreach($checklist as $check)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="check-{{ ++$i }}" onclick="clickChecklist()">
                        <label class="form-check-label" for="check-{{ $i }}">
                            {{ $check }}
                        </label>
                    </div>
                    <hr>
                @endforeach
            </div>

            <h2>
                <strong>Forma de Pagamento*</strong>
            </h2>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="forma-pagamento" id="pgto-cartao" value="Cartão" checked>
                <label class="form-check-label" for="pgto-cartao">Cartão</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="forma-pagamento" id="pgto-boleto" value="Boleto">
                <label class="form-check-label" for="pgto-boleto">Boleto</label>
            </div>

            <h2>
                <strong>Foto do comprovante de pagamento</strong>
            </h2>
            <input type="file" id="comp_pagamento" name="comp_pagamento" style="width: 80%;" accept="image/*" data-text="Foto..."  data-description="Foto do comprovante de pagamento">

            <h2>
                <strong>4) Declaração de Saúde</strong>
            </h2>
            @php
                $questoes = [
                    "Sofre(u) de alguma doença infecciosa ou parasitária: erlichia ou anaplasma (doença do carrapato), hepatite, meningite, infecções virais, entre outros? Especifique.",
                    "Sofre(u) de algum tipo de neoplasia (câncer)? Especifique.",
                    "Sofre(u) de alguma doença no sangue (anemias)? Especifique.",
                    "É portador(a) de alguma doença endócrina (diabetes, hiperadrenocorticismo, hipotireoidismo, entre outras)? Especifique.",
                    "Sofre(u) de alguma doença do sistema nervoso (convulsões, ataxias, entre outras)? Especifique.",
                    "Alguma afecção dermatológica? (atopia, DAPE, Sarna)? Especifique.",
                    "É portador de alguma enfermidade circulatória (sopro, arritmia, hipertensão)? Especifique.",
                    "Sofre(u) algum problema em ouvido? Especifique.",
                    "Sofre(u) alguma afecção do aparelho respiratório (colapso de traqueia, bronquite, pneumonia, estenose de narinas, (palato alongado)? Especifique.",
                    "Sofre(u) de doenças do aparelho digestivo (gastrite, úlceras, diarreias, corpo estranho)? Especifique.",
                    "Sofre(u) de doença do aparelho genito-urinário (piometras, hiperplasia prostática, mastites, hematúria, obstruções, cistite, cálculo, fimose, insuficiência renal)? Especifique.",
                    "Sofre(u) algum tipo de fratura ou traumatismo?",
                    "Realizou algum procedimento cirúrgico para correção ortopédica (fratura ou traumatismo)?",
                    "Realizou algum tipo de procedimento cirúrgico? Especifique.",
                    "Sofre de alguma má formação congênita? Especifique.",
                    "Sofre(u) algum tipo de doença não relacionada acima? Especifique."
                ];
                $j = 0;
            @endphp
            <hr>
            @foreach($questoes as $questao)
                <p>{{ ++$j }}) {{ $questao }}</p>
                @foreach($pets as $pet)
                    <div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                id="questao-{{ $j }}-{{ $pet->id }}"
                                name="questao-{{ $j }}-{{ $pet->id }}"
                                onclick="$('input[name=especificacao-{{ $j }}-{{ $pet->id }}]').attr('readonly', $(this).val() == 0)"
                            >
                            <label class="form-check-label" for="questao-{{ $j }}-{{ $pet->id }}">
                                {{ $pet->nome_pet }}
                            </label>
                        </div>
                    </div>
                    <div>
                        <input type="text" class="form-control especificacao" name="especificacao-{{ $j }}-{{ $pet->id }}" placeholder="Especifique..." readonly>
                    </div>
                @endforeach
                {{--<textarea name="espeficicacao-{{ $j }}" rows="3" class="form-control" placeholder="Especifique..."></textarea>--}}
                <hr>
            @endforeach
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" name="declaracao-saude" id="declaracao-saude" data-description="Declaração de saúde " required>
                <label class="form-check-label" for="declaracao-saude" >
                    Declaro para os devidos fins que na hipotese de
                    doença preexistente, conhecida ou não, a cobertura do procedimento será após 12 meses de
                    permanencia initerrupta no plano. No ato da microchipagem, o(s) pet(s) passará(ão) por uma
                    avaliação de pré-existência.Considera-se doença preexistente a que o Pet tinha antes da
                    contratação do plano.
                </label>
            </div>

            <h2>
                <strong>5) Assinatura do cliente:</strong>
            </h2>
            {{--<img src="{{ url('/') . '/' .  $cliente->uploads()->where('description', 'Assinatura digital')->first()->path }}" alt="">--}}
            <div class="wrapper-signature">
                <canvas id="signature-pad-cliente" class="signature-pad" width=700 height=200></canvas>
                <div style="margin-top: 5px;">
                    <a class="btn btn-default signature-save" onclick="signatureSave(signaturePadCliente, $(this))"><i class="fa fa-check"></i> Confirmar</a>
                    <a class="btn btn-default signature-remake" onclick="signatureRemake(signaturePadCliente, $(this))" style="display: none;"><i class="fa fa-retweet"></i> Refazer</a>
                    <a class="btn btn-default signature-clear" onclick="signatureClear(signaturePadCliente)"><i class="fa fa-eraser"></i> Limpar Tudo</a>
                    <a class="btn btn-default signature-undo" onclick="signatureUndo(signaturePadCliente)"><i class="fa fa-undo"></i> Refazer</a>
                </div>
            </div>
        </form>


        <button class="btlaranja text-center" id="btnGerarProposta" onclick="confirmarVenda(signaturePadCliente, event)">GERAR PROPOSTA</button>

    </div>
    {{--<div class="col-md-12">--}}
    {{--</div>--}}
</div>
