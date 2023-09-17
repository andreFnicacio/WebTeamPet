<div class="tela" id="bemvindo">
    <div class="dados-resumo text-white">

        <h1>
            @if($pets->count() > 1)
                <strong>Resumo dos Pets</strong>
            @else
                <strong>Resumo do Pet</strong>
            @endif
        </h1>

        @foreach($pets as $pet)
            <div class="card text-dark">
                {{ $pet->nome_pet }} - {{ $pet->plano()->nome_plano }}
            </div>
        @endforeach
        {{--nome_pet--}}
        {{--tipo--}}
        {{--sexo--}}
        {{--id_raca--}}
        {{--data_nascimento--}}
        {{--contem_doenca_pre_existente--}}
        {{--doencas_pre_existentes--}}
        {{--observacoes--}}
        {{--carteira_vacinacao--}}
        {{--foto_pet--}}
        {{--id_plano--}}
        {{--participativo--}}
        {{--familiar--}}
        {{--data_inicio_contrato--}}
        {{--id_vendedor--}}
        {{--valor_adesao--}}
        {{--valor_plano--}}
        {{--regime--}}

    </div>

    <div class="wrapper-signature">
        <canvas id="signature-pad-pets" class="signature-pad" width=700 height=200></canvas>
        <div>
            <button class="btn btn-default signature-save" onclick="signatureSave(signaturePadPets, $(this));"><i class="fa fa-check"></i> Confirmar</button>
            <button class="btn btn-default signature-remake" onclick="signatureRemake(signaturePadCliente, $(this))" style="display: none;"><i class="fa fa-retweet"></i> Refazer</button>
            <button class="btn btn-default signature-clear" onclick="signatureClear(signaturePadPets);"><i class="fa fa-eraser"></i> Limpar Tudo</button>
            <button class="btn btn-default signature-undo" onclick="signatureUndo(signaturePadPets);"><i class="fa fa-undo"></i> Refazer</button>
        </div>
    </div>

    <div class="col-md-12">
        <a class="btlaranja" onclick="avanca_tela(signaturePadPets)">Avançar</a>
        <a class="btbranco voltar">Voltar</a>
    </div>
</div>