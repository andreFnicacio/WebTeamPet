<!DOCTYPE html>
<html lang="pt-br">
<head>

    <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>
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

    {{--<script type="text/javascript">--}}
        {{--(function($){var nextId=0;var Filestyle=function(element,options){this.options=options;this.$elementFilestyle=[];this.$element=$(element)};Filestyle.prototype={clear:function(){this.$element.val("");this.$elementFilestyle.find(":text").val("");this.$elementFilestyle.find(".badge").remove()},destroy:function(){this.$element.removeAttr("style").removeData("filestyle");this.$elementFilestyle.remove()},disabled:function(value){if(value===true){if(!this.options.disabled){this.$element.attr("disabled","true");this.$elementFilestyle.find("label").attr("disabled","true");this.options.disabled=true}}else{if(value===false){if(this.options.disabled){this.$element.removeAttr("disabled");this.$elementFilestyle.find("label").removeAttr("disabled");this.options.disabled=false}}else{return this.options.disabled}}},buttonBefore:function(value){if(value===true){if(!this.options.buttonBefore){this.options.buttonBefore=true;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{if(value===false){if(this.options.buttonBefore){this.options.buttonBefore=false;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{return this.options.buttonBefore}}},icon:function(value){if(value===true){if(!this.options.icon){this.options.icon=true;this.$elementFilestyle.find("label").prepend(this.htmlIcon())}}else{if(value===false){if(this.options.icon){this.options.icon=false;this.$elementFilestyle.find(".icon-span-filestyle").remove()}}else{return this.options.icon}}},input:function(value){if(value===true){if(!this.options.input){this.options.input=true;if(this.options.buttonBefore){this.$elementFilestyle.append(this.htmlInput())}else{this.$elementFilestyle.prepend(this.htmlInput())}this.$elementFilestyle.find(".badge").remove();this.pushNameFiles();this.$elementFilestyle.find(".group-span-filestyle").addClass("input-group-btn")}}else{if(value===false){if(this.options.input){this.options.input=false;this.$elementFilestyle.find(":text").remove();var files=this.pushNameFiles();if(files.length>0&&this.options.badge){this.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}this.$elementFilestyle.find(".group-span-filestyle").removeClass("input-group-btn")}}else{return this.options.input}}},size:function(value){if(value!==undefined){var btn=this.$elementFilestyle.find("label"),input=this.$elementFilestyle.find("input");btn.removeClass("btn-lg btn-sm");input.removeClass("input-lg input-sm");if(value!="nr"){btn.addClass("btn-"+value);input.addClass("input-"+value)}}else{return this.options.size}},placeholder:function(value){if(value!==undefined){this.options.placeholder=value;this.$elementFilestyle.find("input").attr("placeholder",value)}else{return this.options.placeholder}},buttonText:function(value){if(value!==undefined){this.options.buttonText=value;this.$elementFilestyle.find("label .buttonText").html(this.options.buttonText)}else{return this.options.buttonText}},buttonName:function(value){if(value!==undefined){this.options.buttonName=value;this.$elementFilestyle.find("label").attr({"class":"btn "+this.options.buttonName})}else{return this.options.buttonName}},iconName:function(value){if(value!==undefined){this.$elementFilestyle.find(".icon-span-filestyle").attr({"class":"icon-span-filestyle "+this.options.iconName})}else{return this.options.iconName}},htmlIcon:function(){if(this.options.icon){return'<span class="icon-span-filestyle '+this.options.iconName+'"></span> '}else{return""}},htmlInput:function(){if(this.options.input){return'<input type="text" class=" '+(this.options.size=="nr"?"":"input-"+this.options.size)+'" placeholder="'+this.options.placeholder+'" disabled> '}else{return""}},pushNameFiles:function(){var content="",files=[];if(this.$element[0].files===undefined){files[0]={name:this.$element[0]&&this.$element[0].value}}else{files=this.$element[0].files}for(var i=0;i<files.length;i++){content+=files[i].name.split("\\").pop()+", "}if(content!==""){this.$elementFilestyle.find(":text").val(content.replace(/\, $/g,""))}else{this.$elementFilestyle.find(":text").val("")}return files},constructor:function(){var _self=this,html="",id=_self.$element.attr("id"),files=[],btn="",$label;if(id===""||!id){id="filestyle-"+nextId;_self.$element.attr({id:id});nextId++}btn='<span class="group-span-filestyle '+(_self.options.input?"input-group-btn":"")+'"><label for="'+id+'" class="btn '+_self.options.buttonName+" "+(_self.options.size=="nr"?"":"btn-"+_self.options.size)+'" '+(_self.options.disabled?'disabled="true"':"")+">"+_self.htmlIcon()+'<span class="buttonText">'+_self.options.buttonText+"</span></label></span>";html=_self.options.buttonBefore?btn+_self.htmlInput():_self.htmlInput()+btn;_self.$elementFilestyle=$('<div class="bootstrap-filestyle input-group">'+html+"</div>");_self.$elementFilestyle.find(".group-span-filestyle").attr("tabindex","0").keypress(function(e){if(e.keyCode===13||e.charCode===32){_self.$elementFilestyle.find("label").click();return false}});_self.$element.css({position:"absolute",clip:"rect(0px 0px 0px 0px)"}).attr("tabindex","-1").after(_self.$elementFilestyle);if(_self.options.disabled){_self.$element.attr("disabled","true")}_self.$element.change(function(){var files=_self.pushNameFiles();if(_self.options.input==false&&_self.options.badge){if(_self.$elementFilestyle.find(".badge").length==0){_self.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}else{if(files.length==0){_self.$elementFilestyle.find(".badge").remove()}else{_self.$elementFilestyle.find(".badge").html(files.length)}}}else{_self.$elementFilestyle.find(".badge").remove()}});if(window.navigator.userAgent.search(/firefox/i)>-1){_self.$elementFilestyle.find("label").click(function(){_self.$element.click();return false})}}};var old=$.fn.filestyle;$.fn.filestyle=function(option,value){var get="",element=this.each(function(){if($(this).attr("type")==="file"){var $this=$(this),data=$this.data("filestyle"),options=$.extend({},$.fn.filestyle.defaults,option,typeof option==="object"&&option);if(!data){$this.data("filestyle",(data=new Filestyle(this,options)));data.constructor()}if(typeof option==="string"){get=data[option](value)}}});if(typeof get!==undefined){return get}else{return element}};$.fn.filestyle.defaults={--}}
            {{--buttonText:"Carregar Foto",iconName:"fa fa-camera",buttonName:"btn-light",size:"lg",input:false,badge:false,icon:true,buttonBefore:false,disabled:false,placeholder:""};$.fn.filestyle.noConflict=function(){$.fn.filestyle=old;return this};$(function(){$(".filestyle").each(function(){var $this=$(this),options={input:$this.attr("data-input")==="false"?false:true,icon:$this.attr("data-icon")==="false"?false:true,buttonBefore:$this.attr("data-buttonBefore")==="true"?true:false,disabled:$this.attr("data-disabled")==="true"?true:false,size:$this.attr("data-size"),buttonText:$this.attr("data-buttonText"),buttonName:$this.attr("data-buttonName"),iconName:$this.attr("data-iconName"),badge:$this.attr("data-badge")==="false"?false:true,placeholder:$this.attr("data-placeholder")};$this.filestyle(options)})})})(window.jQuery);--}}
    {{--</script>--}}
    {{--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">--}}

    <script src="/_app_cadastro_cliente/inc/jquery.mask.js?"></script>
    <title>Lifepet - Cadastro de Pets</title>
    <meta charset="utf-8">

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
        @include('app_cadastro_cliente.parts.tela_dadospet')
        <a href="" class="avanca hidden"></a>
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
            swal('A assinatura é obrigatória!', 'Por favor, confirme-a antes de salvar.', 'warning');
        }
        else {
            signature.off();
            triggerButton.closest('form').find('.assinatura').val(signature.toDataURL("image/png"));
            triggerButton.hide();
            triggerButton.next().show();
            triggerButton.siblings().addClass('disabled');
            triggerButton.next().removeClass('disabled', false);
            signature._canvas.classList.add('confirmado');
            var dataURL = signature.toDataURL("image/jpeg");
            download(dataURL, "assinatura-" + (new Date).getTime() + ".jpg");
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

        window.validate = function(container) {
            var validation = {
                invalidFields: [],
                isValid: true
            };
            if(!container) {
                container = "#form-compra";
            }

            var inputs = $(container).find("input, select, textarea").toArray();
            var inputIsValid = false;
            var description = "";
            for(var i = 0; i < inputs.length; i++) {
                if($(inputs[i]).hasClass('disabled')) {
                    continue;
                }

                inputIsValid = inputs[i].checkValidity();
                if(!inputIsValid) {
                    description = $(inputs[i]).data('description');
                    if(description && description !== "") {
                        validation.invalidFields.push({
                            "name" : description,
                            "element" : inputs[i]
                        });
                    }
                }
                validation.isValid = validation.isValid && inputIsValid;
            }

            return validation;
        };

        window.validateContext = function(context, e) {
            var validation = validate(context);

            if(!validation.isValid) {

                validation.invalidFields.forEach(function(item, index, all) {
                    $(item.element).addClass('error').change(function() {
                        $(this).removeClass('error');
                    });
                });

                var message = "<ul class='swal-required'>" + validation.invalidFields.map(function(i){
                    return "<li>" + i.name + "</li>";
                }).join('') + "</ul>";

                swal({
                    title: 'Oops!',
                    html: "Para finalizar, você precisa preencher todos os campos. Volte e verifique se não faltou nada no seu cadastro.<br/><br/>"+message,
                    type: 'warning',
                    confirmButtonColor: '#ff8400',
                    confirmButtonText: 'Ok!'
                });

                e.preventDefault();
                return false;

            } else {

                return true;

            }
        };

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
                            title:  'Pet(s) Cadastrado(s)!',
                            type:'success',
                            showConfirmButton: true,
                            confirmButtonColor: '#ff8400',
                            confirmButtonText: 'Avançar',
                            allowOutsideClick: false
                        }).then(function (result) {
                            $('body').addClass('pageReady');
                            window.location.replace(data);
                        })
                    },
                });
            }
        });
    });

    $(function() {
        $('.date').mask('00/00/0000');
        $('.datec').mask('00/00');
        $('.dated').mask('0000 0000 0000 0000');
        $('.time').mask('00:00:00');
        $('.date_time').mask('00/00/0000 00:00:00');
        $('.cep').mask('00000-000');
        $('.phone').mask('0000-0000');
        $('.phone_with_ddd').mask('(00) 0000-0000');
        $('.phone').mask('(00) 0000-0000');
        $('.mixed').mask('AAA 000-S0S');
        $('.validade').mask('00/00');
        $('.ip_address').mask('099.099.099.099');
        $('.percent').mask('##0,00%', {reverse: true});
        $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
        $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
        $('.fallback').mask("00r00r0000", {
            translation: {
                'r': {
                    pattern: /[\/]/,
                    fallback: '/'
                },
                placeholder: "__/__/____"
            }
        });

        $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});

        $('.cep_with_callback').mask('00000-000', {onComplete: function(cep) {
            console.log('Mask is done!:', cep);
        },
            onKeyPress: function(cep, event, currentField, options){
                console.log('An key was pressed!:', cep, ' event: ', event, 'currentField: ', currentField.attr('class'), ' options: ', options);
            },
            onInvalid: function(val, e, field, invalid, options){
                var error = invalid[0];
                console.log ("Digit: ", error.v, " is invalid for the position: ", error.p, ". We expect something like: ", error.e);
            }
        });

        $('.crazy_cep').mask('00000-000', {onKeyPress: function(cep, e, field, options){
            var masks = ['00000-000', '0-00-00-00'];
            mask = (cep.length>7) ? masks[1] : masks[0];
            $('.crazy_cep').mask(mask, options);
        }});

        $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
        $('.cpf').mask('000.000.000-00', {reverse: true});
        $('.money').mask('#.##0,00', {reverse: true});

        var SPMaskBehavior = function (val) {
                    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                },
                spOptions = {
                    onKeyPress: function(val, e, field, options) {
                        field.mask(SPMaskBehavior.apply({}, arguments), options);
                    }
                };

        $('.sp_celphones').mask(SPMaskBehavior, spOptions);

        $(".bt-mask-it").click(function(){
            $(".mask-on-div").mask("000.000.000-00");
            $(".mask-on-div").fadeOut(500).fadeIn(500)
        })

        $('pre').each(function(i, e) {hljs.highlightBlock(e)});
    });

    $( "a.volta" ).click(function() {
        $( "#telas" ).animate({
            left: "+=800px",
            opacity: 1
        }, {
            duration: 500,
            queue: false
        });
        $('html, body').animate({
            scrollTop: 0
        }, 500);
    });

    $( "a.avanca" ).click(function(e) {
        var context = $(this).data('context');
        var validated = window.validateContext(context, e);

        if(validated) {
            $("#telas").animate({
                left: "-=800px",
                opacity: 1
            }, {
                duration: 500,
                queue: false
            });

            $('html, body').animate({
                scrollTop: 0
            }, 500);
        }
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
<script>
    $(document).ready(function(e) {
        $('select#plano_pet').change(function (e) {
            var valorPlanoPet = $('input#valor_plano_pet');
            var realPrice = $(this).find('option:selected').data('real-price');
            valorPlanoPet.val(realPrice);
        });
    });
</script>
</body>
</html>

