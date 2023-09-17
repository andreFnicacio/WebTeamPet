@extends('mail.modelos.credenciados.v1')

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
																<span style="font-size:30px">
																	<span class="text-center">
																		<span style="font-family:helvetica neue,helvetica,arial,verdana,sans-serif">Novo Comunicado</span>
																	</span>		
																</span>
															</span>
														</h1>
														<p style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; font-size: 12px; margin: 5px 0 30px;">
																{{$comunicado->published_at->format('d/m/Y H:i')}}
														</p>
														<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
															<span style="font-size:16px">
																<span style="color:#696969">Olá, Parceiro!
																	<br>
																	<br>
																	Tem um novo recado para você na "Área do Credenciado" e ele é sobre: <b>{!! $comunicado->titulo !!}.</b>
																	<br>
																	<br>
																	Acesse e confira!
																	<br>
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
