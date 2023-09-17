<div class="form-group">
    <label class="control-label col-md-3" for="id_cliente">
        Cliente
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        @php
         $idCliente = -1;
         if($pagamentos->id) {
            $idCliente = $pagamentos->cobranca()->first()->id_cliente;
         }
        @endphp
        <select name="id_cliente" id="id_cliente" class="form-control select2">
            <option value=""></option>
            @foreach(\App\Models\Clientes::orderBy('nome_cliente')->get() as $cliente)
                <option value="{{ $cliente->id }}" {{ $idCliente == $cliente->id ? "selected" : "" }}>{{ $cliente->id }} - {{ $cliente->nome_cliente }}</option>
            @endforeach
        </select>
    </div>
</div>
<!-- Id Cobranca Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_cobranca">
        Cobranca
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_cobranca" id="id_cobranca" class="select2 form-control">
            @if($pagamentos->id)
                <option value="{{ $pagamentos->cobranca()->first()->id }}">{{ $pagamentos->cobranca()->first()->competencia }}</option>
            @endif
        </select>
    </div>
</div>

<!-- Data Pagamento Field -->
<div class="form-group">
    <label class="control-label col-md-3">Data de Pagamento
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
       <div class="input-group input-medium date date-picker"  data-date-format="dd/mm/yyyy">
        <input type="text" name='data_pagamento' value="{{ $pagamentos->data_pagamento ? $pagamentos->data_pagamento->format('d/m/Y') : "" }}" class="form-control" readonly>
        <span class="input-group-btn">
            <button class="btn default" type="button">
                <i class="fa fa-calendar"></i>
            </button>
        </span>
        </div>
    </div>
</div>

<!-- Complemento Field -->
<div class="form-group col-sm-12 col-lg-12">
    <span class="control-label col-md-3">Complemento
        <span class="required">*</span>
    </span>
    <div class="col-md-4">
        {!! Form::textarea('complemento', null, ['class' => 'form-control', 'rows' => 3]) !!}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3">Forma Pagamento?
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="forma_pagamento" id="forma_pagamento" class="form-control select2">
            <option value="0" {{ $pagamentos->forma_pagamento == 0  ? "selected" : "" }}>Boleto</option>
            <option value="1" {{ $pagamentos->forma_pagamento == 1  ? "selected" : "" }}>Crédito</option>
            <option value="2" {{ $pagamentos->forma_pagamento == 2  ? "selected" : "" }}>Débito</option>
        </select>
    </div>
</div>

<!-- Valor Pago Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="valor_pago">
        Valor Pago
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="number" name="valor_pago" {{ \App\Helpers\Utils::value($pagamentos, 'valor_pago') }} data-required="1" class="form-control" />
    </div>
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#id_cliente').change(function(e) {
               var $id = $(this).val();
               var cobrancas = [];
               $('#id_cobranca').html('');
               $.getJSON('{{ url('/api/v1/cobrancas') }}/' + $id, {}, function(data) {
                    cobrancas = data;
                    var htmlCobrancas = "<option></option>";
                    for(i = 0; i < cobrancas.length; i++) {
                        htmlCobrancas += "<option value='" + cobrancas[i].id + "'>" + cobrancas[i].competencia + "</option>";
                    }
                   $('#id_cobranca').html(htmlCobrancas);
               });
            });
        })
    </script>
@endsection

