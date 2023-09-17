@extends('layouts.app')


@section('title')
    @parent
    Prestadores
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Veterinários</h1>
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
                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            <h4 class="filter-label">Especialista</h4>
                                            <select name="especialista" id="especialista" class="form-control">
                                                <option value=""></option>
                                                <option value="1" {{ $params['especialista'] === '1' ? 'selected' : '' }}> 
                                                    SIM 
                                                </option>
                                                <option value="0" {{ $params['especialista'] === '0' ? 'selected' : '' }}> 
                                                    NÃO 
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <h4 class="filter-label">Especialidade</h4>
                                            <select name="especialidades[]" id="especialidades" class="form-control select2"  multiple="">
                                                @foreach($especialidades as $especialidade)
                                                    <option value="{{ $especialidade->id }}"
                                                        {{ in_array($especialidade->id, $params['especialidades']) ? 'selected' : '' }}>
                                                        {{ $especialidade->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            <h4 class="filter-label">CRMV</h4>
                                            <input type="text" class="form-control" name="crmv" value="{{ $params['crmv'] }}">
                                        </div>  
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <h4 class="filter-label">Nome</h4>
                                            <input type="text" class="form-control" name="nome" value="{{ $params['nome'] }}">
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
                        @include('veterinaries::table')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

