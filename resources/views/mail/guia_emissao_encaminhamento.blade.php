@extends('mail.modelos.cliente.v2')

{{-- @php
	$nome_cliente = "nome_cliente";
	$nome_pet = "nome_pet";
	$status_guia = "status_guia";
	$nome_clinica = "nome_clinica";
	$nome_prestador = "nome_prestador";
@endphp --}}

@section('content')

	<td align="center" valign="top" id="templateBody">
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
			<tbody>
				<tr>
					<td valign="top" class="bodyContainer">
						<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
							<tbody class="mcnTextBlockOuter">
								<tr>
									<td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
										<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
											<tbody>
												<tr>
													<td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
														<br>
														<h1>
															<span style="color:#696969">
																<span style="font-size:20px">
																	<span style="font-family:helvetica neue,helvetica,arial,verdana,sans-serif">Olá, {{ $nome_cliente }}!</span>
																</span>
															</span>
														</h1>
														<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
															<span style="color:#696969">
																Uma&nbsp;guia de&nbsp;encaminhamento&nbsp;para o(a) pet
																"<strong>{{ $nome_pet }}</strong>"
																acabou de ser emitida. Porém, ela precisa passar por uma análise da auditoria antes de ser
																liberada para agendamento. Abaixo, você terá mais detalhes:
															</span>
														</p>
														<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
															<span style="color:#696969">Status:
																<strong>{{ $status_guia }}</strong>
																<br>
																Clínica que&nbsp;solicitou:&nbsp;
																<strong>{{ $nome_clinica }}</strong>
																<br>
																Médico que solicitou:&nbsp;
																<strong>{{ $nome_prestador }}</strong>
																<br>
																<br>
																<strong>O que você deve fazer agora?</strong>
																<br>
																<br>
																Agora, você deverá acompanhar seu aplicativo ou e-mail para saber quando ela será liberada. Após ser autorizada,
																você será notificado por esses dois canais e receberá novas instruções para agendamento dos procedimentos.
																<strong>É recomendado você utilizar nosso apliticativo para facilitar o acompanhamento e possíveis mudanças no status da guia.</strong>
															</span>
															<br>
															<br> &nbsp;
														</p>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
						<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnButtonBlock" style="min-width:100%;">
							<tbody class="mcnButtonBlockOuter">
								<tr>
									<td style="padding-top:0; padding-right:18px; padding-bottom:18px; padding-left:18px;" valign="top" align="center" class="mcnButtonBlockInner">
										<table border="0" cellpadding="0" cellspacing="0" class="mcnButtonContentContainer" style="border-collapse: separate !important;border-radius: 4px;background-color: #FFD249;">
											<tbody>
												<tr>
													<td align="center" valign="middle" class="mcnButtonContent" style="font-family: Arial; font-size: 16px; padding: 18px;">
														<a class="mcnButton " title="Baixar aplicativo" href="http://lifepet.com.br/app" target="_blank" style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;">Baixar aplicativo</a>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
						<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
							<tbody class="mcnTextBlockOuter">
								<tr>
									<td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
										<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
											<tbody>
												<tr>
													<td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
														<h1>
															<span style="font-size:16px">O que é uma guia de encaminhamento?</span>
														</h1>
														<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
															<span style="font-size:16px">
																Quando o seu filho de patas precisa de um atendimento que não está disponível no momento da consulta,
																como um especialista ou um exame por imagem, é gerada uma guia de encaminhamento.
																<br>
																<br>
																Essa guia é um documento formal que indicará para os profissionais da Lifepet que seu pet
																necessita de um atendimento específico. Após avaliar esse documento, nossos profissionais irão
																então informar qual a data que o seu pet poderá executar o que foi solicitado.
																<br>
																<br>
																Não está conseguindo marcar com algum credenciado ou precisa de ajuda?
																<br>
																<br>
																Fale com nosso suporte. Para marcação de consultas, exames, dúvidas e sugestões,
																o nosso suporte funciona de segunda a sexta de 9h às 18h e sábado de 9h às 12h.
															</span>
														</p>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
						<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
							<tbody class="mcnTextBlockOuter">
								<tr>
									<td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
										<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
											<tbody>
												<tr>
													<td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
														<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
															<span style="font-size:14px">
																<span style="color:#696969">Estamos à disposição para o que você precisar.
																	A partir de agora você pode ficar tranquilo e deixar a saúde do seu filhote por nossa conta :)
																	<br>
																	<br>
																	Atenciosamente,
																	<br>
																	Equipe Lifepet
																</span>
															</span>
														</p>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</td>

@endsection
