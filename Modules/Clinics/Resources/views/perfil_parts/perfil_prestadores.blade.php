<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-user-md"></i>Veterinários
        </div>
    </div>
    <div class="portlet-body">
        <form action="{{ route('clinicas.atualizaPrestadores') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="id_clinica" value="{{ $clinica->id }}">
            <table class="table table-light table-hover">
                <tr>
                    <th>Veterinário</th>
                    <th>CRMV</th>
                    <th>Atende?</th>
                </tr>
                @foreach((new \Modules\Veterinaries\Entities\Prestadores())->orderBy('id', 'DESC')->get() as $prestador)
                    <tr>
                        <td><strong>{{ $prestador->nome }}</strong></td>
                        <td> {{ $prestador->getCRMV() }} </td>
                        <td>
                            <div class="mt-radio-inline">
                                <label class="mt-radio">
                                    <input type="radio" name="prestadorCredenciado[{{ $prestador->id }}]"
                                           value="1" {{ ($clinica->prestadores->contains($prestador->id) ? 'checked' : '') }} />
                                    Sim
                                    <span></span>
                                </label>
                                <label class="mt-radio">
                                    <input type="radio" name="prestadorCredenciado[{{ $prestador->id }}]"
                                           value="0" {{ ($clinica->prestadores->contains($prestador->id) ? '' : 'checked') }}/>
                                    Nâo
                                    <span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
            <!--end profile-settings-->
            <div class="margin-top-10">
                <button class="btn green"> Salvar</button>
            </div>
        </form>
    </div>
</div>