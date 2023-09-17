@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h1>403 - Não autorizado</h1>
            @if(isset($message) && $message)
                <h2>{!! nl2br($message) !!}</h2>
            @else
                <h2>Você não pode acessar esse conteúdo ou realizar essa ação.</h2>
            @endif
            <p>Clique <a href="{!! URL::previous() !!}">aqui</a> para retornar à página anterior.</p>
        </div>
    </div>
@endsection
