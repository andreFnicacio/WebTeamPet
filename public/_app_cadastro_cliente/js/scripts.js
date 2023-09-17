(function($){var nextId=0;var Filestyle=function(element,options){this.options=options;this.$elementFilestyle=[];this.$element=$(element)};Filestyle.prototype={clear:function(){this.$element.val("");this.$elementFilestyle.find(":text").val("");this.$elementFilestyle.find(".badge").remove()},destroy:function(){this.$element.removeAttr("style").removeData("filestyle");this.$elementFilestyle.remove()},disabled:function(value){if(value===true){if(!this.options.disabled){this.$element.attr("disabled","true");this.$elementFilestyle.find("label").attr("disabled","true");this.options.disabled=true}}else{if(value===false){if(this.options.disabled){this.$element.removeAttr("disabled");this.$elementFilestyle.find("label").removeAttr("disabled");this.options.disabled=false}}else{return this.options.disabled}}},buttonBefore:function(value){if(value===true){if(!this.options.buttonBefore){this.options.buttonBefore=true;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{if(value===false){if(this.options.buttonBefore){this.options.buttonBefore=false;if(this.options.input){this.$elementFilestyle.remove();this.constructor();this.pushNameFiles()}}}else{return this.options.buttonBefore}}},icon:function(value){if(value===true){if(!this.options.icon){this.options.icon=true;this.$elementFilestyle.find("label").prepend(this.htmlIcon())}}else{if(value===false){if(this.options.icon){this.options.icon=false;this.$elementFilestyle.find(".icon-span-filestyle").remove()}}else{return this.options.icon}}},input:function(value){if(value===true){if(!this.options.input){this.options.input=true;if(this.options.buttonBefore){this.$elementFilestyle.append(this.htmlInput())}else{this.$elementFilestyle.prepend(this.htmlInput())}this.$elementFilestyle.find(".badge").remove();this.pushNameFiles();this.$elementFilestyle.find(".group-span-filestyle").addClass("input-group-btn")}}else{if(value===false){if(this.options.input){this.options.input=false;this.$elementFilestyle.find(":text").remove();var files=this.pushNameFiles();if(files.length>0&&this.options.badge){this.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}this.$elementFilestyle.find(".group-span-filestyle").removeClass("input-group-btn")}}else{return this.options.input}}},size:function(value){if(value!==undefined){var btn=this.$elementFilestyle.find("label"),input=this.$elementFilestyle.find("input");btn.removeClass("btn-lg btn-sm");input.removeClass("input-lg input-sm");if(value!="nr"){btn.addClass("btn-"+value);input.addClass("input-"+value)}}else{return this.options.size}},placeholder:function(value){if(value!==undefined){this.options.placeholder=value;this.$elementFilestyle.find("input").attr("placeholder",value)}else{return this.options.placeholder}},buttonText:function(value){if(value!==undefined){this.options.buttonText=value;this.$elementFilestyle.find("label .buttonText").html(this.options.buttonText)}else{return this.options.buttonText}},buttonName:function(value){if(value!==undefined){this.options.buttonName=value;this.$elementFilestyle.find("label").attr({"class":"btn "+this.options.buttonName})}else{return this.options.buttonName}},iconName:function(value){if(value!==undefined){this.$elementFilestyle.find(".icon-span-filestyle").attr({"class":"icon-span-filestyle "+this.options.iconName})}else{return this.options.iconName}},htmlIcon:function(){if(this.options.icon){return'<span class="icon-span-filestyle '+this.options.iconName+'"></span> '}else{return""}},htmlInput:function(){if(this.options.input){return'<input type="text" class=" '+(this.options.size=="nr"?"":"input-"+this.options.size)+'" placeholder="'+this.options.placeholder+'" disabled> '}else{return""}},pushNameFiles:function(){var content="",files=[];if(this.$element[0].files===undefined){files[0]={name:this.$element[0]&&this.$element[0].value}}else{files=this.$element[0].files}for(var i=0;i<files.length;i++){content+=files[i].name.split("\\").pop()+", "}if(content!==""){this.$elementFilestyle.find(":text").val(content.replace(/\, $/g,""))}else{this.$elementFilestyle.find(":text").val("")}return files},constructor:function(){var _self=this,html="",id=_self.$element.attr("id"),files=[],btn="",$label;if(id===""||!id){id="filestyle-"+nextId;_self.$element.attr({id:id});nextId++}btn='<span class="group-span-filestyle '+(_self.options.input?"input-group-btn":"")+'"><label for="'+id+'" class="btn '+_self.options.buttonName+" "+(_self.options.size=="nr"?"":"btn-"+_self.options.size)+'" '+(_self.options.disabled?'disabled="true"':"")+">"+_self.htmlIcon()+'<span class="buttonText">'+_self.options.buttonText+"</span></label></span>";html=_self.options.buttonBefore?btn+_self.htmlInput():_self.htmlInput()+btn;_self.$elementFilestyle=$('<div class="bootstrap-filestyle input-group">'+html+"</div>");_self.$elementFilestyle.find(".group-span-filestyle").attr("tabindex","0").keypress(function(e){if(e.keyCode===13||e.charCode===32){_self.$elementFilestyle.find("label").click();return false}});_self.$element.css({position:"absolute",clip:"rect(0px 0px 0px 0px)"}).attr("tabindex","-1").after(_self.$elementFilestyle);if(_self.options.disabled){_self.$element.attr("disabled","true")}_self.$element.change(function(){var files=_self.pushNameFiles();if(_self.options.input==false&&_self.options.badge){if(_self.$elementFilestyle.find(".badge").length==0){_self.$elementFilestyle.find("label").append(' <span class="badge">'+files.length+"</span>")}else{if(files.length==0){_self.$elementFilestyle.find(".badge").remove()}else{_self.$elementFilestyle.find(".badge").html(files.length)}}}else{_self.$elementFilestyle.find(".badge").remove()}});if(window.navigator.userAgent.search(/firefox/i)>-1){_self.$elementFilestyle.find("label").click(function(){_self.$element.click();return false})}}};var old=$.fn.filestyle;$.fn.filestyle=function(option,value){var get="",element=this.each(function(){if($(this).attr("type")==="file"){var $this=$(this),data=$this.data("filestyle"),options=$.extend({},$.fn.filestyle.defaults,option,typeof option==="object"&&option);if(!data){$this.data("filestyle",(data=new Filestyle(this,options)));data.constructor()}if(typeof option==="string"){get=data[option](value)}}});if(typeof get!==undefined){return get}else{return element}};$.fn.filestyle.defaults={
    buttonText:"Carregar Foto",iconName:"fa fa-camera",buttonName:"btn-light",size:"lg",input:true,badge:false,icon:true,buttonBefore:false,disabled:false,placeholder:""};$.fn.filestyle.noConflict=function(){$.fn.filestyle=old;return this};$(function(){$(".filestyle").each(function(){var $this=$(this),options={input:$this.attr("data-input")==="false"?false:true,icon:$this.attr("data-icon")==="false"?false:true,buttonBefore:$this.attr("data-buttonBefore")==="true"?true:false,disabled:$this.attr("data-disabled")==="true"?true:false,size:$this.attr("data-size"),buttonText:$this.attr("data-buttonText"),buttonName:$this.attr("data-buttonName"),iconName:$this.attr("data-iconName"),badge:$this.attr("data-badge")==="false"?false:true,placeholder:$this.attr("data-placeholder")};$this.filestyle(options)})})})(window.jQuery);

function ValidaCPF(strCPF) {
    var Soma;
    var Resto;
    Soma = 0;
    strCPF = strCPF.replace(/\D/g, '');
    if (strCPF == "00000000000") return false;

    for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11))  Resto = 0;
    if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;

    Soma = 0;
    for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11))  Resto = 0;
    if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;
    return true;
}

$(document).ready(function () {

    window.onbeforeunload = function() {
        if($('body').hasClass('pageReady')) {
            return;
        }
        return  "Tem certeza que deseja sair desta página?";
    }

    var connectionOnline = true;

    setInterval(function () {
        if (!navigator.onLine && connectionOnline) {
            connectionOnline = false;
            swal({
                title: 'Offline!',
                text: 'Sem acesso a internet no momento. Conecte-se e tente novamente.',
                type:'error',
                showConfirmButton: true ,
                allowOutsideClick: false
            }).then(function () {
                connectionOnline = true;
            });
        }
        // connectionOnline = true;
        // else if (navigator.onLine) {
        //     connectionOnline = true;
        //     // swal.close();
        // }
    }, 500);

    $( document ).ajaxStart(function() {
        $('.loading-overlay').show();
    });

    $( document ).ajaxStop(function() {
        $('.loading-overlay').hide();
    });

    $(".mdl-textfield__input").blur(function (){
        if( !this.value ){
            $(this).prop('required', true);
            $(this).parent().addClass('is-invalid');
        }
    });
    
    $('.cep.address-search-trigger-blur').blur(function () {
        $('#bairro').removeClass('error');
        $('#cidade').removeClass('error');
        $('#localidade').removeClass('error');
        $('#logradouro').removeClass('error');
        $('#uf').removeClass('error');
    });

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
                if($(item.element).parent().hasClass('mdl-textfield')) {
                    $(item.element).parent().addClass('is-invalid');
                }
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

    setTimeout(function () {
        $(".mdl-textfield").removeClass('is-invalid');
        $('*[data-required=true]').attr('required', true);
    }, 1000);

    $('.datetoday').click(function () {
        // $(this).val() = $(this).val() || ;
        if (!$(this).val()) {
            $(this).parent().addClass('is-dirty');
            $(this).val($(this).data('today'));
        }
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
});