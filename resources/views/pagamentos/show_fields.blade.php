<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', 'Id:') !!}
    <p>{!! $pagamentos->id !!}</p>
</div>

<!-- Id Cobranca Field -->
<div class="form-group">
    {!! Form::label('id_cobranca', 'Id Cobranca:') !!}
    <p>{!! $pagamentos->id_cobranca !!}</p>
</div>

<!-- Data Pagamento Field -->
<div class="form-group">
    {!! Form::label('data_pagamento', 'Data Pagamento:') !!}
    <p>{!! $pagamentos->data_pagamento !!}</p>
</div>

<!-- Complemento Field -->
<div class="form-group">
    {!! Form::label('complemento', 'Complemento:') !!}
    <p>{!! $pagamentos->complemento !!}</p>
</div>

<!-- Forma Pagamento Field -->
<div class="form-group">
    {!! Form::label('forma_pagamento', 'Forma Pagamento:') !!}
    <p>{!! $pagamentos->forma_pagamento !!}</p>
</div>

<!-- Valor Pago Field -->
<div class="form-group">
    {!! Form::label('valor_pago', 'Valor Pago:') !!}
    <p>{!! $pagamentos->valor_pago !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', 'Created At:') !!}
    <p>{!! $pagamentos->created_at !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', 'Updated At:') !!}
    <p>{!! $pagamentos->updated_at !!}</p>
</div>

