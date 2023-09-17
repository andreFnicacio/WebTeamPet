<input type="hidden" name="id" value="{{ $procedimentos->id }}">

<!-- Cod Procedimento Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="cod_procedimento">
        Código do Procedimento
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $procedimentos->cod_procedimento }}" name="cod_procedimento" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Nome Procedimento Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="nome_procedimento">
        Nome do Procedimento
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $procedimentos->nome_procedimento }}" name="nome_procedimento" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Especialista Field -->
<div class="form-group">
    <label class="control-label col-md-3">Especialidade?
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        {{ Form::hidden('especialista',0) }}
        {{
           Form::checkbox('especialista', 1, $procedimentos->especialista, array(
             'id'=>'especialista',
             'class' => "make-switch",
             'data-on-color' => "success",
             'data-off-color' => "danger",
             'data-on-text'=>"Sim",
             'data-off-text' => "Não"
            )
           )
        }}
        {{--<input type="checkbox" name="especialista" {{ $procedimentos->especialista ? "checked" : "" }} class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não">--}}
    </div>
</div>

<!-- Intervalo Usos Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="intervalo_usos">
        Intervalo de usos
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="number" required name="intervalo_usos" value="{{ $procedimentos->intervalo_usos }}" data-required="1" class="form-control" />
    </div>
</div>
<!-- Valor Base Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="valor_base">
        Valor Base
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="number" required name="valor_base" value="{{ $procedimentos->valor_base }}" data-required="1" class="form-control" />
    </div>
</div>
<!-- Id Grupo Field -->
<div class="form-group">
    <label class="control-label col-md-3">Grupo
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select id="id_grupo" name="id_grupo" required placeholder="Selecione um cadastro" class="form-control select2">
            <option></option>
            @foreach(\App\Models\Grupos::orderBy('nome_grupo', 'asc')->get() as $g)
                <option value="{{ $g->id }}"
                        {{ ($g->id == $procedimentos->id_grupo || (isset($id_grupo) ? $id_grupo == $g->id : 0)) ? "selected" : "" }}>
                    {{ $g->id . " - " . $g->nome_grupo }}
                </option>
            @endforeach
        </select>
    </div>
</div>


<!-- Liberacao Automatica Field -->
<div class="form-group">
    <label class="control-label col-md-3">Liberado automaticamente?
        
    </label>
    <div class="col-md-4">
        {{ Form::hidden('liberacao_automatica',0) }}
        {{
           Form::checkbox('liberacao_automatica', 1, $procedimentos->liberacao_automatica, array(
             'id'=>'liberacao_automatica',
             'class' => "make-switch",
             'data-on-color' => "success",
             'data-off-color' => "danger",
             'data-on-text'=>"Sim",
             'data-off-text' => "Não"
            )
           )
        }}
        {{--<input type="checkbox" name="liberacao_automatica" {{ $procedimentos->liberacao_automatica ? "checked" : "" }} class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não">--}}
    </div>
</div>

<!-- Ativo Field -->
<div class="form-group">
    <label class="control-label col-md-3">Ativo?

    </label>
    <div class="col-md-4">
        {{ Form::hidden('ativo',0) }}
        {{
           Form::checkbox('ativo', 1, $procedimentos->ativo, array(
             'id'=>'ativo',
             'class' => "make-switch",
             'data-on-color' => "success",
             'data-off-color' => "danger",
             'data-on-text'=>"Sim",
             'data-off-text' => "Não"
            )
           )
        }}
        {{--<input type="checkbox" name="liberacao_automatica" {{ $procedimentos->liberacao_automatica ? "checked" : "" }} class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não">--}}
    </div>
</div>

<!-- Pré-Cirúrgico Field -->
<div class="form-group">
    <label class="control-label col-md-3">Pré-Cirúrgico?

    </label>
    <div class="col-md-4">
        {{ Form::hidden('pre_cirurgico',0) }}
        {{
           Form::checkbox('pre_cirurgico', 1, $procedimentos->pre_cirurgico, array(
             'id'=>'pre_cirurgico',
             'class' => "make-switch",
             'data-on-color' => "success",
             'data-off-color' => "danger",
             'data-on-text'=>"Sim",
             'data-off-text' => "Não"
            )
           )
        }}
        {{--<input type="checkbox" name="liberacao_automatica" {{ $procedimentos->liberacao_automatica ? "checked" : "" }} class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não">--}}
    </div>
</div>

<!-- Emergencial Field -->
<div class="form-group">
    <label class="control-label col-md-3">Emergencial?

    </label>
    <div class="col-md-4">
        {{ Form::hidden('emergencial',0) }}
        {{
           Form::checkbox('emergencial', 1, $procedimentos->emergencial, array(
             'id'=>'emergencial',
             'class' => "make-switch",
             'data-on-color' => "success",
             'data-off-color' => "danger",
             'data-on-text'=>"Sim",
             'data-off-text' => "Não"
            )
           )
        }}
        {{--<input type="checkbox" name="liberacao_automatica" {{ $procedimentos->liberacao_automatica ? "checked" : "" }} class="make-switch" data-on-color="success" data-off-color="danger" data-on-text="Sim" data-off-text="Não">--}}
    </div>
</div>

