@extends('layouts.app')

@section('title')
    @parent
    Manual do Credenciado
@endsection

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

    <iframe class="full-frame" src="https://www.lifepet.com.br/manual-do-credenciado" frameborder="0"></iframe>

@endsection