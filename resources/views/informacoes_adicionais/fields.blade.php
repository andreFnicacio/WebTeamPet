<!-- Cor Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="cor">
        Cor
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="cor" id="cor" class="form-control">
            @foreach(\App\Models\InformacoesAdicionais::$cores as $cor)
                <option value="{{ $cor }}" data-html="<span class='lifepet_select_color_badge badge bg-{{ $cor }} bg-font-{{ $cor }}'>{{ $cor }}</span>">

                </option>
            @endforeach
        </select>
    </div>
</div>

<!-- Descricao Resumida Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="descricao_resumida">
        Resumo
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="descricao_resumida" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Descricao Completa Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="descricao_completa">
        Descrição
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        {!! Form::textarea('descricao_completa', null, ['class' => 'form-control', 'required' => 'required']) !!}
    </div>
</div>


<!-- Icone Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="icone">
        Icone
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        @include('informacoes_adicionais.parts.icons')
    </div>
</div>

<!-- Prioridade Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="prioridade">
        Prioridade
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="range" min="1" max="100" value="10" name="prioridade" id="prioridade" data-required="1" class="form-control" />
    </div>
</div>

@section('scripts')
    @parent
    <style>
        .select2-container--bootstrap .select2-results__option--highlighted[aria-selected] {
            background-color: rgba(208, 215, 222, 0.16);
            color: #fff;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#prioridade').ionRangeSlider();
        });
    </script>
@endsection
<!-- Submit Field -->
<!--
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('informacoesAdicionais.index') !!}" class="btn btn-default">Cancel</a>
</div>
-->
