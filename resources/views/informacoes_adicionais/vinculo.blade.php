@php
    $i = 1;
    $vinculadas = \App\Models\InformacoesAdicionaisVinculos::where('id_vinculado', $id)
                                             ->where('tabela_vinculada', $tabelaVinculada)
                                             ->get();
@endphp

<div class="badges">
    @foreach($vinculadas as $v)
        @php
            $info = $v->informacoesAdicionais()->first();
        @endphp
        <div class="lifepet-badge bg-{{ $info->cor }} bg-font-{{ $info->cor }}" data-modal="#modalInformacao{{ $i }}">
            <span class="fa {{ $info->icone }}"></span>
        </div>
        @php
            $i++;
        @endphp
    @endforeach
    <div class="lifepet-badge bg-green-meadow bg-font-green-meadow" data-modal="#adicionarInformacao">
        <span class="fa fa-plus"></span>
    </div>
</div>

@php
    $i = 1;
@endphp

@foreach($vinculadas as $v)
    @php
        $info = $v->informacoesAdicionais()->first();
    @endphp
    <div id="modalInformacao{{$i}}" class="modal fade" tabindex="-1" data-replace="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-{{ $info->cor }} bg-font-{{ $info->cor }}">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">{{ $info->descricao_resumida }}</h4>
                </div>
                    <div class="modal-body">
                        {{ $info->descricao_completa }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    </div>
            </div>
        </div>
    </div>
    @php
        $i++;
    @endphp
@endforeach

<div id="adicionarInformacao" class="modal fade" tabindex="-1" data-replace="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-green-meadow bg-font-green-meadow">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Vincular informação: </h4>
            </div>
            <form action="{{ route('informacoesAdicionais.vincular') }}" method="POST">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_vinculado" value="{{ $id }}">
                    <input type="hidden" name="tabela_vinculada" value="{{ $tabelaVinculada }}">
                    <select name="id_informacoes_adicionais" id="" class="select2">
                        <option value="">Selecione uma opção</option>
                        @foreach(\App\Models\InformacoesAdicionais::all() as $ia)
                            <option value="{{ $ia->id }}">{{ $ia->descricao_resumida }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn dark btn-outline">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.lifepet-badge').click(function() {
                 var $modal = $($(this).data('modal'));
                 $modal.modal('show');
            });
        });

    </script>
@endsection