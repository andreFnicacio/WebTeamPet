<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" required>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Plano FREE</title>

    {{-- <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script> --}}
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link href="{{ url('/') }}/assets/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
	<link href="{{ url('/') }}/assets/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
	{{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> --}}
	
	<link rel="stylesheet" id="saasland-editor-fonts-css" href="https://fonts.googleapis.com/css?family=Poppins%3A300%2C400%2C500%2C600%2C700%2C900&amp;subset" type="text/css" media="all">

    {{-- <script src="https://code.jquery.com/jquery-3.3.1.js"
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
			crossorigin="anonymous"></script>  --}}
			
	<style>
		* {
			font-family: 'Poppins' !important;
		}
		body {
			background-image: linear-gradient(90deg, #00a5f0 20%, #00d7d7 90%);
			color: white;
		}
		.btn-social {
			font: 500 16px "Poppins", sans-serif;
			padding: 12px 24px;
			border-color: #fff;
			background: #fff;
			line-height: 27px;
			-webkit-transition: all 0.2s linear;
			-o-transition: all 0.2s linear;
			transition: all 0.2s linear;
			min-width: 180px;
			text-align: center;
			display: inline-block;
			border-radius: 45px;
			margin-bottom: 15px;
		}
		}
		.btn-social img {
			padding-right: 12px;
		}
		.btn-android {
			color: #ffffff;
			border-color: rgba(0,0,0,0);
			background: #65d87e;
		}
		.btn-apple {
			color: #ffffff;
			border-color: rgba(0,0,0,0);
			background: black;
		}
	</style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6 offset-md-3">
				<img class="d-block my-5 mx-auto " src="https://www.lifepet.com.br/wp-content/uploads/2019/08/logob2x.png" srcset="https://www.lifepet.com.br/wp-content/uploads/2019/08/logob2x.png 2x" alt="Lifepet Brasil">

				@if(request('err') && request('err') == '001')
					<h2 class="text-center mt-5">Ops!</h2>

					<p>
						Verificamos que você já é nosso cliente!
					</p>
					<p>
						Infelizmente, existe algum problema em seu cadastro. Não será possível fazer adesão no Plano Free neste momento.
					</p>
				@else
					<h2 class="text-center mt-5">Que bom te ver aqui!</h2>

					<p>
						Estamos muito felizes com sua chegada em nosso Plano Free. Agora, seus documentos serão validados por nosso atendimento e assim que aprovado, você poderá agendar a microchipagem do seu pet e começar a usar. Até lá, você já pode ir acompanhando e se cadastrando no nosso App. 
					</p>
				@endif
				<h3 class="text-center mt-5">Novo App Lifepet</h3>
				<h2 class="text-center">Baixe agora!</h2>
				<p>
					Moderno, intuitivo, rápido e seguro. Feito com carinho para você ter fácil acesso a todo o histórico do seu filho de patas com a Lifepet, além de acessar a carteirinha virtual e acompanhar seu histórico financeiro.  Se você já é cliente, baixe agora nosso app e ganhe mais tempo para brincar com seus filhotes.
				</p>

				<div class="btn_group text-center">
					<a href="https://play.google.com/store/apps/details?id=com.lifepet.lifepet&amp;hl=pt&amp;showAllReviews=true" class="app_btn app_btn_one wow fadeInLeft btn-social btn-android" data-wow-delay="0.5s" style="visibility: visible; animation-delay: 0.5s; animation-name: fadeInLeft;">
						<img src="https://www.lifepet.com.br/wp-content/uploads/2019/09/googleplay.png" alt="download button">
						Google Play                                        
					</a>
					<a href="https://apps.apple.com/br/app/lifepet/id1361154811" class="app_btn app_btn_two wow fadeInLeft btn-social btn-apple" data-wow-delay="0.6s" style="visibility: visible; animation-delay: 0.6s; animation-name: fadeInLeft;">
						<img src="https://www.lifepet.com.br/wp-content/uploads/2019/09/iconapple2.png" alt="download button">                                             
						App Store                                        
					</a>
					{{-- <button class="btn btn-default btn-circle green-jungle py-2 px-3">
						<i class="fa fa-android fa-2x"></i>
						Google Play
					</button>
					<button class="btn btn-default btn-circle dark py-2 px-3">
						<i class="fa fa-apple fa-2x"></i>
						App Store
					</button> --}}
				</div>
				
            </div>
        </div>
    </div>

    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="{{ url('/') }}/assets/global/scripts/app.min.js" type="text/javascript"></script>
    <script src="{{ url('/') }}/assets/global/plugins/jquery-mask/jquery.mask.min.js" type="text/javascript"></script> --}}
    {{-- <script>

        $(document).ready(function() {

			var cel = $('.cel');
			var data = $('.data');

			if (cel.length == 11) {
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

			$('.cep').blur(function(e) {
				$('#bairro').addClass('edited');
				$('#cidade').addClass('edited');
				$('#logradouro').addClass('edited');
			});

			$('#form_adesao').submit(function(e) {
				e.preventDefault();

				// var dadosCliente = $(this).find('#dados_cliente').find('input, select').serializeArray();
				// var dadosPet = $(this).find('#dados_pet').find('input, select').serializeArray();

				$.ajax({
					url: '{{ route('clientes.adesao_plano_free') }}',
					method: 'POST',
					data: $('#form_adesao').serializeArray()
				}).done(function(resCliente) {
					console.log(resCliente);
					// $.ajax({
					// 	url: "{{ route('api.adesao.inclusao_pet', "+resCliente.id+") }}",
					// 	method: 'POST',
					// 	data: dadosPet
					// }).done(function(resPet) {
					// 	console.log(resPet);
					// });
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
	<script type="text/javascript" src="{{ mix('js/app.js') }}?{{ time() }}"></script> --}}
</body>
</html>