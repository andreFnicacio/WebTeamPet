@php
    $edit = isset($edit) ? $edit : false;
@endphp
<div class="row">
    <div class="col-md-3">

    </div>
    <div class="col-sm-3">
        @foreach($errors->all() as $key => $error)
            <div class="alert alert-dismissable alert-warning">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                {{ $error }}
            </div>
        @endforeach
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-3" for="name">
        Nome
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input value="{{ $user->name }}" type="text" name="name" data-required="1" class="form-control" required/>
    </div>
</div>

<!-- Preco Plano Familiar Field -->
<div class="form-group">
    <label class="control-label col-md-3" for="email">
        E-mail
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input value="{{ $user->email }}" type="email" name="email" data-required="1" class="form-control" required/><br>
        <small>Utilizado no login</small>
    </div>
</div>


@unless($edit)
<div class="form-group">
    <label class="control-label col-md-3" for="password">
        Senha
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="password" name="password" data-required="1" class="form-control" required/>
    </div>
</div>
@endunless

@unless($edit)
<div class="form-group">
    <label class="control-label col-md-3" for="password">
        Confirmação de senha
        <span class="required"> * </span>
    </label>
    <div class="col-md-4">
        <input type="password" name="password_confirmation" data-required="1" class="form-control" required/>
    </div>
</div>
@endunless

