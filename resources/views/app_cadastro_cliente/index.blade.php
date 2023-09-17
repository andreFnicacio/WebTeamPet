<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>
        {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">--}}

        <link href="{{ url('/') }}/assets/global/plugins/bootstrap4/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="{{ url('/') }}/assets/global/plugins/bootstrap4/js/bootstrap.min.js"></script>

        {{--<script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>--}}
        {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>--}}

        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,200i,300,300i,400,400i,600,600i,700,700i,900,900i" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.3.1.js"
                integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
                crossorigin="anonymous"></script>
        {{--<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>--}}
        {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">--}}
        <script type="text/javascript">
            (function($){var nextId=0;var Filestyle=function(element,options){this.options=options;this.$elementFilestyle=[];this.$element=$(element)};Filestyle.prototype={clear:function(){this.$element.val("");this.$elementFilestyle.find(":text").val("");this.$elementFilestyle.find(".badge").remove()},destroy:function(){this.$element.removeAttr("style").removeData("filestyle");this.$elementFilestyle.remove()},disabled:function(value){if(value===true){if(!this.options.disabled){this.$element.attr("disabled","true");this.$elementFilestyle.find("label").attr("disabled","true");this.options.disabled=true}}else{if(value===false){if(this.options.disabled){this.$element.removeAttr("disabled");this.$elementFilestyle.find("label").removeAttr("disabled");this.options.disabled=false}}else{return this.options.disabled}}},buttonBefore:function(value){if(value===true){if(!this.options.buttonBefore){this.options.buttonBefore=true;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{if(value===false){if(this.options.buttonBefore){this.options.buttonBefore=false;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{return this.options.buttonBefore}}},icon:function(value){if(value===true){if(!this.options.icon){this.options.icon=true;this.$elementFilestyle.find("label").prepend(this.htmlIcon())}}else{if(value===false){if(this.options.icon){this.options.icon=false;this.$elementFilestyle.find(".icon-span-filestyle").remove()}}else{return this.options.icon}}},input:function(value){if(value===true){if(!this.options.input){this.options.input=true;if(this.options.buttonBefore){this.$elementFilestyle.append(this.htmlInput())}else{this.$elementFilestyle.prepend(this.htmlInput())}this.$elementFilestyle.find(".badge").remove();this.pushNameFiles();this.$elementFilestyle.find(".group-span-filestyle").addClass("input-group-btn")}}else{if(value===false){if(this.options.input){this.options.input=false;this.$elementFilestyle.find(":text").remove();var files=this.pushNameFiles();if(files.length>0&&this.options.badge){this.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}this.$elementFilestyle.find(".group-span-filestyle").removeClass("input-group-btn")}}else{return this.options.input}}},size:function(value){if(value!==undefined){var btn=this.$elementFilestyle.find("label"),input=this.$elementFilestyle.find("input");btn.removeClass("btn-lg btn-sm");input.removeClass("input-lg input-sm");if(value!="nr"){btn.addClass("btn-"+value);input.addClass("input-"+value)}}else{return this.options.size}},placeholder:function(value){if(value!==undefined){this.options.placeholder=value;this.$elementFilestyle.find("input").attr("placeholder",value)}else{return this.options.placeholder}},buttonText:function(value){if(value!==undefined){this.options.buttonText=value;this.$elementFilestyle.find("label .buttonText").html(this.options.buttonText)}else{return this.options.buttonText}},buttonName:function(value){if(value!==undefined){this.options.buttonName=value;this.$elementFilestyle.find("label").attr({"class":"btn "+this.options.buttonName})}else{return this.options.buttonName}},iconName:function(value){if(value!==undefined){this.$elementFilestyle.find(".icon-span-filestyle").attr({"class":"icon-span-filestyle "+this.options.iconName})}else{return this.options.iconName}},htmlIcon:function(){if(this.options.icon){return'<span class="icon-span-filestyle '+this.options.iconName+'"></span> '}else{return""}},htmlInput:function(){if(this.options.input){return'<input type="text" class=" '+(this.options.size=="nr"?"":"input-"+this.options.size)+'" placeholder="'+this.options.placeholder+'" disabled> '}else{return""}},pushNameFiles:function(){var content="",files=[];if(this.$element[0].files===undefined){files[0]={name:this.$element[0]&&this.$element[0].value}}else{files=this.$element[0].files}for(var i=0;i<files.length;i++){content+=files[i].name.split("\\").pop()+", "}if(content!==""){this.$elementFilestyle.find(":text").val(content.replace(/\, $/g,""))}else{this.$elementFilestyle.find(":text").val("")}return files},constructor:function(){var _self=this,html="",id=_self.$element.attr("id"),files=[],btn="",$label;if(id===""||!id){id="filestyle-"+nextId;_self.$element.attr({id:id});nextId++}btn='<span class="group-span-filestyle '+(_self.options.input?"input-group-btn":"")+'"><label for="'+id+'" class="btn '+_self.options.buttonName+" "+(_self.options.size=="nr"?"":"btn-"+_self.options.size)+'" '+(_self.options.disabled?'disabled="true"':"")+">"+_self.htmlIcon()+'<span class="buttonText">'+_self.options.buttonText+"</span></label></span>";html=_self.options.buttonBefore?btn+_self.htmlInput():_self.htmlInput()+btn;_self.$elementFilestyle=$('<div class="bootstrap-filestyle input-group">'+html+"</div>");_self.$elementFilestyle.find(".group-span-filestyle").attr("tabindex","0").keypress(function(e){if(e.keyCode===13||e.charCode===32){_self.$elementFilestyle.find("label").click();return false}});_self.$element.css({position:"absolute",clip:"rect(0px 0px 0px 0px)"}).attr("tabindex","-1").after(_self.$elementFilestyle);if(_self.options.disabled){_self.$element.attr("disabled","true")}_self.$element.change(function(){var files=_self.pushNameFiles();if(_self.options.input==false&&_self.options.badge){if(_self.$elementFilestyle.find(".badge").length==0){_self.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}else{if(files.length==0){_self.$elementFilestyle.find(".badge").remove()}else{_self.$elementFilestyle.find(".badge").html(files.length)}}}else{_self.$elementFilestyle.find(".badge").remove()}});if(window.navigator.userAgent.search(/firefox/i)>-1){_self.$elementFilestyle.find("label").click(function(){_self.$element.click();return false})}}};var old=$.fn.filestyle;$.fn.filestyle=function(option,value){var get="",element=this.each(function(){if($(this).attr("type")==="file"){var $this=$(this),data=$this.data("filestyle"),options=$.extend({},$.fn.filestyle.defaults,option,typeof option==="object"&&option);if(!data){$this.data("filestyle",(data=new Filestyle(this,options)));data.constructor()}if(typeof option==="string"){get=data[option](value)}}});if(typeof get!==undefined){return get}else{return element}};$.fn.filestyle.defaults={buttonText:"Choose file",iconName:"glyphicon glyphicon-folder-open",buttonName:"btn-default",size:"nr",input:true,badge:true,icon:true,buttonBefore:false,disabled:false,placeholder:""};$.fn.filestyle.noConflict=function(){$.fn.filestyle=old;return this};$(function(){$(".filestyle").each(function(){var $this=$(this),options={input:$this.attr("data-input")==="false"?false:true,icon:$this.attr("data-icon")==="false"?false:true,buttonBefore:$this.attr("data-buttonBefore")==="true"?true:false,disabled:$this.attr("data-disabled")==="true"?true:false,size:$this.attr("data-size"),buttonText:$this.attr("data-buttonText"),buttonName:$this.attr("data-buttonName"),iconName:$this.attr("data-iconName"),badge:$this.attr("data-badge")==="false"?false:true,placeholder:$this.attr("data-placeholder")};$this.filestyle(options)})})})(window.jQuery);
        </script>

        {{--WEB APP META--}}
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="mobile-web-app-capable" content="yes">
        {{--<link rel="icon" sizes="192x192" href="https://www.lifepet.com.br/wp-content/uploads/2018/12/LOGOTIPO_VF.png">--}}

        <link rel="icon" sizes="256x256" href="{{ asset('_app_cadastro_cliente/proposta/img/logo-azul.jpg') }}" />
        <link rel="icon" sizes="128x128" href="{{ asset('_app_cadastro_cliente/proposta/img/logo-azul.jpg') }}" />
        <link rel="shortcut icon"  href="{{ asset('_app_cadastro_cliente/proposta/img/logo-azul.jpg') }}" />
        <meta name="apple-mobile-web-app-status-bar-style" content="blue" />

        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
        <title>Cadastrar Cliente</title>
        <meta charset="utf-8">

        <style>
            html {height: 100%;}
            body {margin:0; padding:0; height:100%; background-color:#005daa; font-family: 'Source Sans Pro', sans-serif; background-image:url('/_app_cadastro_cliente/images/fundoazul2.jpg'); background-size: cover; overflow-x: hidden; background-position: bottom center; }
            #geral_cadastro_app_cadastro_cliente, #telas, .tela {min-height:100%; height:auto;}
            * html #geral_cadastro_app_cadastro_cliente  {height:100%;}
            #telas, .tela  {min-height:100vh;  height:auto;}
            #geral_cadastro_app_cadastro_cliente{max-width: 800px; width: 100%; margin: 0 auto; position: relative; overflow: hidden !important; }
            #telas{width: 2800px; margin: 0 auto; position: relative; }
            .tela{width: 800px; float: left;}
            a.btlaranja{
                width: 100%;
                height: 150px;
                background-color: #ff8400;
                font-weight: 800;
                color: #fff !important;
                font-size: 48px;
                line-height: 140px;
                text-transform: uppercase;
                position: fixed;
                bottom: 0px;
                text-align: center;
                letter-spacing: 2px;
            }
            a.btbranco{width: 100%; margin:0 auto; padding: 5px; display:block; background-color: #fff; font-weight: 800; border-radius: 150px; color:#ff8400 !important; font-size:18px; border:0px; text-transform: uppercase; margin-bottom:8px; }
            .conheca{ width: 800px; padding-top: 20px; text-align: center; height: 68vh; }
            .logocampanhag {margin-bottom: 15px; height: 30vh;  }
            .queroagora{
                position: fixed;
                width: 720px;
                bottom: 20px;
                left: 50%;
                margin-left: -360px;
            }
        </style>
    </head>
    <body class="pageReady">
    @include('common.swal')
        <div id="geral_cadastro_app_cadastro_cliente">
            <div id="telas">
                <div class="tela" id="bemvindo">
                    <div class="logocampanhag col-md-12">
                        <img src="https://www.lifepet.com.br/wp-content/uploads/2018/12/LOGOTIPO_VF.png" style="margin: 10% auto 50px;display: block;height: auto;width: 450px;" />
                        <h2 class="text-center text-white" style="margin: 0">Bem vindo</h2>
                        <h1 class="text-center text-white">{{ \Illuminate\Support\Facades\Auth::user()->name }}.</h1>
                    </div>
                    <a class="btlaranja" href="{{ route('app_cadastro_cliente.cadastro') }}">CADASTRAR</a>
                </div>
            </div>
        </div>
    </body>
    <script>
        // $( ".btbranco" ).click(function() {
        //     swal({
        //         title: '',
        //         width: 600,
        //         type: '',
        //         html:
        //             '<iframe width="100%" height="315" src="https://www.youtube.com/embed/tiY_qUpCx5U" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
        //         showCloseButton: true,
        //         showCancelButton:false,
        //         showConfirmButton:false
        //     })
        // });
    </script>

    <script src="/_app_cadastro_cliente/inc/jquery.mask.js?"></script>
    <script type="text/javascript" src="{{ asset('_app_cadastro_cliente/js/scripts.js') }}?{{ time() }}"></script>

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

