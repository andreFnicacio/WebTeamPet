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
																	<span style="font-family:helvetica neue,helvetica,arial,verdana,sans-serif">Olá!</span>
																</span>
															</span>
														</h1>
														<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
															<span style="color:#696969">
																Ficamos muito felizes por ter você e o seu pet conosco.
																<br><br>
																Conforme solicitado, enviamos anexo o formulário para solicitação de reembolso.
																<br><br>
																Lembramos que ele precisa ser impresso, preenchido e assinado por você e pelo médico veterinário.
																<br><br>
																Se tiver alguma dúvida, entre em contato conosco pelo WhatsApp (27) 99625-5529, pelo chat no aplicativo ou pelo e-mail atendimento@lifepet.com.br que vamos te ajudar.
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
																<span style="color:#696969">
																	Um abraço,
																	<br>
																	Família LIFEPET.
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
