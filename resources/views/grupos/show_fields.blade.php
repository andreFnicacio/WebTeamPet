<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $grupos->id !!}</p>
</div>

<!-- Nome Grupo Field -->
<div class="form-group">
    {!! Form::label('nome_grupo', 'Nome Grupo:') !!}
    <p>{!! $grupos->nome_grupo !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $grupos->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $grupos->updated_at !!}</p>
</div>

<!-- Deleted At Field -->
<div class="form-group">
    {!! Form::label('deleted_at', 'Deleted At:') !!}
    <p>{!! $grupos->deleted_at !!}</p>
</div>

