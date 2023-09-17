@extends('layouts.app')

@section('css')
	@parent
	<style>
		iframe.full-frame {
			width: 100%;
			height: 500vh;
		}
		.page-content-wrapper .page-content {
			padding: 1px 0px 10px;
		}
	</style>
@endsection
@section('content')
@if(\Entrust::hasRole(['CLIENTE']))
	@include('area_cliente.home')
@elseif(\Entrust::hasRole(['CLINICAS']))
	{{--<iframe class="full-frame" src="https://www.lifepet.com.br/manual-do-credenciado" frameborder="0"></iframe>--}}
	@include('clinicas.home')
@else
	<div class="container">
	    <div class="row">

	    </div>
	</div>
@endif
@endsection
