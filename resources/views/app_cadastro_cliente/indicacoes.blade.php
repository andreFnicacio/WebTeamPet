<!DOCTYPE html>
<html lang="pt-br">
<head>


    <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>
    <meta name="viewport" content="width=320, user-scalable=no, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,200i,300,300i,400,400i,600,600i,700,700i,900,900i" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
            crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="text/javascript">
        (function($){var nextId=0;var Filestyle=function(element,options){this.options=options;this.$elementFilestyle=[];this.$element=$(element)};Filestyle.prototype={clear:function(){this.$element.val("");this.$elementFilestyle.find(":text").val("");this.$elementFilestyle.find(".badge").remove()},destroy:function(){this.$element.removeAttr("style").removeData("filestyle");this.$elementFilestyle.remove()},disabled:function(value){if(value===true){if(!this.options.disabled){this.$element.attr("disabled","true");this.$elementFilestyle.find("label").attr("disabled","true");this.options.disabled=true}}else{if(value===false){if(this.options.disabled){this.$element.removeAttr("disabled");this.$elementFilestyle.find("label").removeAttr("disabled");this.options.disabled=false}}else{return this.options.disabled}}},buttonBefore:function(value){if(value===true){if(!this.options.buttonBefore){this.options.buttonBefore=true;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{if(value===false){if(this.options.buttonBefore){this.options.buttonBefore=false;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{return this.options.buttonBefore}}},icon:function(value){if(value===true){if(!this.options.icon){this.options.icon=true;this.$elementFilestyle.find("label").prepend(this.htmlIcon())}}else{if(value===false){if(this.options.icon){this.options.icon=false;this.$elementFilestyle.find(".icon-span-filestyle").remove()}}else{return this.options.icon}}},input:function(value){if(value===true){if(!this.options.input){this.options.input=true;if(this.options.buttonBefore){this.$elementFilestyle.append(this.htmlInput())}else{this.$elementFilestyle.prepend(this.htmlInput())}this.$elementFilestyle.find(".badge").remove();this.pushNameFiles();this.$elementFilestyle.find(".group-span-filestyle").addClass("input-group-btn")}}else{if(value===false){if(this.options.input){this.options.input=false;this.$elementFilestyle.find(":text").remove();var files=this.pushNameFiles();if(files.length>0&&this.options.badge){this.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}this.$elementFilestyle.find(".group-span-filestyle").removeClass("input-group-btn")}}else{return this.options.input}}},size:function(value){if(value!==undefined){var btn=this.$elementFilestyle.find("label"),input=this.$elementFilestyle.find("input");btn.removeClass("btn-lg btn-sm");input.removeClass("input-lg input-sm");if(value!="nr"){btn.addClass("btn-"+value);input.addClass("input-"+value)}}else{return this.options.size}},placeholder:function(value){if(value!==undefined){this.options.placeholder=value;this.$elementFilestyle.find("input").attr("placeholder",value)}else{return this.options.placeholder}},buttonText:function(value){if(value!==undefined){this.options.buttonText=value;this.$elementFilestyle.find("label .buttonText").html(this.options.buttonText)}else{return this.options.buttonText}},buttonName:function(value){if(value!==undefined){this.options.buttonName=value;this.$elementFilestyle.find("label").attr({"class":"btn "+this.options.buttonName})}else{return this.options.buttonName}},iconName:function(value){if(value!==undefined){this.$elementFilestyle.find(".icon-span-filestyle").attr({"class":"icon-span-filestyle "+this.options.iconName})}else{return this.options.iconName}},htmlIcon:function(){if(this.options.icon){return'<span class="icon-span-filestyle '+this.options.iconName+'"></span> '}else{return""}},htmlInput:function(){if(this.options.input){return'<input type="text" class=" '+(this.options.size=="nr"?"":"input-"+this.options.size)+'" placeholder="'+this.options.placeholder+'" disabled> '}else{return""}},pushNameFiles:function(){var content="",files=[];if(this.$element[0].files===undefined){files[0]={name:this.$element[0]&&this.$element[0].value}}else{files=this.$element[0].files}for(var i=0;i<files.length;i++){content+=files[i].name.split("\\").pop()+", "}if(content!==""){this.$elementFilestyle.find(":text").val(content.replace(/\, $/g,""))}else{this.$elementFilestyle.find(":text").val("")}return files},constructor:function(){var _self=this,html="",id=_self.$element.attr("id"),files=[],btn="",$label;if(id===""||!id){id="filestyle-"+nextId;_self.$element.attr({id:id});nextId++}btn='<span class="group-span-filestyle '+(_self.options.input?"input-group-btn":"")+'"><label for="'+id+'" class="btn '+_self.options.buttonName+" "+(_self.options.size=="nr"?"":"btn-"+_self.options.size)+'" '+(_self.options.disabled?'disabled="true"':"")+">"+_self.htmlIcon()+'<span class="buttonText">'+_self.options.buttonText+"</span></label></span>";html=_self.options.buttonBefore?btn+_self.htmlInput():_self.htmlInput()+btn;_self.$elementFilestyle=$('<div class="bootstrap-filestyle input-group">'+html+"</div>");_self.$elementFilestyle.find(".group-span-filestyle").attr("tabindex","0").keypress(function(e){if(e.keyCode===13||e.charCode===32){_self.$elementFilestyle.find("label").click();return false}});_self.$element.css({position:"absolute",clip:"rect(0px 0px 0px 0px)"}).attr("tabindex","-1").after(_self.$elementFilestyle);if(_self.options.disabled){_self.$element.attr("disabled","true")}_self.$element.change(function(){var files=_self.pushNameFiles();if(_self.options.input==false&&_self.options.badge){if(_self.$elementFilestyle.find(".badge").length==0){_self.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}else{if(files.length==0){_self.$elementFilestyle.find(".badge").remove()}else{_self.$elementFilestyle.find(".badge").html(files.length)}}}else{_self.$elementFilestyle.find(".badge").remove()}});if(window.navigator.userAgent.search(/firefox/i)>-1){_self.$elementFilestyle.find("label").click(function(){_self.$element.click();return false})}}};var old=$.fn.filestyle;$.fn.filestyle=function(option,value){var get="",element=this.each(function(){if($(this).attr("type")==="file"){var $this=$(this),data=$this.data("filestyle"),options=$.extend({},$.fn.filestyle.defaults,option,typeof option==="object"&&option);if(!data){$this.data("filestyle",(data=new Filestyle(this,options)));data.constructor()}if(typeof option==="string"){get=data[option](value)}}});if(typeof get!==undefined){return get}else{return element}};$.fn.filestyle.defaults={buttonText:"Choose file",iconName:"glyphicon glyphicon-folder-open",buttonName:"btn-default",size:"nr",input:true,badge:true,icon:true,buttonBefore:false,disabled:false,placeholder:""};$.fn.filestyle.noConflict=function(){$.fn.filestyle=old;return this};$(function(){$(".filestyle").each(function(){var $this=$(this),options={input:$this.attr("data-input")==="false"?false:true,icon:$this.attr("data-icon")==="false"?false:true,buttonBefore:$this.attr("data-buttonBefore")==="true"?true:false,disabled:$this.attr("data-disabled")==="true"?true:false,size:$this.attr("data-size"),buttonText:$this.attr("data-buttonText"),buttonName:$this.attr("data-buttonName"),iconName:$this.attr("data-iconName"),badge:$this.attr("data-badge")==="false"?false:true,placeholder:$this.attr("data-placeholder")};$this.filestyle(options)})})})(window.jQuery);
    </script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">


    <script src="/_app_cadastro_cliente/inc/jquery.mask.js?"></script>
    <title>App - 1 ano de Plano Básico Grátis</title>
    <meta charset="utf-8">

    <style>
        a:hover{text-decoration: none;}
        *:focus {
            outline: none;
        }
        .btn-foto{
            padding-top: 22px;
            padding-bottom: 0px;
        }
        .bootstrap-filestyle input {
            width: 245px !important;
        }
        html {height: 100%;}
        body {margin:0; padding:0; height:100%; font-family: 'Source Sans Pro', sans-serif; background-image:url('/_app_cadastro_cliente/images/1ano.jpg'); background-attachment: fixed !important; background-repeat: no-repeat; background-size: cover; overflow-x: hidden; background-position: top center; }
        h2{font-family: 'Source Sans Pro', sans-serif; font-weight: 800; font-size: 25px; color: #fff; text-align: left; padding-top: 0px; padding-bottom: 10px;}
        h3{font-family: 'Source Sans Pro', sans-serif; font-weight: 500; font-size: 15px; color: #fff; text-align: left; padding-top: 0px; padding-bottom: 0px;}

        input,select{font-family: 'Source Sans Pro', sans-serif;

            font-weight: 200;
            font-size: 22px;
            padding: 5px 5px 5px 0px;
            border: none;
            border-bottom-width: medium;
            border-bottom-style: none;
            border-bottom-color: currentcolor;
            border-bottom: 1px solid #fff;
            color: #b3d1d9;
            background-color: transparent;
            margin-bottom: 20px;
            width: 100%;
        }


        ::-webkit-input-placeholder { /* Chrome/Opera/Safari */
            color: #fff !important;
            font-family: 'Source Sans Pro', sans-serif; font-weight: 200;
        }
        ::-moz-placeholder { /* Firefox 19+ */
            color: #fff !important;
            font-family: 'Source Sans Pro', sans-serif; font-weight: 200;
        }
        :-ms-input-placeholder { /* IE 10+ */
            color: #fff !important;
            font-family: 'Source Sans Pro', sans-serif; font-weight: 200;
        }
        :-moz-placeholder { /* Firefox 18- */
            color: #fff !important;
            font-family: 'Source Sans Pro', sans-serif; font-weight: 200;
        }
        * { /* To receive click events on iOS */ cursor: pointer; }
        #geral_cadastro_app_cadastro_cliente, #telas, .tela {min-height:100%; height:auto;}
        * html #geral_cadastro_app_cadastro_cliente  {height:100%;}
        #telas, .tela  {min-height:100vh;  height:auto;}
        #geral_cadastro_app_cadastro_cliente{max-width: 800px; width: 100%; margin: 0 auto; position: relative; overflow: hidden !important; }
        #telas{width: 2800px; margin: 0 auto; height: 100vh !important; position: relative; }
        .tela{width: 800px; float: left;}
        a.btlaranja, button[type=submit].btlaranja{width: 100%; margin:0 auto; padding: 5px;  margin-bottom: 15px !important; display:block; background-color: #ff8400; font-weight: 800; border-radius: 150px; color:#fff !important; font-size:18px; border:0px; text-transform: uppercase;}
        a.btbranco{width: 100%; margin:0 auto; padding: 5px; display:block; background-color: #fff; font-weight: 800; border-radius: 150px; color:#ff8400 !important; font-size:18px; border:0px; text-transform: uppercase; margin-bottom:8px; }
        .conheca{ width: 800px; padding-top: 20px; text-align: center; height: auto;}
        #bemvindo{}
        .logocampanhag {margin-bottom: 15px; height: 20vh; min-height: 130px !important; text-align: center;  }
        .regulamento {margin-bottom: 15px; height: 50vh !important; text-align: center; overflow-y:scroll; background-color:rgba(255,255,255, 0.6); padding: 10px 25px 10px 10px; color:#000;  }
        .queroagora{width: 290px; bottom: 20px; }
        .avancar{margin-top: 20px; margin-bottom: 30px;}
        .avanca{}
        .volta{ margin-left: 5%;}
        a{cursor: pointer !important;}

        input.error, select.error, textarea.error {
            border-color: #ff8400 !important;
        }
        input.error::placeholder, select.error::placeholder, textarea.error::placeholder {
            color: #ff8400 !important;
            font-weight: bold;
        }

        ul.swal-required {
            text-align: left;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        ul.swal-required li {
            padding-left: 1em;
            text-indent: -.7em;
        }

        ul.swal-required li::before {
            content: "•";
            color: rgb(255, 132, 0);
            padding-right: 10px;
        }

        ul#lista_pets li {
            color: white;
            background: #ff8400;
            padding-left: 10px;
            padding-top: 3px;
            padding-bottom: 3px;
            border-radius: 20px;
            margin-bottom: 5px;
        }
        ul#lista_pets {
            list-style: none;
            text-align: left;
            font-size: 20px;
            padding-left: 0;
            font-weight: bolder;
        }
        #pets_container {
            position: absolute;
            visibility: hidden;
        }

        #lista_pets li span.pet {
            display: inline-block;
            width: 37px;
            height: 35px;
            vertical-align: middle;
            margin-right: 15px;
            position: relative;
            border-radius: 100px;
            background: white;
            border: 2px solid #f1b31e;
            padding: 6px;
        }
        #lista_pets li span.pet::after {
            background-position: center center;
            background-size: contain;
            display: block;
            position: absolute;
            top: 5%;
            content: '';
            width: 90%;
            height: 90%;
            background-repeat: no-repeat;
            box-sizing: border-box;
            left: 5%;
        }

        #lista_pets li span.pet.cachorro::after {
            background-image: url({{ url("/assets/images/dog.png") }});
        }
        #lista_pets li span.pet.gato::after {
            background-image: url({{ url("/assets/images/cat.png") }});
        }
    </style>


</head>
<body>


<div id="geral_cadastro_app_cadastro_cliente">
    <div class="logocampanhag col-md-12">
        <img src="https://www.lifepet.com.br/wp-content/uploads/2018/04/landing_pageLOGO-1.png" height="100px" style="margin-top: 20px; " />
    </div>

    <div id="telas">
        <div class="tela" id="indique">
            <form name="indique" class="indique" id="indique" method="POST" action="{{ route('app_cadastro_cliente.indicar') }}">
                {{ csrf_field() }}
                <input type="hidden" name="idCliente" value="{{$idCliente}}">
                <div class="conheca col-md-12">
                    <h2>Indique 3 amigos:</h2>
                    <p>O preenchimento é obrigatório.</p>
                    <h3 style="padding: 20px 0px 0px 0px;">Indique o amigo 1:</h3>
                    <input name="indicacao[0][nome_amigo]" type="text" placeholder="Nome" required>
                    <input name="indicacao[0][celular_amigo]" type="text"  placeholder="Celular" required>
                    <input name="indicacao[0][email_amigo]" type="text"  placeholder="E-mail" required>
                    <h3 style="padding: 20px 0px 0px 0px;">Indique o amigo 2:</h3>
                    <input name="indicacao[1][nome_amigo]" type="text" placeholder="Nome" required>
                    <input name="indicacao[1][celular_amigo]" type="text"  placeholder="Celular" required>
                    <input name="indicacao[1][email_amigo]" type="text"  placeholder="E-mail" required>
                    <h3 style="padding: 20px 0px 0px 0px;">Indique o amigo 3:</h3>
                    <input name="indicacao[2][nome_amigo]" type="text" placeholder="Nome" required>
                    <input name="indicacao[2][celular_amigo]" type="text"  placeholder="Celular" required>
                    <input name="indicacao[2][email_amigo]" type="text"  placeholder="E-mail" required>

                    <div class="avancar col-md-12">
                        <button type="submit" class="btlaranja final">Finalizar Adesão</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="tela" id="final">

            <div class="conheca col-md-12">

                <h2>Parabéns!</h2>

                <h3 style="padding: 30px 30px 0px 30px;">Você completou sua adesão! Agora, iremos analisar seus dados. Em breve você receberá um e-mail de confirmação. Caso exista algum dado divergente, nosso atendimento irá te auxiliar!</h3>



                <h3 style="padding: 30px;"></h3>

                <div class="avancar col-md-12">
                    <a class="btbranco " href="https://app.lifepet.com.br/cliente/login">Voltar para a Home</a>
                </div>

            </div>

        </div>

    </div>

</div>





</div>
<script type="text/javascript">


    $(document).ready(function() {
        window.validate = function(container) {
            var validation = {
                invalidFields: [],
                isValid: true
            };
            if(!container) {
                container = "#indique";
            }

            var inputs = $(container).find("input, select, textarea").toArray();
            var inputIsValid = false;
            var description = "";
            for(var i = 0; i < inputs.length; i++) {
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
            }

            return true;
        };

        $('#pagar').click(function(e) {
            return validateContext("#form-compra", e);
        });
    });

    $(function() {
        $('.date').mask('00/00/0000');
        $('.datec').mask('00/0000');
        $('.time').mask('00:00:00');
        $('.date_time').mask('00/00/0000 00:00:00');
        $('.cep').mask('00000-000');
        $('.phone').mask('0000-0000');
        $('.phone_with_ddd').mask('(00) 0000-0000');
        $('.phone').mask('(00) 0000-0000');
        $('.mixed').mask('AAA 000-S0S');
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

    $("#indique").submit(function(e) {
        var context = $(this).data('context');
        var validated = window.validateContext(context, e);
            if(validated) {
            swal({
                title: 'Enviando Indicações',
                text: 'Aguarde a confirmação',
                showCancelButton: false, // There won't be any cancel button
                showConfirmButton: false,
            });
        }

    });
    $("a.euaceito").click(function() {
        swal({
            title: 'Bem-vindo!',
            html: "Para iniciarmos, você precisará ter em mãos:<br/><br/>- Seu RG ou CNH;<br/>- Um comprovante de residência;<br/>- A carteirinha de vacinação de cada pet (caso exista).<br/><br/>Se você já for cliente Lifepet, por enquanto não poderá aderir esse plano.<br/><br/><b>Importante: Promoção válida somente para clientes da Grande Vitória</b>",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff8400',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Cadastrar',
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
</script>
</body>
</html>

