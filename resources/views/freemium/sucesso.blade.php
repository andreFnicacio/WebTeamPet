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


    <script src="/_freemium/inc/jquery.mask.js?"></script>
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
        body {margin:0; padding:0; height:100%; font-family: 'Source Sans Pro', sans-serif; background-image:url('/_freemium/images/1ano.jpg'); background-attachment: fixed !important; background-repeat: no-repeat; background-size: cover; overflow-x: hidden; background-position: top center; }
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
        #geral_cadastro_freemium, #telas, .tela {min-height:100%; height:auto;}
        * html #geral_cadastro_freemium  {height:100%;}
        #telas, .tela  {min-height:100vh;  height:auto;}
        #geral_cadastro_freemium{max-width: 320px; width: 100%; margin: 0 auto; position: relative; overflow: hidden !important; }
        #telas{width: 2320px; margin: 0 auto; height: 100vh !important; position: relative; }
        .tela{width: 320px; float: left;}
        a.btlaranja, button[type=submit].btlaranja{width: 100%; margin:0 auto; padding: 5px;  margin-bottom: 15px !important; display:block; background-color: #ff8400; font-weight: 800; border-radius: 150px; color:#fff !important; font-size:18px; border:0px; text-transform: uppercase;}
        a.btbranco{width: 100%; margin:0 auto; padding: 5px; display:block; background-color: #fff; font-weight: 800; border-radius: 150px; color:#ff8400 !important; font-size:18px; border:0px; text-transform: uppercase; margin-bottom:8px; }
        .conheca{ width: 320px; padding-top: 20px; text-align: center; height: auto;}
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


<div id="geral_cadastro_freemium">
    <div class="logocampanhag col-md-12">
        <img src="https://www.lifepet.com.br/wp-content/uploads/2018/04/landing_pageLOGO-1.png" height="100px" style="margin-top: 20px; " />
    </div>

    <div id="telas">
        <div class="tela" id="final">

            <div class="conheca col-md-12">

                <h2 class="text-center">Bem-vindo à Lifepet!</h2>

                <h3 style="padding: 30px 30px 0px 30px;">Parabéns! Você completou a primeira parte do seu cadastro.
                    <br><br> Agora falta pouco para você proteger seu pet. Em breve você receberá um e-mail em que serão solicitados os dados do seu pet e algumas informações complementares sobre você.
                    <br><br>Você terá <strong>48h</strong> para responder! Agradecemos à sua adesão
                    <br><br>Atenciosamente,
                    <br>Equipe Lifepet</h3>

                <h3 style="padding: 30px;"></h3>

                <div class="avancar col-md-12">
                    <a class="btbranco " href="https://www.lifepet.com.br">Visite o nosso site</a>
                </div>

            </div>

        </div>

    </div>

</div>





</div>
<script type="text/javascript">


</script>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-85146807-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-85146807-1');
</script>




<!-- Global site tag (gtag.js) - Google AdWords: 869040570 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-869040570"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-869040570');
</script>






<!-- Event snippet for Cadastrou no Fácil conversion page -->
<script>
  gtag('event', 'conversion', {'send_to': 'AW-869040570/D5RqCMe034EBELqDsp4D'});
</script>


<!-- Facebook Pixel Code -->
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '241129993069453'); // Insert your pixel ID here.
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=241129993069453&ev=PageView&noscript=1"
    /></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
</body>
</html>

