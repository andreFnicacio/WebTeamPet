<div class="tela" id="bemvindo">
    <div class="dados-resumo text-white">

        <h1>
            Checklist
        </h1>
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
                "Fidelidade: o contrato possui fidelidade de 12 meses, podendo ser cancelado sem multa rescisória caso não tenha utilizado qualquer procedimento."
            ];
            $i = 0;
        @endphp
        <hr>
        @foreach($checklist as $check)
            <div class="form-check">
                <input autocomplete="off" class="form-check-input" type="checkbox" value="1" id="check-{{ ++$i }}">
                <label class="form-check-label" for="check-{{ $i }}">
                    {{ $check }}
                </label>
            </div>
            <hr>
        @endforeach

    </div>

    <div class="wrapper-signature">
        <canvas id="signature-pad-checklist" class="signature-pad" width=700 height=200></canvas>
        <div>
            <button class="btn btn-default signature-save" onclick="signatureSave(signaturePadChecklist, $(this));"><i class="fa fa-check"></i> Confirmar</button>
            <button class="btn btn-default signature-remake" onclick="signatureRemake(signaturePadCliente, $(this))" style="display: none;"><i class="fa fa-retweet"></i> Refazer</button>
            <button class="btn btn-default signature-clear" onclick="signatureClear(signaturePadChecklist);"><i class="fa fa-eraser"></i> Limpar Tudo</button>
            <button class="btn btn-default signature-undo" onclick="signatureUndo(signaturePadChecklist);"><i class="fa fa-undo"></i> Refazer</button>
        </div>
    </div>

    <div class="col-md-12">
        <a class="btlaranja" gerar_proposta(signaturePadChecklist)>Gerar Proposta de Adesão</a>
        <a class="btbranco">Voltar</a>
    </div>
</div>