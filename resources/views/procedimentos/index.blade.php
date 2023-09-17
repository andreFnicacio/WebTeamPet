@extends('layouts.app')

@section('title')
    @parent
    Procedimentos
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Procedimentos</h1>
            </section>
            <div class="content">
                <div class="clearfix"></div>

                @include('flash::message')
                
                <div class="clearfix"></div>
                <div class="box box-primary">
                    <div class="box-body" style="background-color: #fff">
                        <div class="col-sm-12">
                            <form action="{{ route(Route::getCurrentRoute()->getName()) }}" method="GET" class="form">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <h4 class="filter-label">Código</h4>
                                            <input type="text" class="form-control" name="codigo" value="{{ $params['codigo'] }}">
                                        </div>  
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <h4 class="filter-label">Nome</h4>
                                            <input type="text" class="form-control" name="nome" value="{{ $params['nome'] }}">
                                        </div>  
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <h4 class="filter-label">Grupo</h4>
                                            <div class="input-group">
                                                <span class="input-group-addon input-left">
                                                    <i class="fa fa-check-square"></i>
                                                </span>
                                                <select name="grupos[]" id="grupos" class="form-control select2" multiple="multiple">
                                                    @foreach($grupos as $grupo)
                                                        <option value="{{ $grupo->id }}" 
                                                            {{ in_array($grupo->id, $params['grupos']) ? 'selected' : '' }}>
                                                            {{ $grupo->nome_grupo }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn blue margin-top-30">
                                            <span>Pesquisar</span> <span class="fa fa-search"></span>
                                        </button>
                                    </div>  
                                </div>
                            </form>
                        </div>
                        @include('procedimentos.table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

