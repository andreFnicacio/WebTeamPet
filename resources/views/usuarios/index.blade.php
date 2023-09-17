@extends('layouts.app')


@section('title')
    @parent
    Usuários
@endsection
@section('content')
    <section class="content-header">
        <h1 class="pull-left">Usuários</h1>
        @permission('create_grupos')
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('usuarios.create') !!}">Novo</a>
        </h1>
        @endpermission
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="portlet">
                    <div class="portlet-body">
                        <form id="search-form" method="GET" class="search-form search-form-expanded" action="{{ route('usuarios.index') }}">
                            <div class="input-group"><input type="text" id="query" placeholder="Buscar..." name="s" class="form-control">
                                <span class="input-group-btn">
                                    <a href="javascript:;" class="btn submit">
                                        <i class="icon-magnifier"></i>
                                    </a>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
                @include('usuarios.table')
            </div>
        </div>
    </div>
@endsection

