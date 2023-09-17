@extends('layouts.app')

@section('title')
	@parent
	Assinaturas Pendentes
@endsection

@section('content')

	<section class="content-header text-center">
		<h1 class="title">Assinaturas Pendentes</h1>
	</section>

	@if ($prestadores->count())

		@foreach ($prestadores as $prestador)
			<div class="col-sm-12 col-md-6">
				<div class="portlet light">

					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-user-md font-green-jungle"></i>
							<span class="caption-subject font-green-jungle sbold uppercase">
								{{ $prestador->nome }}
							</span>
						</div>
						<div class="actions">
							<button class="btn green" data-toggle="modal" data-target="#modal-prestador{{ $prestador->id }}-assinatura">
								Assinar Todas
								({{ $prestador->guias->count() }})
							</button>
						</div>
					</div>

					<div class="portlet-body">
						<div class="table-scrollable table-scrollable-borderless">
							<table class="table table-hover">
								<thead>
									<tr class="uppercase">
										<th><strong>GUIA</strong></th>
										<th><strong>PET</strong></th>
										<th><strong>DATA</strong></th>
									</tr>
								</thead>
								<tbody>
									@foreach($prestador->guias as $guia)
										<tr>
											<td>
												<a href="{{ route('autorizador.verGuia', $guia->numero_guia) }}" target="_blank" class="primary-link">#{{ $guia->numero_guia }}</a>
											</td>
											<td>
												{{ $guia->pet->nome_pet }}
											</td>
											<td>
												{{ ($guia->realizado_em ?: $guia->created_at)->format('d/m/Y') }}
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>

						</div>
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal-prestador{{ $prestador->id }}-assinatura">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="text-center">Assinatura Eletrônica</h3>
						</div>
						<div class="modal-body">
							<div class="text-center">
								<div class="inline-block">
									<h3>{{ $prestador->nome }}</h3>
								</div>
							</div>

							<form action="{{ route('autorizador.assinarGuiaPrestador') }}" method="POST" id="form-assinarPrestador">
								{{ csrf_field() }}
								@foreach($prestador->guias as $guia)
									<input type="hidden" name="numero_guia[]" value="{{ $guia->numero_guia }}">
								@endforeach
								<div class="row">
									<div class="col-md-12">
										<h3 class="text-center">CRMV</h3>
									</div>
									<div class="col-md-6 col-md-offset-3">
										<input class="form-control text-center" name="senha_prestador" id="senha_prestador" type="text" placeholder="Ex.: 1234ES">
										<small class="helper">Apenas números e as duas letras do estado</small>
									</div>
								</div>
								<button class="btn btn-lg blue center-block margin-top-20" id="btn_assinar_prestador">Assinar</button>
							</form>

						</div>
					</div>
				</div>
			</div>

		@endforeach

		<div class="clearfix"></div>

	@else

		<h5 class="text-center">Não existem guias pendentes de assinatura</h5>

	@endif

@endsection
