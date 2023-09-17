<input type="hidden" name="id" value="{{ $papeis->id }}">

<!-- Cod Procedimento Field -->

<div class="form-group">
    <label class="control-label col-md-3" for="name">
        Nome
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $papeis->name }}" name="name" data-required="1" class="form-control" required/>
    </div>
</div>


<div class="form-group">
    <label class="control-label col-md-3" for="display_name">
        Nome de Apresentação
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="text" value="{{ $papeis->display_name }}" name="display_name" data-required="1" class="form-control" required=""/>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="description">
        Descrição
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <textarea class="form-control" name="description"></textarea>
    </div>
</div>
