<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta name="viewport" content="width=800, user-scalable=no, initial-scale=1">

    {{--<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">--}}
    {{--<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.orange-blue.min.css" />--}}
    {{--<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>--}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,200i,300,300i,400,400i,600,600i,700,700i,900,900i" rel="stylesheet">
    <script src="{{ url('/') }}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    {{--<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>--}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="{{ url('/') }}/assets/global/plugins/bootstrap4/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="{{ url('/') }}/assets/global/plugins/bootstrap4/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    {{--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">--}}

    <script src="/_app_cadastro_cliente/inc/jquery.mask.js?"></script>
    <title>Lifepet - Cadastro de Cliente</title>
    <meta charset="utf-8">

    <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>

    <script>
        function salvarDadosCliente(signature, e) {
            if (signature.isEmpty()) {
                swal('A assinatura é obrigatória!', 'Por favor, preencha sua assinatura.', 'warning');
            } else if(!signature._canvas.classList.contains('confirmado')) {
                swal('A assinatura é obrigatória!', 'Por favor, confirme a assinatura.', 'warning');
            } else {
                if (validateContext("#dadospessoais", e)) {
                    $.ajax({
                        url: "{{ route('app_cadastro_cliente.cadastrar') }}",
                        method: 'post',
                        data: new FormData($('#dadospessoais')[0]),
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            swal({
                                title: 'Cliente Cadastrado!',
                                type:'success',
                                showConfirmButton: true,
                                confirmButtonColor: '#ff8400',
                                confirmButtonText: 'Cadastrar Pets',
                                allowOutsideClick: false
                            }).then(function (result) {
                                $('body').addClass('pageReady');
                                window.location.replace(data);
                            });
                        },
                        error: function(err) {
                            swal({
                                title: 'Erro!',
                                type:'error',
                                showConfirmButton: true,
                                confirmButtonColor: '#ff8400',
                                confirmButtonText: 'Ok',
                            });
                        },
                    });
                }
            }
        }
    </script>

    <style>
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #fff;
            opacity: 0.8;
            z-index: 9999;
        }

        .loading-overlay .spin-loader {
            height: 100px;
            margin: 50% auto 30px;
            background: url({{ asset('_app_cadastro_cliente/images/loader.gif') }}) no-repeat center center transparent;
            top: 25%;
        }

        .loading-overlay h2 {
            color: #000;
            height: 100px;
            text-align: center;
            font-size: 40px;
        }

        .group-buscacpf .form-group {
            width: 70%;
        }
        .group-buscacpf a.btnBuscaCPF {
            width: 30%;
            margin: 0px 0 20px !important;
            font-size: 20px !important;
            padding: 6px;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('_app_cadastro_cliente/css/style.css') }}?{{ time() }}">

</head>
<body>

<div class="loading-overlay">
    <div class="spin-loader"></div>
    <h2 style="color: black;">Enviando...</h2>
</div>

{{--@include('app_cadastro_cliente.parts.contrato')--}}
{{--@include('app_cadastro_cliente.parts.regulamento')--}}
<div id="geral_cadastro_app_cadastro_cliente">
    <div class="logocampanhag col-md-12">
        <img src="https://www.lifepet.com.br/wp-content/uploads/2018/12/LOGOTIPO_VF.png" height="100px" style="margin-top: 20px; " />
    </div>

    <div id="telas">
        {{--<form action="#" method="post" id="form-compra" enctype="multipart/form-data">--}}
            {{--{{ csrf_field() }}--}}

            {{--@include('app_cadastro_cliente.parts.tela_regulamento')--}}
            @include('app_cadastro_cliente.parts.tela_dadospessoais')
            {{--@include('app_cadastro_cliente.parts.tela_endereco')--}}
            {{--@include('app_cadastro_cliente.parts.tela_dadospet')--}}

        {{--</form>--}}
        <a href="" class="avanca hidden"></a>
    </div>

</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script>

    var signaturePad = null;

    signaturePad = null || document.getElementById('signature-pad-cliente');
    if(signaturePad) {
        var signaturePadCliente = new SignaturePad(signaturePad, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1,
            maxWidth: 1,
        });
    }

    signaturePad = null || document.getElementById('signature-pad-pets');
    if(signaturePad) {
        var signaturePadPets = new SignaturePad(signaturePad, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1,
            maxWidth: 1,
        });
    }

    signaturePad = null || document.getElementById('signature-pad-checklist');
    if(signaturePad) {
        var signaturePadChecklist = new SignaturePad(signaturePad, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1,
            maxWidth: 1,
        });
    }

    // Ações
    function signatureSave(signature, triggerButton) {
        if (signature.isEmpty()) {
            swal('A assinatura é obrigatória!', 'Por favor, preencha sua assinatura.', 'warning');
        }
        else {
            signature.off();
            triggerButton.closest('form').find('.assinatura').val(signature.toDataURL("image/png"));
            triggerButton.hide();
            triggerButton.next().show();
            triggerButton.siblings().addClass('disabled');
            triggerButton.next().removeClass('disabled', false);
            signature._canvas.classList.add('confirmado');
            // var dataURL = signature.toDataURL("image/jpeg");
            // download(dataURL, "assinatura-" + (new Date).getTime() + ".jpg");
        }
    }

    function signatureRemake(signature, triggerButton) {
        signature.on();
        triggerButton.hide();
        triggerButton.prev().show();
        triggerButton.siblings().removeClass('disabled');
        triggerButton.prev().removeClass('disabled');
        signature._canvas.classList.remove('confirmado');
    }

    function signatureClear(signature) {
        signature.clear();
    }

    function signatureUndo(signature) {
        var data = signature.toData();
        if (data) {
            data.pop(); // remove the last dot or line
            signature.fromData(data);
        }
    }

    function download(dataURL, filename) {
        if (navigator.userAgent.indexOf("Safari") > -1 && navigator.userAgent.indexOf("Chrome") === -1) {
            window.open(dataURL);
        } else {
            var blob = dataURLToBlob(dataURL);
            var url = window.URL.createObjectURL(blob);

            var a = document.createElement("a");
            a.style = "display: none";
            a.href = url;
            a.download = filename;

            document.body.appendChild(a);
            a.click();

            window.URL.revokeObjectURL(url);
        }
    }

    function dataURLToBlob(dataURL) {
        // Code taken from https://github.com/ebidel/filer.js
        var parts = dataURL.split(';base64,');
        var contentType = parts[0].split(":")[1];
        var raw = window.atob(parts[1]);
        var rawLength = raw.length;
        var uInt8Array = new Uint8Array(rawLength);

        for (var i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }

        return new Blob([uInt8Array], { type: contentType });
    }

    function animate_tela(size) {
        $('html, body').animate({
            scrollTop: 0
        }, 500);
        $( "#telas" ).animate({
            left: size,
            opacity: 1
        }, {
            duration: 500,
            queue: false
        });
    }

    function avanca_tela(signature) {
        animate_tela("-=800px");
    }

    function volta_tela() {
        animate_tela("+=800px");
    }

    function jqSelector(str)
    {
        return str.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, '\\$1');
    }

    function launchModal(form) {
        var modal = form.find('.modal');
        var data = form.serializeArray();
        modal.modal();
        data.forEach(function (field) {
            if (field.value) {
                modal.find('.' + jqSelector(field.name) + ' span').text(field.value);
            }
        });
    }

    $('.voltar').click(function (e) {
        volta_tela();
    });
</script>
<script type="text/javascript">

    $(document).ready(function() {
        $('#telas input[type="file"]').filestyle(
            // {
            //     buttonName : 'btn-foto',
            //     buttonText : '<div class="btn btn-light"><i class="fa fa-camera"></i></div>'
            // }
        );

        // window.validate = function(container) {
        //     var validation = {
        //         invalidFields: [],
        //         isValid: true
        //     };
        //     if(!container) {
        //         container = "#form-compra";
        //     }
        //
        //     var inputs = $(container).find("input, select, textarea").toArray();
        //     var inputIsValid = false;
        //     var description = "";
        //     for(var i = 0; i < inputs.length; i++) {
        //         if($(inputs[i]).hasClass('disabled')) {
        //             continue;
        //         }
        //
        //         inputIsValid = inputs[i].checkValidity();
        //         if(!inputIsValid) {
        //             description = $(inputs[i]).data('description');
        //             if(description && description !== "") {
        //                 validation.invalidFields.push({
        //                     "name" : description,
        //                     "element" : inputs[i]
        //                 });
        //             }
        //         }
        //         validation.isValid = validation.isValid && inputIsValid;
        //     }
        //
        //     return validation;
        // };
        //
        // window.validateContext = function(context, e) {
        //     var validation = validate(context);
        //
        //     if(!validation.isValid) {
        //
        //         validation.invalidFields.forEach(function(item, index, all) {
        //             $(item.element).addClass('error').change(function() {
        //                 $(this).removeClass('error');
        //             });
        //         });
        //
        //         var message = "<ul class='swal-required'>" + validation.invalidFields.map(function(i){
        //             return "<li>" + i.name + "</li>";
        //         }).join('') + "</ul>";
        //
        //         swal({
        //             title: 'Oops!',
        //             html: "Para finalizar, você precisa preencher todos os campos. Volte e verifique se não faltou nada no seu cadastro.<br/><br/>"+message,
        //             type: 'warning',
        //             confirmButtonColor: '#ff8400',
        //             confirmButtonText: 'Ok!'
        //         });
        //
        //         e.preventDefault();
        //         return false;
        //
        //     } else {
        //
        //         return true;
        //
        //     }
        // };

        $('#cadastrarEndereco').click(function(e) {
            if(validateContext("#endereco", e)) {
                $.ajax({
                    url: "{{ route('app_cadastro_cliente.cadastrarEndereco') }}",
                    method: 'post',
                    data: new FormData($('#endereco')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data){
                        //
                    },
                });
            }
        });

        $('#cadastrarPet').click(function(e) {
            if(validateContext("#dadospet", e)) {
                $.ajax({
                    url: "{{ route('app_cadastro_cliente.cadastrarPet') }}",
                    method: 'post',
                    data: new FormData($('#dadospet')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data){
                        swal({
                            title:  'Pet Cadastrado!',
                            text: 'Você pode cadastar mais um pet ou avançar.',
                            type:'success',
                            showCancelButton: true,
                            showConfirmButton: true,
                            confirmButtonColor: '#ff8400',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Avançar',
                            cancelButtonText: 'Novo'
                        }).then(function (result) {
                            if (result.dismiss !== "cancel") {
                                avanca_tela();
                                $("#dados_pet").find('select,input,textarea').addClass('disabled');
                            } else {
                                $("#dados_pet").find('input,textarea').val('');
                                $("#dados_pet select").prop('selectedIndex', 0);
                            }
                        })
                    },
                });
            }
        });
    });



    function addPet() {
        if(typeof pets === "undefined") {
            pets = 0;
        }

        var dadosOriginais = $('#dados_pet').find("input,select,textarea");
        var dados = dadosOriginais.clone();
        var nome = $('#nome_pet').val();
        var tipo = $('#tipo_pet').val();
        var petDataIdentifier = "pet_data_" + pets;
        $("<li><span class='pet "+ tipo + "'></span>" +nome+"</li>").appendTo("#lista_pets");
        var container = $("<div/>", {
            id: petDataIdentifier,
            class: 'pet_data'
        });
        container.appendTo("#pets_container");

        dadosOriginais.each(function(k,v) {
            var name = $(v).attr('name');
            name = "pets["+pets+"]["+name+"]";
            if($(v).is("select")) {
                $("<input type=hidden name='" + name + "' value='" + $(v).val() + "'/>").appendTo(container);
            }
        });
        dados.each(function(k,v) {
            var name = $(v).attr('name');
            name = "pets["+pets+"]["+name+"]";
            $(v).removeAttr('id');
            $(v).attr('name', name);
        });

        dados.filter(':not(select)').appendTo(container);

        /**
         * Reseta as escolhas
         */
        dadosOriginais.val("");
        pets++;
        var valorAdesao = pets * 75.00;
        $("#valor-adesao").html(valorAdesao.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+\,)/g, "$1."))
    }

    $( "a.addpet" ).click(function(e) {
        var context = "#dados_pet";
        var validated = window.validateContext(context, e);
        if(!validated) {
            return false;
        }

        addPet();

        swal({
            title: 'Cadastrando Pet',
            text: 'Aguarde a confirmação',
            showCancelButton: false, // There won't be any cancel button
            showConfirmButton: false,
            timer: 3000
        }).then(function (result) {
            if (result.dismiss === swal.DismissReason.timer) {
                swal({
                    title:  'Cadastro Realizado!',
                    text: 'Você pode cadastar mais um pet ou avançar.',
                    type:'success',
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonColor: '#ff8400',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Avançar',
                    cancelButtonText: 'Novo'
                }).then(function (result) {
                    if (result.dismiss !== "cancel") {
                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);
                        $( "#telas" ).animate({
                            left: "-=800px",
                            opacity: 1
                        }, {
                            duration: 500,
                            queue: false
                        });

                        $("#dados_pet").find('select,input,textarea').addClass('disabled');
                    } else {

                    }
                })
            } else {
                swal({
                    title:'Erro ao cadastrar!',
                    text: 'Tente enviar novamente!',
                    type:  'alert',
                    showConfirmButton: true
                });
            }

        });

    });
    $( "a.pagar" ).click(function(e) {
        var validated = window.validateContext(null, e);
        if(!validated) {
            return false;
        }
        $(this).prop('disabled', true);
        swal({
            title: 'Enviando informarções',
            html: 'Por favor, aguarde o envio dos dados e não feche.',
            showCancelButton: false, // There won't be any cancel button
            showConfirmButton: false,
            allowOutsideClick: false,
            showLoaderOnConfirm: true
        });
        $("#form-compra").submit();
    });

    $("a.final").click(function() {

        swal({
            title: 'Enviando Indicações',
            text: 'Aguarde a confirmação',
            showCancelButton: false, // There won't be any cancel button
            showConfirmButton: false,
            timer: 3000
        }).then(function (result) {
            if (

                    result.dismiss === swal.DismissReason.timer
            ) {
                swal({
                    title:  'Adesão realizada com sucesso!',
                    text: 'Seus dados serão analisados pelo nosso atendimento. Assim que aprovado, você será avisado por e-mail com os próximos passos.',
                    type:'success',
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonColor: '#ff8400',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Finalizar'
                }).then(function (result) {
                    if (result.value) {
                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);

                        window.location.href = "https://app.lifepet.com.br/cliente/login";

                        $( "#telas" ).animate({
                            left: "=0px",
                            opacity: 1
                        }, {
                            duration: 500,
                            queue: false
                        });
                    }
                });

            }else{

                swal({
                    title:'Erro ao enviar!',
                    text: 'Tente enviar novamente!',
                    type:  'warning',
                    showConfirmButton: true
                })
            }

        });

    });
    $("a.euaceito").click(function() {
        swal({
            title: 'Bem-vindo!',
            html: "Para iniciarmos, você precisará ter em mãos:<br/><br/>- Seu RG ou CNH;<br/>- Um comprovante de residência;<br/>- A carteirinha de vacinação de cada pet.<br/><br/>Este plano em breve estará disponível para quem já é cliente.<br/><br/> Atenção: esse plano só poderá ser contratado por moradores da Grande Vitória, no Espírito Santo.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff8400',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Vamos lá!',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (result.value) {
                $( "#telas" ).animate({
                    left: "-800px",
                    opacity: 1
                }, {
                    duration: 500,
                    queue: false
                });
            }
        });

        $('html, body').animate({
            scrollTop: 0
        }, 500);
    });
    $( "a.endereco" ).click(function() {
        $( "#telas" ).animate({
            left: "-640px",
            opacity: 1
        }, {
            duration: 500,
            queue: false
        });
        $('html, body').animate({
            scrollTop: 0
        }, 500);
    });

    function numberToReal(numero) {
        var numero = numero.toFixed(2).split('.');
        numero[0] = "R$ " + numero[0].split(/(?=(?:...)*$)/).join('.');
        return numero.join(',');
    }

    $("#pets").change(function() {
       valorBase = {{ \App\Http\Controllers\AppCadastroClienteController::VALOR_ADESAO }};
       $('#valor-adesao').html(numberToReal(valorBase * $('#pets').val()));
    });



    function feedbackCPF(cpf) {
        if (cpf == '') {
            swal({
                title: 'CPF Obrigatório!',
                type:'warning',
                showConfirmButton: true,
                confirmButtonColor: '#ff8400',
                confirmButtonText: 'Ok',
            });
        } else if(!ValidaCPF(cpf)) {
            swal({
                title: 'CPF Inválido!',
                type:'error',
                showConfirmButton: true,
                confirmButtonColor: '#ff8400',
                confirmButtonText: 'Ok',
            });
        } else {
            $.ajax({
                url: "/clientes/"+cpf+"/checkClienteCpf",
                method: 'get',
                cache: false,
                contentType: false,
                processData: false,
                success: function(data){
                    if(!data.exists) {
                        swal('Cliente não encontrado!', 'Bem vindo à Lifepet!', 'warning');
                    } else {

                        swal('Cliente encontrado!', 'Confira seus dados antes de avançar.', 'success');

                        var dt_nascimento = data.dados.data_nascimento.split(' ')[0].split('-');
                        dt_nascimento = dt_nascimento[2]+ '/' +dt_nascimento[1]+ '/' +dt_nascimento[0];

                        $('input[name="id_cliente"]').val(data.dados.id);

                        $('input[name="cliente[nome_cliente]"]').val(data.dados.nome_cliente);
                        $('select[name="cliente[sexo]"]').val((data.dados.sexo == 'M' ? 'Masculino' : 'Feminino'));
                        $('input[name="cliente[rg]"]').val(data.dados.rg);
                        $('input[name="cliente[email]"]').val(data.dados.email);
                        $('input[name="cliente[celular]"]').val(data.dados.celular);
                        $('input[name="cliente[telefone_fixo]"]').val(data.dados.telefone_fixo);
                        $('input[name="cliente[data_nascimento]"]').val(dt_nascimento);
                        // $('input[name="cliente[observacoes]"]').val(data.dados.observacoes);
                        $('input[name="cliente[cep]"]').val(data.dados.cep);
                        $('input[name="cliente[numero_endereco]"]').val(data.dados.numero_endereco);
                        $('input[name="cliente[complemento_endereco]"]').val(data.dados.complemento_endereco);
                        $('input[name="cliente[rua]"]').val(data.dados.rua);
                        $('input[name="cliente[bairro]"]').val(data.dados.bairro);
                        $('input[name="cliente[cidade]"]').val(data.dados.cidade);
                        $('input[name="cliente[estado]"]').val(data.dados.estado);
                    }
                },
            });
        }
    }

    $('.cpf').blur(function () {
        feedbackCPF($('.cpf').val());
    });

    $('.btnBuscaCPF').click(function () {
        feedbackCPF($('.cpf').val());
    });

</script>
<script type="text/javascript" src="{{ asset('_app_cadastro_cliente/js/scripts.js') }}?{{ time() }}"></script>
<script type="text/javascript" src="{{ mix('js/app.js') }}?{{ time() }}"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-85146807-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-85146807-1');
</script>
</body>
</html>

