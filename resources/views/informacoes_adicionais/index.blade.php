@extends('layouts.app')

@section('content')
    <section class="content-header text-center">
        <h1 class="title">Informacoes Adicionais</h1>

    </section>

    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">

                @include('informacoes_adicionais.table')

            </div>
        </div>
    </div>

@endsection

