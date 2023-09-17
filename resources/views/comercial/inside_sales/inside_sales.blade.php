@extends('layouts.app')

@section('title')
    @parent
    Inside Sales - Novo cliente
@endsection

@section('css')
    <style>
        .form-wizard .steps>li>a.step>.number {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }
    </style>
@endsection

@section('content')
    <!-- BEGIN VALIDATION STATES-->
    <div class="portlet light portlet-fit portlet-form " id="form_wizard_1">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-settings font-green-jungle"></i>
                <span class="caption-subject font-green-jungle sbold uppercase">
                  Clientes
                </span>
            </div>
        </div>
        <div class="portlet-body form">
            <!-- BEGIN FORM-->
{{--            {!! Form::open(['route' => ['comercial.inside_sales_cadastro'], 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'inside_sales_cadastro']) !!}--}}
            <form action="{{ route('comercial.inside_sales_cadastro') }}" method="POST" class="form-horizontal" id="submit_form">
                {{ csrf_field() }}
                <div class="form-wizard">
                    @include('comercial.inside_sales.inside_sales_fields')
                </div>
            {{--{!! Form::close() !!}--}}
            </form>

            {{--<div class="modal" id="modal-d4sign" tabindex="-1" role="dialog">--}}
                {{--<div class="modal-dialog modal-lg" role="document">--}}
                    {{--<div class="modal-content">--}}
                        {{--<div class="modal-header">--}}
                            {{--<h5 class="modal-title">--}}
                                {{--<h2>Finalizar</h2>--}}
                            {{--</h5>--}}
                            {{--<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">--}}
                                {{--<span aria-hidden="true">&times;</span>--}}
                            {{--</button>--}}
                        {{--</div>--}}
                        {{--<div class="modal-body">--}}
                            {{--<form action="" method="POST" enctype="multipart/form-data">--}}
                                {{--{{ csrf_field() }}--}}
                                {{--<input type="hidden" name="idCliente" value="1020751126" required>--}}
                                {{--<input type="file" name="proposta" required>--}}
                                {{--<button>Salvar</button>--}}
                            {{--</form>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

        </div>
        <!-- END FORM-->
    </div>
    <!-- END VALIDATION STATES-->
@endsection

@section('scripts')
    @parent
    <script src="{{ url('/') }}/js/mask-money/jquery.maskMoney.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>

    <script type="text/javascript">

        var FormWizard = function () {

            return {
                //main function to initiate the module
                init: function () {

                    var form = $('#submit_form');
                    $.removeData(form.get(0),'validator');

                    if (!jQuery().bootstrapWizard) {
                        return;
                    }

                    // var form = $('#submit_form');
                    var error = $('.alert-danger', form);
                    var success = $('.alert-success', form);
                    var warningPet = $('.alert-warning.alert-pet', form);

                    form.validate({
                        doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                        errorElement: 'span', //default input error message container
                        errorClass: 'help-block help-block-error', // default input error message class
                        focusInvalid: false, // do not focus the last invalid input
                        rules: {
                            //account
                            nome_cliente: {
                                minlength: 5,
                                required: true
                            },
                            sexo: {
                                required: true
                            },
                            cpf: {
                                minlength: 14,
                                required: true
                            },
                            email: {
                                required: true,
                                email: true
                            },
                            celular: {
                                required: true
                            },
                            data_nascimento: {
                                required: true
                            },
                            cep: {
                                required: true
                            },
                            rua: {
                                required: true
                            },
                            numero_endereco: {
                                required: true
                            },
                            bairro: {
                                required: true
                            },
                            cidade: {
                                required: true
                            },
                            estado: {
                                required: true
                            },
                            'checklist[]': {
                                required: true,
                                minlength: 1
                            }
                        },

                        messages: { // custom messages for radio buttons and checkboxes
                            'checklist[]': {
                                required: "Please select at least one option",
                                minlength: jQuery.validator.format("Please select at least one option")
                            }
                        },

                        errorPlacement: function (error, element) { // render error placement for each input type
                            if (element.attr("name") == "data_nascimento") { // for uniform radio buttons, insert the after the given container
                                error.insertAfter(element.parent().parent());
                            } else if (element.attr("name") == "checklist[]") { // for uniform checkboxes, insert the after the given container
                                error.insertAfter("#form_checklist_error");
                            } else {
                                error.insertAfter(element.parent()); // for other inputs, just perform default behavior
                            }
                        },

                        invalidHandler: function (event, validator) { //display error alert on form submit
                            success.hide();
                            error.show();
                            App.scrollTo(error, -200);
                        },

                        highlight: function (element) { // hightlight error inputs
                            $(element)
                                .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                        },

                        unhighlight: function (element) { // revert the change done by hightlight
                            $(element)
                                .closest('.form-group').removeClass('has-error'); // set error class to the control group
                        },

                        success: function (label) {
                            if (label.attr("for") == "gender" || label.attr("for") == "checklist[]") { // for checkboxes and radio buttons, no need to show OK icon
                                label
                                    .closest('.form-group').removeClass('has-error').addClass('has-success');
                                label.remove(); // remove error label here
                            } else { // display success icon for other inputs
                                label
                                    .addClass('valid') // mark the current input as valid and display OK icon
                                    .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                            }
                        },

                        submitHandler: function (form) {
                            success.show();
                            error.hide();
                            form[0].submit();
                            //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                        }

                    });

                    var displayConfirm = function() {
                        $('#tab4 .form-control-static', form).each(function(){
                            var input = $('[name="'+$(this).attr("data-display")+'"]', form);
                            if (input.is(":radio")) {
                                input = $('[name="'+$(this).attr("data-display")+'"]:checked', form);
                            }
                            if (input.is(":text") || input.is("textarea")) {
                                $(this).html(input.val());
                            } else if (input.is("select")) {
                                $(this).html(input.find('option:selected').text());
                            } else if (input.is(":radio") && input.is(":checked")) {
                                $(this).html(input.attr("data-title"));
                            } else if ($(this).attr("data-display") == 'checklist[]') {
                                var checklist = [];
                                $('[name="checklist[]"]:checked', form).each(function(){
                                    checklist.push($(this).attr('data-title'));
                                });
                                $(this).html(checklist.join("<br>"));
                            }
                        });
                    }

                    var handleTitle = function(tab, navigation, index) {
                        var total = navigation.find('li').length;
                        var current = index + 1;
                        var form = $('#form_wizard_1');
                        // set wizard title
                        $('.step-title', form).text('Step ' + (index + 1) + ' of ' + total);
                        // set done steps
                        jQuery('li', form).removeClass("done");
                        var li_list = navigation.find('li');
                        for (var i = 0; i < index; i++) {
                            jQuery(li_list[i]).addClass("done");
                        }

                        form.find('.btn-group-actions .btn').hide();

                        if (current == 1) {
                            form.find('.button-next').show();
                        } else if (current == 2) {
                            form.find('.button-previous').show();
                            form.find('.button-next').show();
                        } else if (current == 3) {
                            form.find('.button-previous').show();
                            form.find('.button-next').show();
                        } else if (current == 4) {
                            form.find('.button-previous').show();
                            form.find('.button-submit').show();
                        } else {
                            form.find('.button-end').show();
                        }
                        App.scrollTo($('.page-title'));
                    }

                    // default form wizard
                    $('#form_wizard_1').bootstrapWizard({
                        'nextSelector': '.button-next',
                        'previousSelector': '.button-previous',
                        onTabClick: function (tab, navigation, index, clickedIndex) {
                            return false;

                            // success.hide();
                            // error.hide();
                            // warningPet.hide();
                            // if (form.valid() == false) {
                            //     return false;
                            // }

                            handleTitle(tab, navigation, clickedIndex);
                        },
                        onNext: function (tab, navigation, index) {
                            success.hide();
                            error.hide();
                            warningPet.hide();

                            if (index != 2 && form.valid() == false) {
                                // console.log('index != 2 && form.valid() == false');
                                return false;
                            }
                            else if (index == 2 && $('.Pets.List ul li').length === 0) {
                                warningPet.show();
                                return false;
                            }

                            handleTitle(tab, navigation, index);
                        },
                        onPrevious: function (tab, navigation, index) {
                            success.hide();
                            error.hide();

                            handleTitle(tab, navigation, index);
                        },
                        onTabShow: function (tab, navigation, index) {
                            var total = navigation.find('li').length;
                            var current = index + 1;
                            var $percent = (current / total) * 100;
                            $('#form_wizard_1').find('.progress-bar').css({
                                width: $percent + '%'
                            });
                        }
                    });

                    $('#form_wizard_1').find('.btn.button-previous').hide();
                    $('#form_wizard_1').find('.btn.button-submit').hide();
                    $('#form_wizard_1').find('.btn.button-end').hide();
                    $('#form_wizard_1').find('.button-next').show();
                    $('#form_wizard_1 .button-submit').click(function (e) {

                        var gerar_cobranca = $('#gerar_cobranca').val();
                        var texto_alerta = 'Conforme desmarcado, não será gerado uma cobrança para este cliente. O sistema apenas irá salvar suas informações.';
                        texto_alerta += ' Deseja realmente continuar?';
                        console.log(gerar_cobranca);
                        if(gerar_cobranca == 'sim') {
                            var forma_pagamento = $('.select-forma option:selected').val();
                        
                            texto_alerta = 'Verifique se os dados de pagamento estão corretos. ';
                            texto_alerta += 'O sistema irá gerar um boleto com o valor informado e irá enviar para o e-mail do cliente.';
                            if(forma_pagamento != 'Boleto') {
                                texto_alerta = 'O sistema irá gerar uma cobrança no cartão de crédito informado e irá enviar a confirmação para o e-mail do cliente.';
                            }
                            
                            texto_alerta += ' Deseja realmente continuar?';
                        }
                        

                        swal({
                            title: "Atenção!",
                            text: texto_alerta,
                            showCancelButton: true,
                            type: "warning"
                        }).then(function() {
                            submitFormCliente(e);
                        });
                        
                    }).hide();

                }

            };

        }();

        jQuery(document).ready(function() {
            FormWizard.init();

            $(".mask-money").maskMoney({allowNegative: true, thousands:'.', decimal:',', affixesStay: false, allowZero: true});

            // $('#valor_pagamento').mask('#.##0,00', {reverse: true});
            // var valor = 1050.53;
            // valor = valor.toFixed(2).toString().replace(/\./, ',');
            // console.log(valor);
            // $('#valor_pagamento').val(valor);
            // $('#valor_pagamento').mask('#.##0,00', {reverse: true});

        });

        function submitFormCliente(e) {
            e.preventDefault();
            
            $('.btn-delete-pet').remove();
            $('#btn-adicionar-pet').remove();

            $(
                'input[name*="pet-"], ' +
                'select[name*="pet-"], ' +
                'textarea[name*="pet-"], ' +
                'input[name*="plano-"], ' +
                'select[name*="plano-"], ' +
                'textarea[name*="plano-"]'
            ).removeAttr('name');
            swal({
                title: 'Por favor, aguarde...',
                text: 'O cadastro do cliente está sendo feito!',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                onOpen: () => {
                    swal.showLoading()
                }
            });
            $.ajax({
                method: "POST",
                url: "{{ route('comercial.inside_sales_cadastro') }}",
                data: $('#submit_form').serialize()
            })
            .done(function (res) {
                swal('Sucesso!', 'Cliente cadastrado com sucesso!', 'success');
                var urlDownloadProposta = '/clientes/' + res.id + '/downloadProposta/0';
                var urlProposta = '/clientes/' + res.id + '/proposta/0';
                var urlClienteEdit = '/clientes/' + res.id + '/edit';
                $('.btn-download-proposta').attr('href', urlDownloadProposta);
                $('.btn-ver-proposta').attr('href', urlProposta);
                $('.btn-cliente-edit').attr('href', urlClienteEdit);
                $('input[name="idCliente"]').val(res.id);
                $('#form_wizard_1').bootstrapWizard('next')
            })
            .fail(function (res) {
                swal('Erro!', res.responseText, 'error');
            });
            return false;
        }

        $('.select-forma').on('change',function(){
            let opt = $(this).val();
            if(opt == 'Boleto') {
                $('#gerar_cobranca').val('sim');
                $('.dados_boleto').show();
                $('.dados_cartao').hide();
            }

            if(opt != 'Boleto') {
                $('#gerar_cobranca').val('');
                $('.dados_boleto').hide();
                $('.dados_cartao').show();
            }

        });

        function addPet() {

            var formPet = $('#submit_form');
            var error = $('.alert-danger', formPet);
            var success = $('.alert-success', formPet);

            formPet.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    'pet-nome_pet': {
                        required: true
                    },
                    'pet-tipo': {
                        required: true
                    },
                    'pet-sexo': {
                        required: true
                    },
                    'pet-id_raca': {
                        required: true
                    },
                    'pet-data_nascimento': {
                        required: true
                    },
                    'pet-contem_doenca_pre_existente': {
                        required: true
                    },
                    'plano-id_plano': {
                        required: true
                    },
                    'plano-participativo': {
                        required: true
                    },
                    'plano-familiar': {
                        required: true
                    },
                    'plano-data_inicio_contrato': {
                        required: true
                    },
                    'plano-valor_adesao': {
                        required: true
                    },
                    'plano-valor_plano': {
                        required: true
                    },
                    'plano-regime': {
                        required: true
                    },
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") == "data_nascimento") { // for uniform radio buttons, insert the after the given container
                        error.insertAfter(element.parent().parent());
                    } else if (element.attr("name") == "checklist[]") { // for uniform checkboxes, insert the after the given container
                        error.insertAfter("#form_checklist_error");
                    } else {
                        error.insertAfter(element.parent()); // for other inputs, just perform default behavior
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    App.scrollTo(error, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    if (label.attr("for") == "gender" || label.attr("for") == "checklist[]") { // for checkboxes and radio buttons, no need to show OK icon
                        label
                            .closest('.form-group').removeClass('has-error').addClass('has-success');
                        label.remove(); // remove error label here
                    } else { // display success icon for other inputs
                        label
                            .addClass('valid') // mark the current input as valid and display OK icon
                            .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    }
                },

                submitHandler: function (form) {
                    success.show();
                    error.hide();
                    form[0].submit();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });

            if (!formPet.valid()) {

                success.hide();
                error.show();
                return false;

            } else {
                success.show();
                error.hide();

                var campos = [];
                var inputs = '';
                var nomePlano = $('#modal-addPet select[name="plano-id_plano"] option:selected').text();

                var petsLista = $('.Pets.List');
                petsLista.attr('data-numpets', (parseInt(petsLista.attr('data-numpets')) + 1));
                var numPets = parseInt(petsLista.attr('data-numpets'));

                $.each($('#modal-addPet input, #modal-addPet select, #modal-addPet textarea').not('input[name*="checklistDoencas"]'), function (i, el) {
                    campos[$(el).attr('name')] = $(el).val();

                    inputs += '<input type="hidden" name="pets[' + (numPets-1) + '][' + $(el).attr('name').split('-')[0] + '][' + $(el).attr('name').split('-')[1] + ']" value="' + $(el).val() + '">';
                    $(el).val('');
                });

                // $.each($('#addPet-form input[name*="checklistDoencas"]'), function(i,el) {
                //     if ($(el).attr('type') == 'text') {
                //         inputs += '<input type="hidden" name="pets[' + numPets + '][doencas]' + $(el).attr('name').split('checklistDoencas')[1] + '" value="' + $(el).val() + '">';
                //         $(el).val('').attr('disabled', 'disabled');
                //     } else if ($(el).attr('type') == 'checkbox') {
                //         inputs += '<input type="hidden" name="pets[' + numPets + '][doencas]' + $(el).attr('name').split('checklistDoencas')[1] + '" value="' + $(el).prop('checked') + '">';
                //         inputs += '<input type="hidden" name="pets[' + numPets + '][doencas][' + $(el).attr('data-count') + '][doenca]" value="' + $(el).attr('data-text') + '">';
                //         $(el).prop('checked', false);
                //     }
                // });

                petsLista.find('ul').prepend('' +
                    '<li class="mt-list-item" data-numpet="'+numPets+'">' +
                    '    <div class="list-icon-container done" style="padding: 0;">' +
                    '        <span class="Item ' + (campos["pet-tipo"] == "GATO" ? "Cat" : "Dog") + '" style="display: block; margin: 5px auto;"></span>' +
                    '    </div>' +
                    '    <div class="list-datetime" style="width: 100px;">' +
                    '        R$ ' + campos["plano-valor_plano"] +
                    '        <br>' +
                    '        <button type="button" class="btn btn-danger btn-xs btn-delete-pet" onclick="deletePet(\''+numPets+'\')">' +
                    '            <i class="fa fa-trash"></i>' +
                    '        </button>' +
                    '    </div>' +
                    '    <div class="list-item-content" style="padding-right: 100px;">' +
                    '        <h3 class="uppercase bold">' +
                    '            ' + campos["pet-nome_pet"] +
                    '        </h3>' +
                    '        <p>' + nomePlano + '</p>' +
                    '        <div>' + inputs + '</div>' +
                    '    </div>' +
                    '</li>' +
                '');

                var lista_doencas = $('#doencas_pre_existentes .list-item-content');
                $.each(lista_doencas, function (i, el) {
                    $(el).find('.pets').prepend('' +
                        '<div class="row" data-numpet="'+numPets+'">' +
                            '<div class="col-sm-12">' +
                                '<div class="mt-checkbox-list">' +
                                    '<label class="mt-checkbox">' +
                                        '<input type="checkbox" id="checklist_doencas-' + (numPets-1) + '-' + i + '" name="doencas_pre_existentes[' + i + '][pets][' + (numPets-1) + '][possui]" class="md-check" onchange="$(this).parent().next().prop(\'disabled\', function(i, v) { return !v; });">' +
                                        campos["pet-nome_pet"] +
                                        '<span></span>' +
                                    '</label>' +
                                    '<input type="text" class="form-control input-sm descricao_doenca margin-top-10 margin-bottom-10" name="doencas_pre_existentes[' + i + '][pets][' + (numPets-1) + '][descricao]" disabled>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '');
                });

                var valor_pagamento = $('#valor_pagamento').val();
                valor_pagamento = valor_pagamento.replace(/\./g, "");
                valor_pagamento = valor_pagamento.replace(/\,/g, ".");
                
                console.log('valor_inicial ' + valor_pagamento);

                valor_pagamento = parseFloat(valor_pagamento ? valor_pagamento : 0);

                console.log('valor_inicial_float ' + valor_pagamento);

                var valor_plano = campos["plano-valor_plano"];

                if(valor_plano != '') {
                    valor_plano = valor_plano.replace(/\./g, "");
                    valor_plano = valor_plano.replace(/\,/g, ".");
                    valor_plano = parseFloat(valor_plano);

                    console.log('valor_plano ' + valor_plano);

                    valor_pagamento = valor_plano + valor_pagamento;

                    console.log('valor_plano + valor_pagamento ' + valor_pagamento);
                }
                

                var valor_adesao = campos["plano-valor_adesao"];

                if(valor_adesao != '') {
                    valor_adesao = valor_adesao.replace(/\./g, "");
                    valor_adesao = valor_adesao.replace(/\,/g, ".");
                    valor_adesao = parseFloat(valor_adesao);

                    console.log('valor_adesao ' + valor_adesao);

                    valor_pagamento = valor_adesao + valor_pagamento;

                    console.log('valor_adesao + valor_pagamento ' + valor_pagamento);
                }

                console.log('valor_final ' + valor_pagamento);

                valor_pagamento = valor_pagamento.toFixed(2).toString().replace(/\./, ',');

                console.log('valor_final_formatado ' + valor_pagamento);

                $('#valor_pagamento').val(valor_pagamento);
                $('#valor_pagamento').mask('#.##0,00', {reverse: true});
          

                lista_doencas.find('.md-check').prop('checked', false);
                lista_doencas.find('.descricao_doenca').prop('disabled', true);

                $('#modal-addPet').modal('toggle');

                return false;
            }

        }

        function deletePet(numpet) {

            var valor_pagamento = $('#valor_pagamento').val();
            valor_pagamento = valor_pagamento.replace(/\./g, "");
            valor_pagamento = valor_pagamento.replace(/\,/g, ".");
            
            console.log('delete valor_inicial = ' + valor_pagamento);

            valor_pagamento = parseFloat(valor_pagamento ? valor_pagamento : 0);

            var valor_plano = $("[name='pets["+(parseInt(numpet)-1).toString()+"][plano][valor_plano]']").val();

            if(valor_plano != '') {
                valor_plano = valor_plano.replace(/\./g, "");
                valor_plano = valor_plano.replace(/\,/g, ".");
                valor_plano = parseFloat(valor_plano);

                console.log('delete valor_plano = ' + valor_plano);

                valor_pagamento = valor_pagamento - valor_plano;

                console.log('delete valor_pagamento - valor_plano = ' + valor_pagamento);
            }

            var valor_adesao = $("[name='pets["+(parseInt(numpet)-1).toString()+"][plano][valor_adesao]']").val();

            if(valor_adesao != '') {
                valor_adesao = valor_adesao.replace(/\./g, "");
                valor_adesao = valor_adesao.replace(/\,/g, ".");
                valor_adesao = parseFloat(valor_adesao);

                console.log('delete valor_adesao = ' + valor_adesao);

                valor_pagamento = valor_pagamento - valor_adesao;

                console.log('delete valor_pagamento - valor_adesao = ' + valor_pagamento);
            }

            if(valor_pagamento < 0) {
                valor_pagamento = 0;
            }
            valor_pagamento = valor_pagamento.toFixed(2).toString().replace(/\./, ',');

            console.log('delete valor_final_formatado = ' + valor_pagamento);

            $('#valor_pagamento').val(valor_pagamento);
            $('#valor_pagamento').mask('#.##0,00', {reverse: true});

            $('li[data-numpet="'+numpet+'"]').remove();
            $('div[data-numpet="'+numpet+'"]').remove();
        }
    </script>
    <script>
        $(document).ready(function() {

            $('#forma_pagamento').change(function() {
                var forma_pagamento_sel = $('option:selected', this).val();    
                console.log(forma_pagamento_sel);
                $('#forma_primeiro_pagamento').val('Boleto');
                if(forma_pagamento_sel == 'cartao') {
                    $('#forma_primeiro_pagamento').val('Cartão');
                }

                $('#forma_primeiro_pagamento').trigger('change');
                
                console.log($('#forma_primeiro_pagamento').val());

            });

            $('.datec').mask('00/00');

            //Checar CPF
            $("#cpf").blur(function(e) {
                var cpf = $(this).val();
                if (cpf) {
                    $.get('/api/v1/cpf/'+cpf).then(function(data) {
                        if(data.exists) {
                            swal(
                                'Erro',
                                'Já existe um cliente com esse CPF cadastrado.',
                                'error'
                            );
                            $("#cpf").val("");
                        }

                    });
                }
            });

            //Checar CPF
            $("#email").blur(function(e) {
                var email = $(this).val();
                if (email) {
                    $.get('/api/v1/email/'+email).then(function(data) {
                        if(data.exists) {
                            swal(
                                'Erro',
                                'Já existe um cliente com esse EMAIL cadastrado.',
                                'error'
                            );
                            $("#email").val("");
                        }

                    });
                }
            });

            $('.cep-search').click(function (e) {
                var cep = $('input[name="cliente[endereco][cep]"]');
                var cepHidden = $('input[name="cep"]');
                cepHidden.val(cep.val()).blur();
            });

        });
    </script>
@endsection
