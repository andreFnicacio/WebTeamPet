@extends('mail.modelos.consultores.v1')

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
																		<span style="font-family:helvetica neue,helvetica,arial,verdana,sans-serif">Consultores Lifepet - Comunicado importante</span>
																	</span>		
																</span>
															</span>
														</h1>
													
														<p dir="ltr" style="font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, Verdana, sans-serif; line-height: 125%;">
                                                            <span style="font-size:16px">
                                                                
																<span style="color:#696969">Olá, {{$consultor['first_name']}}!
																	<br>
																	<br>
                                                                    

                                                                    @if ($consultor['status'] == 'pending')
                                                                        <b>Seu status foi marcado como PENDENTE DE FINALIZAÇÃO DE CADASTRO</b>
                                                                    @elseif ($consultor['status'] == 'pending_data')
                                                                        <b>Seu status foi marcado como PENDENTE DE INFORMAÇÕES</b>
                                                                    @elseif ($consultor['status'] == 'active')
                                                                        <b>Seu cadastro foi marcado como ATIVO.</b>
                                                                    @elseif ($consultor['status'] == 'blocked')
                                                                        <b>Seu cadastro foi marcado como BLOQUEADO</b>
                                                        
                                                                    @elseif ($consultor['status'] == 'canceled')
                                                                        <b>Seu cadastro foi cancelado.</b>
                                                                    @endif

                                                                    <br>
                                                                    <br>
                                                                    <b>Motivo:</b>
                                                                    <br><br>
                                                                    {{$consultor['status_reason']}}
                                                                    <br><br>
                                                                    <br>

                                                                    Qualquer dúvida, favor entrar em contato pelo e-mail {{config('app.consultores.support_email')}}
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
