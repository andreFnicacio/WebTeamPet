<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-tags"></i>Categorias
        </div>
    </div>
    <div class="portlet-body">        
        <form action="{{ route('clinicas.atualizaCategorias') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="id_clinica" value="{{ $clinica->id }}">
            <table class="table table-light table-hover">
                <tr>
                    <th>Categoria</th>
                    <th>Grupos</th>
                    <th>É dessa categoria?</th>
                </tr>
                @foreach((new \App\Models\Categorias())->all() as $categoria)
                <tr>
                    <td> 
                        <strong>{{ $categoria->nome }}</strong> 
                    </td>
                    <td> 
                        <a class="label label-success" data-toggle="modal" data-target="#cat{{ $categoria->id }}-modal">Ver Grupos</a>
                        <div class="modal" id="cat{{ $categoria->id }}-modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">{{ $categoria->nome }} - Grupos</div>
                                    <div class="modal-body">
                                        <h5>Credenciados que são desta categoria realizam procedimentos dos seguintes grupos:</h5>
                                        <ul class="list-unstyled">
                                            @foreach($categoria->grupos as $grupo)
                                            <li>
                                                <span>> {{ $grupo->nome_grupo }}</span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="mt-radio-inline">
                            <label class="mt-radio">
                                <input type="radio" name="categoriaCredenciado[{{ $categoria->id }}]" value="1" {{ ($clinica->categorias->contains($categoria->id) ? 'checked' : '') }} /> Sim
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" name="categoriaCredenciado[{{ $categoria->id }}]" value="0" {{ ($clinica->categorias->contains($categoria->id) ? '' : 'checked') }}/> Nâo
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