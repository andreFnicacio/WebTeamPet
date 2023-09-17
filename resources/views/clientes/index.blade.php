@extends('layouts.app')

@section('title')
    @parent
    Clientes
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Clientes</h1>
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
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <h4 class="filter-label">Estado</h4>
                                            <div class="input-group">
                                                <span class="input-group-addon input-left">
                                                    <i class="fa fa-check-square"></i>
                                                </span>
                                                <select name="estados[]" id="estados" class="form-control select2"  multiple="">
                                                    @foreach($estados as $estado)
                                                        <option value="{{ $estado }}" 
                                                            {{ in_array($estado, $params['estados']) ? 'selected' : '' }}>
                                                            {{ $estado }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <h4 class="filter-label">Ano de adesão</h4>
                                            <div class="input-group">
                                                <span class="input-group-addon input-left">
                                                    <i class="fa fa-check-square"></i>
                                                </span>
                                                <select name="anos[]" id="anos" class="form-control select2" multiple=""> 
                                                    @foreach($anos as $ano)
                                                        <option value="{{ $ano }}" 
                                                            {{ in_array($ano, $params['anos']) ? 'selected' : '' }}>
                                                            {{ $ano }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <h4 class="filter-label">Status</h4>
                                            <select name="status" id="status" class="form-control">
                                                <option value=""></option>
                                                <option value="1" {{ $params['status'] === '1' ? 'selected' : '' }}> 
                                                    ATIVO 
                                                </option>
                                                <option value="0"
                                                    {{ $params['status'] === '0' ? 'selected' : '' }}> 
                                                    INATIVO 
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <h4 class="filter-label">Ordenar por:</h4>
                                            <select name="ordem" id="ordem" class="form-control">
                                                <option value=""></option>
                                                <option value="default" 
                                                    {{ $params['ordem'] === 'default' ? 'selected' : '' }}>
                                                    MAIS RECENTES 
                                                </option>
                                                <option value="maisAntigos"
                                                    {{ $params['ordem'] === 'maisAntigos' ? 'selected' : '' }}>
                                                    MAIS ANTIGOS 
                                                </option>
                                                <option value="nome"
                                                    {{ $params['ordem'] === 'nome' ? 'selected' : '' }}> 
                                                    NOME 
                                                </option>
                                            </select>
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
                        @include('clientes.table')
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

