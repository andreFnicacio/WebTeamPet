<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-ban"></i>Restrições de grupos
        </div>
    </div>
    <div class="portlet-body">        
        <form action="#" onsubmit="preventDefault();">
            {{ csrf_field() }}
            <input type="hidden" name="id_clinica" value="{{ $clinica->id }}">
            <table class="table table-light table-hover">
                <tr>
                    <th>Plano</th>
                    <th>Limite mensal <span data-toggle="tooltip"
                                            data-original-title="Clique duas vezes sobre o campo para habilitar a edição." class="fa fa-question-circle"></span></th>
                </tr>
                @foreach((new \App\Models\Grupos())->orderBy('nome_grupo', 'ASC')->get() as $grupo)
                <tr>
                    <td> <strong>#{{ $grupo->id }} - {{ $grupo->nome_grupo }}</strong> </td>
                    <td>
                        <div class="form-group">
                                <input type="number" readonly="readonly" name="contato_principal" data-required="1" data-grupo="{{ $grupo->id }}" data-clinica="{{ $clinica->id }}" data-before="{{ $clinica->limitePorGrupo($grupo) }}" class="form-control clinica-limite-mensal" value="{{ $clinica->limitePorGrupo($grupo) }}">
                        </div>
                    </td>
                </tr>
                @endforeach
            </table>
        </form>
    </div>
</div>