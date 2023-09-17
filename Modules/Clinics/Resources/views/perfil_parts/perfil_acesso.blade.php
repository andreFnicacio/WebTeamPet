<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-desktop"></i>Acesso ao Sistema
        </div>
    </div>
    <div class="portlet-body">        
        @if(!$clinica->user)
            <h5> <i class="fa fa-warning font-red"></i> Este credenciado não possui acesso ao sistema. Para criá-lo, preencha o formulário abaixo.</h5>
        @endif
        <form action="{{ route('clinicas.atualizaAcessoUser') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="id_clinica" value="{{ $clinica->id }}">
            <div class="form-group">
                <label class="control-label">Email de Login</label>
                <input type="text" name="email" class="form-control" value="{{ $clinica->user ? $clinica->user->email : '' }}" required /> 
            </div>
            <div class="form-group">
                <label class="control-label">Nova senha</label>
                <input type="text" name="password" class="form-control" required value="" onfocus="$(this).attr('type', 'password')" autocomplete="clinica_user" /> 
            </div>
            <div class="margin-top-10">
                <button class="btn green"> Salvar </button>
            </div>
        </form>
    </div>
</div>