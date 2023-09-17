<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-book"></i>Planos
        </div>
    </div>
    <div class="portlet-body">        
        <form action="{{ route('clinicas.atualizaPlanos') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="id_clinica" value="{{ $clinica->id }}">
            <table class="table table-light table-hover">
                <tr>
                    <th>Plano</th>
                    <th>Atende?</th>
                </tr>
                @foreach((new \App\Models\Planos())->orderBy('id', 'DESC')->get() as $plano)
                <tr>
                    <td> <strong>#{{ $plano->id }} - {{ $plano->nome_plano }}</strong> </td>
                    <td>
                        <div class="mt-radio-inline">
                            <label class="mt-radio">
                                <input type="radio" name="planoCredenciado[{{ $plano->id }}]" value="1" {{ $clinica->checkPlanoCredenciado($plano->id) ? 'checked' : '' }} /> Sim
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" name="planoCredenciado[{{ $plano->id }}]" value="0" {{ $clinica->checkPlanoCredenciado($plano->id) ? '' : 'checked' }}/> Nâo
                                <span></span>
                            </label>
                        </div>
                    </td>
                </tr>
                @endforeach
            </table>
            <!--end profile-settings-->
            <div class="margin-top-10">
                <button class="btn green"> Salvar </button>
            </div>
        </form>
    </div>
</div>