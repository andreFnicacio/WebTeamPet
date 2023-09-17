@extends('layouts.app')

@section('title')
    @parent
    Clientes em Aprovação
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Clientes - Aprovação</h1>
            </section>
            <div class="content">
                <div class="clearfix"></div>

                @include('flash::message')

                <div class="clearfix"></div>
                <div class="box box-primary">
                    <div class="box-body">

                        <div class="portlet">
                            <div class="portlet-body">
                                <div class="table-wrapper">
                                    <table class="table table-responsive datatable table-hover responsive" id="clientes-table">
                                        <thead>
                                        <th>ID</th>
                                        <th>Contrato</th>
                                        <th>Nome</th>
                                        <th>CPF</th>
                                        <th>Email</th>
                                        <th>Sexo</th>
                                        <th colspan="3">Ações</th>
                                        </thead>
                                        <tbody>
                                        @foreach($clientes as $clientes)
                                            <tr>
                                                <td>{{ $clientes->id  }}</td>
                                                <td>{!! $clientes->numero_contrato !!}</td>
                                                <td>{!! $clientes->nome_cliente !!}</td>
                                                <td>{!! $clientes->cpf !!}</td>
                                                <td>{!! $clientes->email !!}</td>
                                                <td>{!! $clientes->sexo == 'F' ? 'Feminino' : 'Masculino' !!}</td>
                                                <td>
                                                    <div class='btn-group'>
                                                        <a href="{!! route('clientes.edit', [$clientes->id]) !!}" class='btn btn-default btn-xs btn-circle edit'>
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <form action="{!! route('clientes.approve', [$clientes->id]) !!}" method="post">
                                                            {{ csrf_field() }}
                                                            <button type="submit" class="btn btn-success btn-circle edit">
                                                                <i class="fa fa-thumbs-up"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

