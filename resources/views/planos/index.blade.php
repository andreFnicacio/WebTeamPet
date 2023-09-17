@extends('layouts.app')

@section('title')
    @parent
    Planos
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Planos</h1>
            </section>
            <div class="content">
                <div class="clearfix"></div>

                @include('flash::message')

                <div class="clearfix"></div>
                <div class="box box-primary">
                    <div class="box-body" style="background-color: #fff">
                        <div>
                            <div class="col-sm-12">
                                <form action="{{ route(Route::getCurrentRoute()->getName()) }}" method="GET" class="form">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <h4 class="filter-label">Status</h4>
                                                <select name="status" id="status" class="form-control">
                                                    <option value=""></option>
                                                    <option value="1" {{ $params['status'] === '1' ? 'selected' : '' }}> 
                                                        ATIVO 
                                                    </option>
                                                    <option value="0" {{ $params['status'] === '0' ? 'selected' : '' }}> 
                                                        INATIVO 
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <h4 class="filter-label">Modalidade</h4>
                                                <select name="modalidade" id="modalidade" class="form-control">
                                                    <option value=""></option>
                                                    <option value="1" {{ $params['modalidade'] === '1' ? 'selected' : '' }}> 
                                                        PARTICIPATIVO 
                                                    </option>
                                                    <option value="0" {{ $params['modalidade'] === '0' ? 'selected' : '' }}> 
                                                        INTEGRAL 
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <h4 class="filter-label">Termo</h4>
                                                <input type="text" class="form-control" name="termo" value="{{ $params['termo'] }}">
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
                        </div>
                        @include('planos.table')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

