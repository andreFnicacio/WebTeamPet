<form action="#" method="post" id="dadospet" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input autocomplete="off" type="hidden" name="idCliente" class="idCliente" value="{{ $idCliente }}">

    <div class="tela">
        <div class="conheca col-md-12">
            <ul id="lista_pets"></ul>
            <div class="pets-container" id="pets_container">

            </div>

            <div id="dados_pet">

                @for($i = 0; $i < $qtdPets; $i++)
                    <div class="formgroup-pet">

                        <h2>Dados do pet ({{ $i+1 }}):</h2>

                        <div class="form-group">
                            <label class="form-label">Nome do Pet*</label>
                            <input autocomplete="off" class="  " name="pet[{{ $i }}][nome_pet]" type="text" data-required="true" data-description="Pet {{ $i+1 }} - Nome do pet">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipo*</label>
                            <select autocomplete="off" class=" " name="pet[{{ $i }}][tipo]" data-required="true" data-description="Pet {{ $i+1 }} - Tipo do pet">
                                <option selected="true" disabled="disabled" value=""></option>
                                <option value="CACHORRO">Cão</option>
                                <option value="GATO">Gato</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sexo*</label>
                            <select autocomplete="off" class=" " name="pet[{{ $i }}][sexo]" data-required="true" data-description="Pet {{ $i+1 }} - Sexo do pet">
                                <option selected="true" disabled="disabled" value=""></option>
                                @foreach([
                                        'M'     => 'Macho',
                                        'F'     => 'Fêmea'
                                    ] as $value => $option)
                                    <option value="{{ $value }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Raça do pet*</label>
                            <select autocomplete="off" class=" " name="pet[{{ $i }}][id_raca]" data-required="true" data-description="Pet {{ $i+1 }} - Raça do pet">
                                <option selected="true" disabled="disabled" value=""></option>
                                <option value="1">SRD - Sem Raça Definida</option>
                                @foreach(\App\Models\Raca::orderBy('tipo', 'asc')->orderBy('nome', 'asc')->get() as $r)
                                    <option value="{{ $r->id }}">{{ $r->nome . " - " . $r->tipo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Data de Nascimento*</label>
                            <input autocomplete="off" class="   date" name="pet[{{ $i }}][data_nascimento]" type="tel" data-description="Pet {{ $i+1 }} - Data de Nascimento" data-required="true">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Possui alguma doença?*</label>
                            <select autocomplete="off" class=" " name="pet[{{ $i }}][contem_doenca_pre_existente]" data-required="true" data-description="Pet {{ $i+1 }} - Possui alguma doença?">
                                <option selected="true" disabled="disabled" value=""></option>
                                <option value="0" >Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Cite as doenças (caso existam).</label>
                            <input autocomplete="off" class="  " name="pet[{{ $i }}][doencas_pre_existentes]" type="text" >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Observações</label>
                            <input autocomplete="off" class="  " name="pet[{{ $i }}][observacoes]" type="text" >
                        </div>

                        <h3 style="margin-top: 10px;">Foto da carteirinha de vacinação (caso tenha)</h3>
                        <input autocomplete="off" name="pet[{{ $i }}][carteira_vacinacao]" type="file" style="width: 80%;" accept="image/*"  placeholder="Carteira de Vacinação" data-description="Pet {{ $i+1 }} - Foto da carteirinha de vacinação do pet">

                        <h3 style="margin-top: 10px;">Foto do Pet para carteirinha (opcional)</h3>
                        <input autocomplete="off" name="pet[{{ $i }}][foto]" type="file" style="width: 80%;" accept="image/*"  placeholder="Foto do pet para carteirinha" data-description="Pet {{ $i+1 }} - Foto do pet para carteirinha">



                        <br>
                        <h2>Dados do plano:</h2>

                        <div class="form-group">
                            <label class="form-label">Plano*</label>
                            <select autocomplete="off" class=" " id="plano_pet" name="pet[{{ $i }}][id_plano]" data-required="true" data-description="Pet {{ $i+1 }} - Plano">
                                <option selected="true" disabled="disabled" value=""></option>
                                @foreach(\App\Models\Planos::where('ativo', 1)->orderBy('ativo', 'desc')->get() as $plano)
                                    <option value="{{ $plano->id }}"
                                            data-real-price="{{ \App\Helpers\Utils::money(\App\Helpers\Utils::calcRealPrice($plano->preco_plano_individual), false) }}">
                                        {{ $plano->nome_plano }} - Ind: R${{ number_format($plano->preco_plano_individual) }} / Fam: R${{ number_format($plano->preco_plano_familiar) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Participativo?*</label>
                            <select autocomplete="off" class=" " name="pet[{{ $i }}][participativo]" data-required="true" data-description="Pet {{ $i+1 }} - Participativo">
                                <option selected="true" disabled="disabled" value=""></option>
                                <option value="0" >Não</option>
                                <option value="1">Sim</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Familiar?*</label>
                            <select autocomplete="off" class=" " name="pet[{{ $i }}][familiar]" data-required="true" data-description="Pet {{ $i+1 }} - Familiar">
                                <option selected="true" disabled="disabled" value=""></option>
                                <option value="0" {{ $qtdPets == 1 ? 'selected' : '' }}>Não</option>
                                <option value="1" {{ $qtdPets > 1 ? 'selected' : '' }}>Sim</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Data de Início do Contrato*</label>
                            <input autocomplete="off" class="   date datetoday" name="pet[{{ $i }}][data_inicio_contrato]" type="tel" data-description="Pet {{ $i+1 }} - Data de Início do Contrato" data-required="true" data-today="{{ (new \Carbon\Carbon())->format('d/m/Y') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Vendedor*</label>
                            <input autocomplete="off" name="pet[{{ $i }}][id_vendedor]" type="hidden" value="{{ \App\Models\Vendedores::where('id_usuario', \Illuminate\Support\Facades\Auth::user()->id)->get()->first()->id }}">
                            <input autocomplete="off" class="  " type="text" value="{{ $user->name }}" readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Valor da Adesão (R$)*</label>
                            <input autocomplete="off" class="   money" name="pet[{{ $i }}][valor_adesao]" type="tel" data-required="true" data-description="Pet {{ $i+1 }} - Valor da Adesão">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Valor do Plano (R$)*</label>
                            <input autocomplete="off"  id="valor_plano_pet" class="   money" name="pet[{{ $i }}][valor_plano]" type="tel" data-required="true" data-description="Pet {{ $i+1 }} - Valor do Plano">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Regime*</label>
                            <select autocomplete="off" class=" " name="pet[{{ $i }}][regime]" data-required="true" data-description="Pet {{ $i+1 }} - Regime">
                                <option selected="true" disabled="disabled" value=""></option>
                                @foreach(\App\Models\Pets::$regimes as $regime)
                                    <option value="{{ $regime }}">{{ $regime }}</option>
                                @endforeach
                            </select>
                        </div>

                </div>
                @endfor

            </div>
            <a type="button" class="btlaranja" id="cadastrarPet" >Salvar Pet</a>

        </div>

    </div>
</form>