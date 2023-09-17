<!DOCTYPE html>
<html lang="pt-br">
    <head>

        <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>
        <meta name="viewport" content="width=800, user-scalable=no, initial-scale=1">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,200i,300,300i,400,400i,600,600i,700,700i,900,900i" rel="stylesheet">
        <script src="{{ url('/') }}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        {{--<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>--}}

        <script src="{{ url('/') }}/assets/global/plugins/bootstrap4/js/bootstrap.min.js" type="text/javascript"></script>
        <link href="{{ url('/') }}/assets/global/plugins/bootstrap4/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript">
            (function($){var nextId=0;var Filestyle=function(element,options){this.options=options;this.$elementFilestyle=[];this.$element=$(element)};Filestyle.prototype={clear:function(){this.$element.val("");this.$elementFilestyle.find(":text").val("");this.$elementFilestyle.find(".badge").remove()},destroy:function(){this.$element.removeAttr("style").removeData("filestyle");this.$elementFilestyle.remove()},disabled:function(value){if(value===true){if(!this.options.disabled){this.$element.attr("disabled","true");this.$elementFilestyle.find("label").attr("disabled","true");this.options.disabled=true}}else{if(value===false){if(this.options.disabled){this.$element.removeAttr("disabled");this.$elementFilestyle.find("label").removeAttr("disabled");this.options.disabled=false}}else{return this.options.disabled}}},buttonBefore:function(value){if(value===true){if(!this.options.buttonBefore){this.options.buttonBefore=true;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{if(value===false){if(this.options.buttonBefore){this.options.buttonBefore=false;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{return this.options.buttonBefore}}},icon:function(value){if(value===true){if(!this.options.icon){this.options.icon=true;this.$elementFilestyle.find("label").prepend(this.htmlIcon())}}else{if(value===false){if(this.options.icon){this.options.icon=false;this.$elementFilestyle.find(".icon-span-filestyle").remove()}}else{return this.options.icon}}},input:function(value){if(value===true){if(!this.options.input){this.options.input=true;if(this.options.buttonBefore){this.$elementFilestyle.append(this.htmlInput())}else{this.$elementFilestyle.prepend(this.htmlInput())}this.$elementFilestyle.find(".badge").remove();this.pushNameFiles();this.$elementFilestyle.find(".group-span-filestyle").addClass("input-group-btn")}}else{if(value===false){if(this.options.input){this.options.input=false;this.$elementFilestyle.find(":text").remove();var files=this.pushNameFiles();if(files.length>0&&this.options.badge){this.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}this.$elementFilestyle.find(".group-span-filestyle").removeClass("input-group-btn")}}else{return this.options.input}}},size:function(value){if(value!==undefined){var btn=this.$elementFilestyle.find("label"),input=this.$elementFilestyle.find("input");btn.removeClass("btn-lg btn-sm");input.removeClass("input-lg input-sm");if(value!="nr"){btn.addClass("btn-"+value);input.addClass("input-"+value)}}else{return this.options.size}},placeholder:function(value){if(value!==undefined){this.options.placeholder=value;this.$elementFilestyle.find("input").attr("placeholder",value)}else{return this.options.placeholder}},buttonText:function(value){if(value!==undefined){this.options.buttonText=value;this.$elementFilestyle.find("label .buttonText").html(this.options.buttonText)}else{return this.options.buttonText}},buttonName:function(value){if(value!==undefined){this.options.buttonName=value;this.$elementFilestyle.find("label").attr({"class":"btn "+this.options.buttonName})}else{return this.options.buttonName}},iconName:function(value){if(value!==undefined){this.$elementFilestyle.find(".icon-span-filestyle").attr({"class":"icon-span-filestyle "+this.options.iconName})}else{return this.options.iconName}},htmlIcon:function(){if(this.options.icon){return'<span class="icon-span-filestyle '+this.options.iconName+'"></span> '}else{return""}},htmlInput:function(){if(this.options.input){return'<input type="text" class=" '+(this.options.size=="nr"?"":"input-"+this.options.size)+'" placeholder="'+this.options.placeholder+'" disabled> '}else{return""}},pushNameFiles:function(){var content="",files=[];if(this.$element[0].files===undefined){files[0]={name:this.$element[0]&&this.$element[0].value}}else{files=this.$element[0].files}for(var i=0;i<files.length;i++){content+=files[i].name.split("\\").pop()+", "}if(content!==""){this.$elementFilestyle.find(":text").val(content.replace(/\, $/g,""))}else{this.$elementFilestyle.find(":text").val("")}return files},constructor:function(){var _self=this,html="",id=_self.$element.attr("id"),files=[],btn="",$label;if(id===""||!id){id="filestyle-"+nextId;_self.$element.attr({id:id});nextId++}btn='<span class="group-span-filestyle '+(_self.options.input?"input-group-btn":"")+'"><label for="'+id+'" class="btn '+_self.options.buttonName+" "+(_self.options.size=="nr"?"":"btn-"+_self.options.size)+'" '+(_self.options.disabled?'disabled="true"':"")+">"+_self.htmlIcon()+'<span class="buttonText">'+_self.options.buttonText+"</span></label></span>";html=_self.options.buttonBefore?btn+_self.htmlInput():_self.htmlInput()+btn;_self.$elementFilestyle=$('<div class="bootstrap-filestyle input-group">'+html+"</div>");_self.$elementFilestyle.find(".group-span-filestyle").attr("tabindex","0").keypress(function(e){if(e.keyCode===13||e.charCode===32){_self.$elementFilestyle.find("label").click();return false}});_self.$element.css({position:"absolute",clip:"rect(0px 0px 0px 0px)"}).attr("tabindex","-1").after(_self.$elementFilestyle);if(_self.options.disabled){_self.$element.attr("disabled","true")}_self.$element.change(function(){var files=_self.pushNameFiles();if(_self.options.input==false&&_self.options.badge){if(_self.$elementFilestyle.find(".badge").length==0){_self.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}else{if(files.length==0){_self.$elementFilestyle.find(".badge").remove()}else{_self.$elementFilestyle.find(".badge").html(files.length)}}}else{_self.$elementFilestyle.find(".badge").remove()}});if(window.navigator.userAgent.search(/firefox/i)>-1){_self.$elementFilestyle.find("label").click(function(){_self.$element.click();return false})}}};var old=$.fn.filestyle;$.fn.filestyle=function(option,value){var get="",element=this.each(function(){if($(this).attr("type")==="file"){var $this=$(this),data=$this.data("filestyle"),options=$.extend({},$.fn.filestyle.defaults,option,typeof option==="object"&&option);if(!data){$this.data("filestyle",(data=new Filestyle(this,options)));data.constructor()}if(typeof option==="string"){get=data[option](value)}}});if(typeof get!==undefined){return get}else{return element}};$.fn.filestyle.defaults={buttonText:"Choose file",iconName:"glyphicon glyphicon-folder-open",buttonName:"btn-default",size:"nr",input:true,badge:true,icon:true,buttonBefore:false,disabled:false,placeholder:""};$.fn.filestyle.noConflict=function(){$.fn.filestyle=old;return this};$(function(){$(".filestyle").each(function(){var $this=$(this),options={input:$this.attr("data-input")==="false"?false:true,icon:$this.attr("data-icon")==="false"?false:true,buttonBefore:$this.attr("data-buttonBefore")==="true"?true:false,disabled:$this.attr("data-disabled")==="true"?true:false,size:$this.attr("data-size"),buttonText:$this.attr("data-buttonText"),buttonName:$this.attr("data-buttonName"),iconName:$this.attr("data-iconName"),badge:$this.attr("data-badge")==="false"?false:true,placeholder:$this.attr("data-placeholder")};$this.filestyle(options)})})})(window.jQuery);
        </script>
        {{--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">--}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <script src="/_app_cadastro_cliente/inc/jquery.mask.js?"></script>
        <title>Resumo do Cadastro</title>
        <meta charset="utf-8">

        <link rel="stylesheet" href="{{ asset('_app_cadastro_cliente/css/style.css') }}?{{ time() }}">
        <style>
            .dados-resumo {
                padding: 0 25px;
            }
            .dados-resumo h1 {
                margin: 25px auto;
                text-align: center;
            }
            .dados-resumo h2 {
                margin: 75px auto 25px;
            }
            .dados-resumo p {
                margin: 0;
                font-size: 25px;
            }
            .dados-resumo div.card {
                margin: 10px 0;
                padding: 15px;
                font-size: 25px;
            }
            .dados-resumo .form-check {
                margin: 20px 0;
                padding-left: 0;
                display: flex;
                align-items: center;
            }
            .dados-resumo .form-check .form-check-input {
                width: 30px;
                height: 30px;
                margin: 0;
            }
            .dados-resumo .form-check .form-check-label {
                font-size: 20px;
                margin-left: 40px;
            }
            .dados-resumo hr {
                border-color: #FFF;
            }
            .loading-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #fff;
                opacity: 0.8;
                z-index: 999;
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

            button.btlaranja{
                width: 100%;
                margin:0 auto;
                padding: 5px;
                margin-top: 20px;
                margin-bottom: 15px !important;
                display:block;
                background-color: #ff8400;
                font-weight: 800;
                color:#fff !important;
                font-size:28px;
                border:0px;
                text-transform: uppercase;
            }

            ::placeholder {
                color: black !important;
                opacity: 1;
            }
            ::-webkit-input-placeholder { /* Chrome/Opera/Safari */
                color: black !important;
                font-family: 'Source Sans Pro', sans-serif; font-weight: 200;
            }
            .form-control[readonly] {
                background-color: #d2d4d7;
            }
        </style>
    </head>
    <body>

    <div class="loading-overlay">
        <div class="spin-loader"></div>
        <h2 style="color: black;">Enviando...</h2>
    </div>
        @include('common.swal')
        <div id="geral_resumo_app_cadastro_cliente">
            <div class="logocampanhag col-md-12">
                <img src="https://www.lifepet.com.br/wp-content/uploads/2018/12/LOGOTIPO_VF.png" style="margin: 10% auto 0;display: block;height: auto;width: 150px;" />
            </div>
            <div id="telas">
                {{--@include('app_cadastro_cliente.parts.resumo_cliente')--}}
                {{--@include('app_cadastro_cliente.parts.resumo_pets')--}}
                {{--@include('app_cadastro_cliente.parts.resumo_checklist')--}}
                @include('app_cadastro_cliente.parts.resumo_geral')
            </div>
        </div>
    </body>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script>

        var signaturePadCliente = new SignaturePad(document.getElementById('signature-pad-cliente'), {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
            minWidth: 1,
            maxWidth: 1,
        });

        // var signaturePadPets = new SignaturePad(document.getElementById('signature-pad-pets'), {
        //     backgroundColor: 'rgb(255, 255, 255)',
        //     penColor: 'rgb(0, 0, 0)',
        //     minWidth: 1,
        //     maxWidth: 5,
        // });
        //
        //
        // var signaturePadChecklist = new SignaturePad(document.getElementById('signature-pad-checklist'), {
        //     backgroundColor: 'rgb(255, 255, 255)',
        //     penColor: 'rgb(0, 0, 0)',
        //     minWidth: 1,
        //     maxWidth: 5,
        // });

        // Ações
        function signatureSave(signature, triggerButton) {
            if (signature.isEmpty()) {
                swal('A assinatura é obrigatória!', 'Por favor, confirme-a antes de salvar.', 'warning');
            }
            else {
                signature.off();
                triggerButton.hide();
                triggerButton.next().show();
                triggerButton.siblings().addClass('disabled');
                triggerButton.next().removeClass('disabled', false);
                signature._canvas.classList.add('confirmado');
                // var dataURL = signature.toDataURL("image/jpeg");
                // download(dataURL, "assinatura.jpg");
            }
        }

        function signatureRemake(signature, triggerButton) {
            signature.on();
            triggerButton.hide();
            triggerButton.prev().show();
            triggerButton.siblings().prop('disabled', false);
            triggerButton.prev().prop('disabled', false);
            signature._canvas.classList.remove('confirmado');
            $('form .proposta').val('');
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
            if (signature.isEmpty() || !signature._canvas.classList.contains('confirmado')) {
                swal('A assinatura é obrigatória!', 'Por favor, confirme-a antes de salvar.', 'warning');
            } else {
                animate_tela("-=800px");
            }
        }

        function volta_tela() {
            animate_tela("+=800px");
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

            window.validate = function (container) {
                var validation = {
                    invalidFields: [],
                    isValid: true
                };
                if (!container) {
                    container = "#form-compra";
                }

                var inputs = $(container).find("input, select, textarea").toArray();
                var inputIsValid = false;
                var description = "";
                for (var i = 0; i < inputs.length; i++) {
                    if ($(inputs[i]).hasClass('disabled')) {
                        continue;
                    }

                    inputIsValid = inputs[i].checkValidity();
                    if (!inputIsValid) {
                        description = $(inputs[i]).data('description');
                        if (description && description !== "") {
                            validation.invalidFields.push({
                                "name": description,
                                "element": inputs[i]
                            });
                        }
                    }
                    validation.isValid = validation.isValid && inputIsValid;
                }

                return validation;
            };

            window.validateContext = function (context, e) {
                var validation = validate(context);

                if (!validation.isValid) {

                    validation.invalidFields.forEach(function (item, index, all) {
                        $(item.element).addClass('error').change(function () {
                            $(this).removeClass('error');
                        });
                    });

                    var message = "<ul class='swal-required'>" + validation.invalidFields.map(function (i) {
                        return "<li>" + i.name + "</li>";
                    }).join('') + "</ul>";

                    swal({
                        title: 'Oops!',
                        html: "Para finalizar, você precisa preencher todos os campos. Volte e verifique se não faltou nada no seu cadastro.<br/><br/>" + message,
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
        });
    </script>

    <script src="/_app_cadastro_cliente/inc/html2canvas.min.js?"></script>
    <script>

        function clickChecklist() {
            var allInputs = $('.containerChecklist input').length;
            var checkedInputs = $('.containerChecklist input:checked').length;
            if (allInputs == checkedInputs) {
                $('input[name="checklist"]').val(1);
            } else {
                $('input[name="checklist"]').val();
            }
        }

        function confirmarVenda(signature, e) {
            $('.loading-overlay').show();
            // console.log('wrkd');
            // return false;
            var serialize = $('#dadosresumo').serialize().replace(/[^=&]+=(&|$)/g,"").replace(/&$/,"");
            if (signature.isEmpty()) {
                $('.loading-overlay').hide();
                swal('A assinatura é obrigatória!', 'Por favor, preencha sua assinatura.', 'warning');
            } else if(!signature._canvas.classList.contains('confirmado')) {
                $('.loading-overlay').hide();
                swal('A assinatura é obrigatória!', 'Por favor, confirme a assinatura.', 'warning');
            } else {
                if ($('#checklist input').length !== $('#checklist input:checked').length) {
                    $('.loading-overlay').hide();
                    swal('O checklist é obrigatório', 'Todos os itens do tópico "3) Checklist" são obrigatórios.', 'warning');
                } else {
                    if(validateContext("#dadosresumo", e)) {
                        // html2canvas(document.querySelector("body")).then(canvas => {
                        //     var imgURL = canvas.toDataURL("image/png");
                        //     download(imgURL, "proposta-adesao.png");
                        // });
                        $.ajax({
                            url: "{{ route('app_cadastro_cliente.salvarDadosResumo') }}",
                            method: 'post',
                            data: new FormData($('#dadosresumo')[0]),
                            cache: false,
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                swal({
                                    title: 'Parabéns!',
                                    text: 'Bem vindo à Lifepet!',
                                    type: 'success',
                                    showConfirmButton: true,
                                    confirmButtonColor: '#ff8400',
                                    confirmButtonText: 'Avançar',
                                    allowOutsideClick: false
                                }).then(function (result) {
                                    $('body').addClass('pageReady');
                                    window.location.replace(data + "?" + serialize);
                                });
                            },
                        });

                    } else {
                        $('.loading-overlay').hide();
                    }
                }
            }
        }
    </script>

    <script type="text/javascript" src="{{ asset('_app_cadastro_cliente/js/scripts.js') }}?{{ time() }}"></script>
    <script type="text/javascript" src="{{ mix('js/app.js') }}?{{ time() }}"></script>

    @if(isset($error))
    <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>
    <script>
        $(document).ready(function () {
            text = "{{ $error }}";
            swal({
                title: 'Erro!',
                html: text,
                showCancelButton: true, // There won't be any cancel button
                showConfirmButton: false,
                cancelButtonText: 'Ok'
            });
        });
    </script>

    @endif
</html>