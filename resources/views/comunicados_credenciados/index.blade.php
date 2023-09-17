@extends('layouts.app')

@section('title')
    @parent
    Comunicados para Credenciados
@endsection

@section('css')
    @parent
    <style>
        .search {
            padding: 20px 30px;
        }
        .search .form-inline input {
            border-radius: 0;
            float: left;
            border-right: 0;
        }
        .search .form-inline button[type="submit"] {
            float: left;
            box-shadow: none !important;
            border-left: none;
            background-color: #1980d5;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Comunicados para Credenciados</h1>
            </section>

            <div class="content">
                <div class="clearfix"></div>

                @include('flash::message')

                <div class="clearfix"></div>
                <div class="box box-primary">
                    <div class="box-body">

                        @include('comunicados_credenciados.table')

                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

