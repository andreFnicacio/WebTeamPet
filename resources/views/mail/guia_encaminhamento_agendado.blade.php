@extends('mail.modelos.cliente.v2')

@section('content')

<td align="center" valign="top" id="templateBody">
	<!--[if (gte mso 9)|(IE)]>
	<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
		<tr>
			<td align="center" valign="top" width="600" style="width:600px;">
				<![endif]-->
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
				<tbody>
					<tr>
						<td valign="top" class="bodyContainer">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
							<tbody class="mcnTextBlockOuter">
								<tr>
									<td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
										<!--[if mso]>
										<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
										<tr>
											<![endif]-->
											<!--[if mso]>
											<td valign="top" width="600" style="width:600px;">
												<![endif]-->
												<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
													<tbody>
													<tr>
														<td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
															<h1>
																<br>
																<span style="color:#696969">
																<span style="font-size:20px">
																<span style="font-family:helvetica neue,helvetica,arial,verdana,sans-serif">Olá, {{ $nome_cliente }}!</span>
																</span>
																</span>
															</h1>
															<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
																<span style="color:#696969">Uma&nbsp;guia de&nbsp;encaminhamento&nbsp;para o(a) pet "<strong>{{ $nome_pet }}</strong>" acabou de ser liberada para agendamento. Abaixo, você terá mais detalhes:</span>
															</p>
															<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
																<span style="color:#696969">Período que você poderá marcar:&nbsp;<strong>{{ $periodo_agendamento }}&nbsp;</strong>
																<br>
																Clínica que&nbsp;solicitou:&nbsp;<strong>{{ $nome_clinica }}</strong>
																<br>
																Médico que solicitou:&nbsp;<strong>{{ $nome_prestador }}</strong>
																<br>
																<br>
																<span style="font-size:14px">
																<span style="font-family:helvetica neue,helvetica,arial,verdana,sans-serif">
																	O que foi solicitado:&nbsp;<br>
																	@foreach($procedimentos as $proc)
																		-&nbsp;<strong>{{ $proc }}</strong>
																		<br>
																	@endforeach
																</span>
																</span>
																<br>
																<br>
																<strong>O que você deve fazer agora?</strong>
																<br>
																Agora, você deverá escolher uma clínica ou hospital para agendar os procedimentos solicitados. Para escolher o credenciado, <strong>você deverá baixar nosso app e seguir as instruções de agendamento</strong>. É rápido e seguro, além de ser o caminho mais recomendado.&nbsp;</span>
																<br>
																<br>
																&nbsp;
															</p>
														</td>
													</tr>
													</tbody>
												</table>
												<!--[if mso]>
											</td>
											<![endif]-->
											<!--[if mso]>
										</tr>
										</table>
										<![endif]-->
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
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnButtonBlock" style="min-width:100%;">
							<tbody class="mcnButtonBlockOuter">
								<tr>
									<td style="padding-top:0; padding-right:18px; padding-bottom:18px; padding-left:18px;" valign="top" align="center" class="mcnButtonBlockInner">
										<table border="0" cellpadding="0" cellspacing="0" class="mcnButtonContentContainer" style="border-collapse: separate !important;border-radius: 4px;background-color: #2BAADF;">
										<tbody>
											<tr>
												<td align="center" valign="middle" class="mcnButtonContent" style="font-family: Arial; font-size: 16px; padding: 18px;">
													<a class="mcnButton " title="Acessar minha área do cliente" href="http://app.lifepet.com.br/cliente/login" target="_blank" style="font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;">Acessar minha área do cliente</a>
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
										<!--[if mso]>
										<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
										<tr>
											<![endif]-->
											<!--[if mso]>
											<td valign="top" width="600" style="width:600px;">
												<![endif]-->
												<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
													<tbody>
													<tr>
														<td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
															<br>
															<span style="color:#696969">Caso você não queira prosseguir o atendimento por aplicativo, poderá acessar nosso site e seguir os passos abaixo:</span>
															<br>
															<br>
															1) Acesse nosso site em <a href="file:///Users/Alexandre/Downloads/www.lifepet.com.br/rede">www.lifepet.com.br/rede</a>
															<br>
															2) Busque&nbsp;o credenciado desejado<br>
															3) Ligue para o credenciado e agende<br>
															4) No dia do atendimento, você deverá informar o código abaixo para validação:
															<div class="coupon" style="
																border: 3px dotted #0fd87b;
																width: 80%;
																margin: 0 auto;
																border-radius: 15px;
																max-width: 300px;
																text-align:center;
																">
																<div class="container" style="background-color:white;padding: 2px 16px;background-color: #f1f1f1;text-align: center;">
																<h2 style="
																	text-align:center;
																	padding:5px;
																	color: #43bb84;
																	font-size: 20px;
																	">
																	<strong>{{ $numero_guia }}</strong>
																</h2>
																</div>
															</div>
														</td>
													</tr>
													</tbody>
												</table>
												<!--[if mso]>
											</td>
											<![endif]-->
											<!--[if mso]>
										</tr>
										</table>
										<![endif]-->
									</td>
								</tr>
							</tbody>
							</table>
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
							<tbody class="mcnTextBlockOuter">
								<tr>
									<td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
										<!--[if mso]>
										<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
										<tr>
											<![endif]-->
											<!--[if mso]>
											<td valign="top" width="600" style="width:600px;">
												<![endif]-->
												<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
													<tbody>
													<tr>
													</tr>
													</tbody>
												</table>
												<!--[if mso]>
											</td>
											<![endif]-->
											<!--[if mso]>
										</tr>
										</table>
										<![endif]-->
									</td>
								</tr>
							</tbody>
							</table>
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
							<tbody class="mcnTextBlockOuter">
								<tr>
									<td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
										<!--[if mso]>
										<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
										<tr>
											<![endif]-->
											<!--[if mso]>
											<td valign="top" width="600" style="width:600px;">
												<![endif]-->
												<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
													<tbody>
													<tr>
														<td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
															<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
																<span style="font-size:14px">
																<span style="color:#696969">Obs: <br>
																- A guia ficará pendente até o dia da realização do(s) procedimento(s).<br>
																- Após o período estabelecido, sua guia será automaticamente expirada.<br>
																<br>
																Estamos à disposição para o que você precisar. A partir de agora você pode ficar tranquilo e deixar a saúde do seu filhote por nossa conta :)<br>
																<br>
																Atenciosamente,<br>
																Equipe Lifepet</span>
																</span>
															</p>
														</td>
													</tr>
													</tbody>
												</table>
												<!--[if mso]>
											</td>
											<![endif]-->
											<!--[if mso]>
										</tr>
										</table>
										<![endif]-->
									</td>
								</tr>
							</tbody>
							</table>
						</td>
					</tr>
				</tbody>
				</table>
				<!--[if (gte mso 9)|(IE)]>
			</td>
		</tr>
	</table>
	<![endif]-->
	</td>

@endsection
