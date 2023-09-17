@extends('layouts.app')

@section('title')
    @parent
    Promoções
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Promoções</h1>
            </section>
            <div class="content">
                <div class="clearfix"></div>

                @include('flash::message')

                <div class="clearfix"></div>
                <div class="box box-primary">
                    <div class="box-body">
                        @include('promocoes.table')
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

