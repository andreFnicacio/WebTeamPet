<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $planos->id !!}</p>
</div>

<!-- Nome Plano Field -->
<div class="form-group">
    {!! Form::label('nome_plano', 'Nome Plano:') !!}
    <p>{!! $planos->nome_plano !!}</p>
</div>

<!-- Preco Plano Familiar Field -->
<div class="form-group">
    {!! Form::label('preco_plano_familiar', 'Preco Plano Familiar:') !!}
    <p>{!! $planos->preco_plano_familiar !!}</p>
</div>

<!-- Preco Plano Individual Field -->
<div class="form-group">
    {!! Form::label('preco_plano_individual', 'Preco Plano Individual:') !!}
    <p>{!! $planos->preco_plano_individual !!}</p>
</div>

<!-- Data Vigencia Field -->
<div class="form-group">
    {!! Form::label('data_vigencia', 'Data Vigencia:') !!}
    <p>{!! $planos->data_vigencia !!}</p>
</div>

<!-- Data Inatividade Field -->
<div class="form-group">
    {!! Form::label('data_inatividade', 'Data Inatividade:') !!}
    <p>{!! $planos->data_inatividade !!}</p>
</div>

<!-- Ativo Field -->
<div class="form-group">
    {!! Form::label('ativo', 'Ativo:') !!}
    <p>{!! $planos->ativo !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $planos->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $planos->updated_at !!}</p>
</div>

<!-- Deleted At Field -->
<div class="form-group">
    {!! Form::label('deleted_at', 'Deleted At:') !!}
    <p>{!! $planos->deleted_at !!}</p>
</div>

