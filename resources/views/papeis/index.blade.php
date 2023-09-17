@extends('layouts.app')

@section('title')
    @parent
    Papéis de Usuário
@endsection
@section('content')
    <section class="content-header">
        <h1 class="pull-left">Papéis de Usuário</h1>
        @permission('create_roles')
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('papeis.create') !!}">Novo</a>
        </h1>
        @endpermission
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('papeis.table')
            </div>
        </div>
    </div>
@endsection

