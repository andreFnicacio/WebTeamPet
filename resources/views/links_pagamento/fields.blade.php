<!-- Id Cliente Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="id_cliente">
        Cliente
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select name="id_cliente" data-required="1" class="form-control select2" id="id_cliente"
                @if(isset($linkPagamento)) disabled  @endif
            >
            @foreach($clientes as $c)
                <option
                        @if(isset($linkPagamento)) seleted="selected" @endif
                        value="{{ $c->id }}">{{ $c->id }} - {{ $c->nome_cliente }} - {{ $c->cpf }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- Valor Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="valor">
        Valor
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">R$</span>
            <input type="number" name="valor" data-required="1" class="form-control"
                @if(isset($linkPagamento) && isset($linkPagamento->valor)) value="{{ $linkPagamento->valor }}" @endif
            />
        </div>
    </div>
</div>

<!-- Parcelas Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="parcelas">
        Parcelas
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="number" step="1" min=1 max=12 name="parcelas" data-required="1" class="form-control"
               @if(isset($linkPagamento) && isset($linkPagamento->parcelas)) value="{{ $linkPagamento->parcelas }}" @else value=1 @endif
        />
    </div>
</div>

<!-- Expires At Field -->
<div class="form-group">
    <label class="control-label col-md-3">Expira em
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
       <div class="input-group date date-picker"  data-date-format="dd/mm/yyyy">
        <input type="text" name='expires_at' class="form-control" readonly
               @if(isset($linkPagamento) && isset($linkPagamento->expires_at))
               value="{{ \Carbon\Carbon::createFromTimeString($linkPagamento->expires_at)->format('d/m/Y') }}"
               @else
               value="{{ \Carbon\Carbon::now()->addMonth(1)->format('d/m/Y') }}"
               @endif
        >
        <span class="input-group-btn">
            <button class="btn default" type="button">
                <i class="fa fa-calendar"></i>
            </button>
        </span>
       </div>
        @if(!isset($linkPagamento)) <small>Por padrão, 1 mês de validade.</small> @endif
    </div>

</div>

<!-- Tag Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="tag">
        Tags
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <select class="form-control select2-tags" name="tags[]" required multiple="multiple"
        @if(isset($linkPagamento)) disabled  @endif>
            <option selected="selected" value="venda-avulsa">Venda avulsa</option>
            <option value="renovacao">Renovação</option>
            <option value="upgrade">Upgrade</option>
            <option value="downgrade">Downgrade</option>
            @if(isset($linkPagamento) && isset($linkPagamento->tags))
            @foreach(explode(';', $linkPagamento->tags) as $tag)
            <option selected="selected" value="{{ $tag }}">{{ $tag }}</option>
            @endforeach
            @endif
        </select>
    </div>
</div>

<!-- Descricao Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="descricao">
        Descricao
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <textarea name="descricao" data-required="1" class="form-control">
            @if(isset($linkPagamento) && isset($linkPagamento->descricao)) {{ $linkPagamento->descricao }} @endif
        </textarea>
    </div>
</div>

<!-- Status Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="status">
        Status
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" name="status" data-required="1" class="form-control" readonly
               @if(isset($linkPagamento) && isset($linkPagamento->status)) value="{{ $linkPagamento->status }}" @else value="ABERTO" @endif/>
    </div>
</div>
<!-- Submit Field -->
<!--
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('links-pagamento.index') !!}" class="btn btn-default">Cancel</a>
</div>
-->
