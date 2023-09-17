@extends('layouts.app')

@section('title')
    @parent
    Clínicas - Nova clínica
@endsection
@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form ">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Clínicas
                </span>
            </div>
            @permission('create_clinicas')
            <div class="actions" data-target="#clinicas">
                <div class="btn-group btn-group-devided" data-toggle="buttons">
                    <button type="submit" id="save" class="btn green-jungle">Salvar</button>
                    <button type="submit" id="cancel" class="btn red-sunglo">Cancelar</button>
                </div>
            </div>
            @endpermission
        </div>
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::open(['route' => ['clinicas.store'], 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'clinicas']) !!}
                <div class="form-body">

                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button> Verifique se você preencheu todos os campos. 
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button> Validado com sucesso. 
                    </div>
                    <div class="col-md-12" style="margin-bottom: 20px;"> 
                        <h3 class="block" style="margin-top: 0px;">Dados Gerais</h3>
                    </div>
                    @include('clinics::fields')

                </div>
            {!! Form::close() !!}
        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            var $actions = $('.actions[data-target="#clinicas"]');

            $actions.find('#save').click(function() {
                var target = $actions.attr('data-target');
                if(target != '') {
                    $(target).submit();
                }
            });
            $actions.find('#cancel').click(function() {
                var target = $actions.attr('data-target');
                location.href = "{!! route('clinicas.index') !!}";
                return;
            });


            //Checar CPF
            $("#cpf_cnpj").blur(function(e) {
                var cpf = $(this).val();
                $.ajax({
                    url: "{{ Route('clinicas.consultaCNPJ') }}",
                    method: "POST",
                    data:
                        {
                            cpf_cnpj: cpf,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                    datatype: "json",
                    success: function(data)
                    {
                        swal(
                            'Erro',
                            'Já existe um credenciado com esse CPF ou CNPJ cadastrado.',
                            'error'
                        );
                        $("#cpf_cnpj").val("");

                    }
                });
            });



            $("#email_contato").blur(function(e) {
                var email = $(this).val();

                $.ajax({
                    url: "{{ Route('clinicas.consultaEmail') }}",
                    method: "POST",
                    data:
                        {
                            email: email,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                    datatype: "json",
                    success: function(data)
                    {
                        swal(
                            'Erro',
                            'Já existe um credenciado com esse e-mail cadastrado.',
                            'error'
                        );
                        $("#email_contato").val("");

                    }
                });

            });
        });
    </script>
@endsection