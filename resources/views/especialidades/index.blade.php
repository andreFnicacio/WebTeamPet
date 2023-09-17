@extends('layouts.app')

@section('title')
    @parent
    Especialidades
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Especialidades</h1>
            </section>
            <div class="content">
                <div class="box box-primary">
                    <div class="box-body">
                        @include('especialidades.table')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

