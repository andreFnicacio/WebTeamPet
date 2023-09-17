<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" required>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Plano FREE</title>

    <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link href="{{ url('/') }}/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{{ url('/') }}/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
	<link href="{{ url('/') }}/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />

	<link rel="stylesheet" id="saasland-editor-fonts-css" href="https://fonts.googleapis.com/css?family=Poppins%3A300%2C400%2C500%2C600%2C700%2C900&amp;subset" type="text/css" media="all">

    <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
			crossorigin="anonymous"></script>
			
	<style>
		* {
			font-family: 'Poppins' !important;
		}
		body {
			background-image: linear-gradient(90deg, #00a5f0 20%, #00d7d7 90%);
			color: white;
		}
		input, label, select {
			color: white !important;
			border-color: white !important;
			height: initial !important;
		}
		select option {
			color: black !important;
		}
		.md-radio label>.box {
			border-color: white !important;
		}
		.md-radio label>.check {
			background: #EEE;
		}
		.modal {
			color: black;
		}
	</style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6 offset-md-3">
                <img class="d-block my-5 mx-auto " src="https://www.lifepet.com.br/wp-content/uploads/2019/08/logob2x.png" srcset="https://www.lifepet.com.br/wp-content/uploads/2019/08/logob2x.png 2x" alt="Lifepet Brasil">
				<form action="#" method="POST" role="form" id="form_adesao" enctype="multipart/form-data" {{ $cliente ? 'novalidate' : '' }}>
					<div class="form-body">
						{{ csrf_field() }}

						<h4>
							Olá novamente, {{ $cliente ? $cliente->nome_cliente : $preCadastro->nome }}!
							<br>
							<br>
							Para finalizar sua adesão é rápido, simples e seguro: basta preencher os campos abaixo e aguardar nossa aprovação.
						</h4>

						<h2 class="text-center mt-5">Dados do Cliente</h2>
						<div id="dados_cliente" class="mt-3">
							<input type="hidden" name="cliente[ativo]" value="1" required>
							<input type="hidden" name="id_cliente" value="{{ $cliente ? $cliente->id : '' }}" required>
							<input type="hidden" name="id_pre_cadastro" value="{{ $preCadastro ? $preCadastro->id : '' }}" required>
							<div id="dados_pre_cadastro">
								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[cpf]" type="text" class="form-control cpf" id="cliente_cpf" value="{{ $cliente ? $cliente->cpf : $preCadastro->cpf }}" readonly required>
									<label for="cliente_cpf">CPF</label>
								</div>
								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[nome_cliente]" type="text" class="form-control" id="cliente_nome" value="{{ ucwords(mb_strtolower($cliente ? $cliente->nome_cliente : $preCadastro->nome)) }}" readonly required>
									<label for="cliente_nome">Nome</label>
								</div>
								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[email]" type="email" class="form-control" id="cliente_email" value="{{ $cliente ? $cliente->email : $preCadastro->email }}" readonly required>
									<label for="cliente_email">Email</label>
									<span class="help-block font-red visible">Este email será utilizado para login futuramente :)</span>
								</div>
								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[celular]" type="tel" class="form-control cel" id="cliente_celular" value="{{ $cliente ? $cliente->celular : $preCadastro->celular }}" readonly required>
									<label for="cliente_celular">Celular</label>
								</div>
								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[data_nascimento]" type="text" class="form-control data" id="cliente_data_nascimento" value="{{ $cliente ? $cliente->data_nascimento->format('d/m/Y') : $preCadastro->data_nascimento->format('d/m/Y') }}" readonly required>
									<label for="cliente_data_nascimento">Data de Nascimento</label>
								</div>
							</div>

							
							<div id="dados_cliente_cadastrado"  style="{{ $cliente ? 'display: none;' : '' }}">
								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[rg]" type="text" class="form-control" id="cliente_rg" required>
									<label for="cliente_rg">RG</label>
									<span class="help-block font-white visible">Somente números. Sem traços, pontos ou barras.</span>
								</div>
								<div class="form-group form-md-radios">
									<label>Sexo</label>
									<div class="md-radio-inline">
										<div class="md-radio">
											<input type="radio" id="sexo_m" name="cliente[sexo]" class="md-radiobtn" value="M" {{ $cliente && $cliente->sexo == "M" ? 'checked' : '' }} required>
											<label for="sexo_m">
												<span></span>
												<span class="check"></span>
												<span class="box"></span> Masculino
											</label>
										</div>
										<div class="md-radio">
											<input type="radio" id="sexo_f" name="cliente[sexo]" class="md-radiobtn" value="F" {{ $cliente && $cliente->sexo == "F" ? 'checked' : '' }} required>
											<label for="sexo_f">
												<span></span>
												<span class="check"></span>
												<span class="box"></span> Feminino
											</label>
										</div>
									</div>
								</div>

								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[cep]" type="text" class="form-control cliente_cep" id="cliente_cep" value="{{ $cliente ? $cliente->cep : '' }}" required autocomplete="cliente_endereco">
									<label for="cliente_cep">CEP</label>
								</div>
								<div class="row">
									<div class="col">
										<div class="form-group form-md-line-input form-md-floating-label">
											<input name="cliente[numero_endereco]" type="text" class="form-control" id="cliente_numero" value="{{ $cliente ? $cliente->numero_endereco : '' }}" required autocomplete="cliente_endereco">
											<label for="cliente_numero">Número</label>
										</div>
									</div>
									<div class="col">
										{{-- <div class="form-group form-md-line-input form-md-floating-label">
											<input name="cliente[estado]" type="text" class="form-control" id="uf" value="" required autocomplete="cliente_endereco">
											<label>Estado</label>
										</div> --}}
										
										<div class="form-group form-md-line-input form-md-floating-label">
											<select name="cliente[estado]" class="form-control" id="uf" value="" required autocomplete="cliente_endereco" >
												<option value=""></option>
												<option value="ES">ES</option>
												<option value="PE">PE</option>
												<option value="RJ">RJ</option>
												<option value="SP">SP</option>
											</select>
											<label>Estado</label>
										</div>
									</div>
								</div>
								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[rua]" type="text" class="form-control" id="logradouro" value="{{ $cliente ? $cliente->rua : '' }}" required autocomplete="cliente_endereco">
									<label for="cliente_endereco">Rua</label>
								</div>
								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[bairro]" type="text" class="form-control" id="bairro" value="{{ $cliente ? $cliente->bairro : '' }}" required autocomplete="cliente_endereco">
									<label for="cliente_bairro">Bairro</label>
								</div>
								<div class="form-group form-md-line-input form-md-floating-label">
									<input name="cliente[cidade]" type="text" class="form-control" id="cidade" value="{{ $cliente ? $cliente->cidade : '' }}" required autocomplete="cliente_endereco">
									<label for="cliente_cidade">Cidade</label>
								</div>

								{{-- <h2 class="text-center mt-5">Documentos</h2>

								<p class=" mt-3">
									Precisamos de alguns documentos para validar seu cadastro.
								</p>
								<div class="form-group form-md-line-input form-md-floating-label">
									<label for="">Comprovante de Residência</label>
									<input name="documentos_comprovante_residencia" type="file" class="form-control edited" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps" required>
								</div>		
								<div class="form-group form-md-line-input form-md-floating-label">
									<label for="">CNH ou RG (frente)</label>
									<input name="documentos_cnh_rg_frente" type="file" class="form-control edited" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps" required>
								</div>		
								<div class="form-group form-md-line-input form-md-floating-label">
									<label for="">CNH ou RG (verso)</label>
									<input name="documentos_cnh_rg_verso" type="file" class="form-control edited" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps" required>
								</div>								 --}}
							</div>
						</div>
							
						<h2 class="text-center mt-5">Dados do Pet</h2>
							
						<div id="dados_pet" class="mt-3">
							<div class="form-group form-md-line-input form-md-floating-label">
								<input name="pet[nome_pet]" type="text" class="form-control" id="pet_nome" required>
								<label for="pet_nome">Nome e Sobrenome do Pet</label>
								<span class="help-block font-white visible">Apenas nome e sobrenome!</span>
							</div>
							<div class="form-group form-md-line-input form-md-floating-label">
								<input name="pet[data_nascimento]" type="text" class="form-control data" id="pet_data_nascimento" required>
								<label for="pet_data_nascimento">Data de Nascimento</label>
							</div>
							<div class="form-group form-md-radios">
								<label>Sexo do Pet</label>
								<div class="md-radio-inline">
									<div class="md-radio">
										<input type="radio" id="pet_sexo_m" name="pet[sexo]" class="md-radiobtn" value="M" required>
										<label for="pet_sexo_m">
											<span></span>
											<span class="check"></span>
											<span class="box"></span> Macho
										</label>
									</div>
									<div class="md-radio">
										<input type="radio" id="pet_sexo_f" name="pet[sexo]" class="md-radiobtn" value="F" required>
										<label for="pet_sexo_f">
											<span></span>
											<span class="check"></span>
											<span class="box"></span> Fêmea
										</label>
									</div>
								</div>
							</div>
							<div class="form-group form-md-radios">
								<label>Tipo do Pet</label>
								<div class="md-radio-inline">
									<div class="md-radio">
										<input type="radio" id="pet_tipo_cao" name="pet[tipo]" class="md-radiobtn" value="CACHORRO" required>
										<label for="pet_tipo_cao">
											<span></span>
											<span class="check"></span>
											<span class="box"></span> Cão
										</label>
									</div>
									<div class="md-radio">
										<input type="radio" id="pet_tipo_gato" name="pet[tipo]" class="md-radiobtn" value="GATO" required>
										<label for="pet_tipo_gato">
											<span></span>
											<span class="check"></span>
											<span class="box"></span> Gato
										</label>
									</div>
								</div>
							</div>
							<div class="form-group form-md-line-input form-md-floating-label">
								<select name="pet[id_raca]" class="form-control" id="pet_raca" required>
									<option value=""></option>
									@foreach($racas as $raca)
										<option value="{{ $raca->id }}" data-tipo="{{ $raca->tipo }}">{{ $raca->nome . ($raca->id == 1 ? ' (Sem Raça Definida)' : '') }}</option>
									@endforeach
								</select>
								<label for="pet_raca">Raça</label>
							</div>
							<input type="hidden" name="plano[id_plano]" value="43" required>
							<input type="hidden" name="plano[participativo]" value="0" required>
							<input type="hidden" name="plano[familiar]" value="0" required>
							<input type="hidden" name="plano[data_inicio_contrato]" value="{{ (new Carbon\Carbon())->today()->format('Y-m-d') }}" required>
							<input type="hidden" name="plano[valor_plano]" value="0" required>
							<input type="hidden" name="plano[valor_adesao]" value="0" required>
							<input type="hidden" name="plano[regime]" value="ANUAL" required>
						</div>

						<br>

						<div id="check_termos">
							<div class="form-group">

								<div class="md-checkbox">
									<div class="md-checkbox">
										<input type="checkbox" class="md-check" checked required>
										<label>
											<span></span>
											<span class="check"></span>
											<span class="box"></span> 
											Li e aceito o <a href="javascript:;" style="color: white; font-weight:bold; text-decoration: underline;" data-toggle="modal" data-target="#modal-contrato">contrato</a> 
										</label>
									</div>
									<div class="md-checkbox">
										<input type="checkbox" class="md-check" checked required>
										<label>
											<span></span>
											<span class="check"></span>
											<span class="box"></span> 
											Li e aceito o <a href="javascript:;" style="color: white; font-weight:bold; text-decoration: underline;" data-toggle="modal" data-target="#modal-regulamento">regulamento</a> 
										</label>
									</div>
									<div class="md-checkbox">
										<input type="checkbox" class="md-check" checked required>
										<label>
											<span></span>
											<span class="check"></span>
											<span class="box"></span> 
											Li e aceito os <a href="javascript:;" style="color: white; font-weight:bold; text-decoration: underline;" data-toggle="modal" data-target="#modal-termos">termos</a> 
										</label>
									</div>
								</div>

							</div>
						</div>

						<br>

					</div>
					
					<button type="submit" class="btn btn-lg white font-blue form-control margin-bottom-20 btn_cadastrar">CADASTRAR</button>
				</form>
            </div>
        </div>
	</div>
	
	@include('freemium.parts.plano_free.modal_contrato')
	@include('freemium.parts.plano_free.modal_regulamento')
	@include('freemium.parts.plano_free.modal_termos')

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-mask/jquery.mask.min.js" type="text/javascript"></script>
	<script src="{{ url('/') }}/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script>

		function updateAddressFields(data, mappings) {
			var map = mappings || {
				"bairro": "#bairro",
				"localidade": "#cidade",
				"logradouro": "#logradouro",
				"uf": "#uf"
			};
			var field = "";
			var f;
			for (field in map) {
				if (map.hasOwnProperty(field)) {
					f = map[field];
					$(f).val(data[field]);
				}
			}
		}

        $(document).ready(function() {

			var cpf = $('.cpf');
			var cel = $('.cel');
			var data = $('.data');
			var pet_nome = $('#pet_nome');

			pet_nome.on('keyup', function (e){
				if (pet_nome.val().indexOf(" ") != -1 &&  String.fromCharCode( e.which ) == ' ') {
					e.preventDefault();
				}
				return true;
			});

			cpf.mask('000.000.000-00');

			if (cel.val().length >= 11) {
				cel.mask('(00) 00000-0000');
			} else {
				cel.mask('(00) 0000-0000');
			}

			data.mask('00/00/0000');
			data.blur(function() {
				if(!isValidDate($(this).val())) {
					swal('Data inválida!', '', 'error');
					$(this).val('');
				}
			});

			$('#pet_tipo_cao, #pet_tipo_gato').change(function() {
				var tipo = $(this).val();
				$.ajax({
					url: '{{ route('api.racas') }}',
					method: 'GET',
					data: {
						tipo: tipo
					}
				}).done(function(res) {
					$('#pet_raca option').addClass('hidden').removeAttr("selected");
					$('#pet_raca option[value=""]').attr("selected","selected");
					$('#pet_raca option[data-tipo="TODOS"]').removeClass('hidden');
					$('#pet_raca option[data-tipo="'+tipo+'"]').removeClass('hidden');
				});
			});

			// var $trigger = $('.address-search-trigger-blur');
			// $trigger.blur(function () {
			// 	var $cep = $(this);
			// 	var $url = Providers.build(Providers.viacep, $cep.val());
			// 	$.ajaxSetup({
			// 		'headers': {}
			// 	});
			// 	$.ajax({
			// 		'url': $url,
			// 		'type': 'get',
			// 		'dataType': 'json'
			// 	}).done(function (data) {
			// 		updateAddressFields(data);
			// 	}).fail(function () {}).always(function () {});
			// });

			$('.cliente_cep').blur(function(e) {
				var $cep = $(this);
				var $url = 'https://viacep.com.br/ws/'+$cep.val()+'/json/';
				$.ajaxSetup({
					'headers': {}
				});
				$.ajax({
					'url': $url,
					'type': 'get',
					'dataType': 'json'
				}).done(function (data) {
					if (['ES', 'SP', 'RJ', 'PE'].includes(data.uf)) {
						updateAddressFields(data);
						$('#bairro').addClass('edited');
						$('#cidade').addClass('edited');
						$('#logradouro').addClass('edited');
						$('#uf').addClass('edited');
					} else {
						swal('Ops...', 'Nós ainda não chegamos na sua região! Em breve estaremos aí e iremos te avisar!', 'warning');
						$('.cliente_cep').val('').removeClass('edited');
						$('#bairro').val('').removeClass('edited');
						$('#cidade').val('').removeClass('edited');
						$('#logradouro').val('').removeClass('edited');
						$('#uf').val('').removeClass('edited');
					}
				}).fail(function () {}).always(function () {});
			});

			$('#form_adesao').submit(function(e) {
				$('#form_adesao .btn_cadastrar').attr('disabled', 'disabled');
				e.preventDefault();
				var formData = new FormData(this);
				// var dadosCliente = $(this).find('#dados_cliente').find('input, select').serializeArray();
				// var dadosPet = $(this).find('#dados_pet').find('input, select').serializeArray();

				swal({
					title: "Enviando seus dados",
					text: "Por favor, aguarde um momento...",
					showConfirmButton: false,
					allowOutsideClick: false,
					onBeforeOpen: () => {
						swal.showLoading()
					}
				});

				$.ajax({
					url: "{{ route('clientes.adesao_plano_free') }}",
					method: 'POST',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
				}).done(function(resCliente) {
					window.location.href = "{{ route('clientes.sucesso_plano_free') }}";
				});

				return false;
			});

			// Validates that the input string is a valid date formatted as "mm/dd/yyyy"
			function isValidDate(dateString)
			{
				// First check for the pattern
				if(!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
					return false;

				// Parse the date parts to integers
				var parts = dateString.split("/");
				var day = parseInt(parts[0], 10);
				var month = parseInt(parts[1], 10);
				var year = parseInt(parts[2], 10);

				// Check the ranges of month and year
				if(year < 1000 || year > 3000 || month == 0 || month > 12)
					return false;

				var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

				// Adjust for leap years
				if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
					monthLength[1] = 29;

				// Check the range of the day
				return day > 0 && day <= monthLength[month - 1];
			};

        });
    </script>
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