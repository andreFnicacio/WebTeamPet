@extends('relatorios.base')

@section('title')
    @parent
    - Clientes Inadimplentes
@endsection

@section('css')
    @parent
    <style>
        form input, form select {
            border-radius: 0;
        }
        .select2-selection.select2-selection--multiple {
            border-radius: 0 !important;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .only-print {
                display: block !important;
            }
        }
        @page {
            size: landscape;
        }

        .portlet.light.bordered {
            border: none !important;
        }
        .only-print {
            display: none;
        }
        .datepicker.dropdown-menu {
            z-index: 10000;
        }
    </style>
@endsection

@section('content')
    @parent
    <div class="row">
        <div class="col-sm-12">
            <section class="content-header text-center">
                <h1 class="title">Rede Credenciada - Planos Antigos</h1>
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
                                                <select name="estados" id="estados" class="form-control select2">
                                                    <option value=""></option>
                                                    @foreach($estados as $estado)
                                                        <option value="{{ $estado }}"
                                                        {{ $params['estados'] == $estado ? 'selected' : '' }}>
                                                            {{ $estado }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <h4 class="filter-label">Cidade</h4>
                                            <div class="input-group">
                                                <select name="cidades" id="cidades" class="form-control select2">
                                                    <option value=""></option>
                                                    @foreach($cidades as $cidade)
                                                        <option value="{{ $cidade }}"
                                                        {{ $params['cidades'] == $cidade ? 'selected' : '' }}>
                                                            {{ $cidade }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>  
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <h4 class="filter-label">Tipo</h4>
                                            <div class="input-group">
                                                <select name="tipos" id="tipos" class="form-control select2">
                                                    <option value="">Tipo</option>
                                                    @foreach($tipos as $tipo)
                                                        <option value="{{ $tipo }}"
                                                        {{ $params['tipos'] == $tipo ? 'selected' : '' }}>
                                                            {{ $tipo }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>  
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <h4 class="filter-label">Plano</h4>
                                            <div class="input-group">
                                                <select name="planos" id="planos" class="form-control select2">
                                                    <option value=""></option>
                                                    @foreach($planos as $plano)
                                                        <option value="{{ $plano->id }}"
                                                            {{ $params['planos'] == $plano->id ? 'selected' : '' }}>
                                                            {{ $plano->nome_plano }}
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="portlet">
        <div class="portlet-body">
            <div class="table-wrapper">
                <table class="table table-responsive datatable table-hover responsive" id="clientes-table">
                    <thead>
                    <th>Nome</th>
                    <th>Estado</th>
                    <th>Cidade</th>
                    <th>Bairro</th>
                    <th>Tipo</th>
                    </thead>
                    <tbody>
                    @foreach($clinicas as $clinica)
                        <tr>
                            <td>{{ $clinica->nome_site }}</td>
                            <td>{{ $clinica->estado }}</td>
                            <td>{{ $clinica->cidade }}</td>
                            <td>{{ $clinica->bairro }}</td>
                            <td>{{ $clinica->tipo }}</td>
                            <td>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-detalhes-{{$clinica->id}}">
                                    Detalhes
                                  </button>
                                  
                                  <!-- Modal -->
                                  <div class="modal fade" id="modal-detalhes-{{$clinica->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h3 class="modal-title" id="exampleModalLabel">{{ $clinica->nome_site }}</h5>
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                          </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>
                                                <span style="font-weight: bold; font-size: 14px;">Endereço:</span><br> 
                                                {{ $clinica->endereco_completo }}
                                            </p>
                                            <p>
                                                <span style="font-weight: bold; font-size: 14px;">Telefones:</span><br>
                                                {{ $clinica->telefones ? $clinica->telefones : '' }}
                                            </p>
                                            <p>
                                                <span style="font-weight: bold; font-size: 14px;">Especialidades:</span><br>
                                                @foreach ($clinica->tagsSelecionadas as $tag)
                                                    <span class="label label-primary" style="margin: 0 3px 3px 0;display:inline-block;">
                                                        {{ $tag->tag->nome }}
                                                    </span>
                                                @endforeach
                                            </p>
                                            <p>
                                                <span style="font-weight: bold; font-size: 14px;">Planos:</span><br>
                                                @foreach ($clinica->planos() as $plano)
                                                    <span class="label label-primary" style="margin: 0 3px 3px 0;display:inline-block;">
                                                        {{ $plano->plano ? $plano->plano->nome_plano : '' }}
                                                    </span>
                                                @endforeach
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
    @parent
@endsection