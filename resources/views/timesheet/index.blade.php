@extends('layouts.app')

@section('title')
    @parent
    TimeSheet
@endsection
@section('css')
    @parent

    <link rel="stylesheet" href="{!! asset('css/materialize/select.css') !!}">
    <style>
        @-webkit-keyframes movingGradient {
            0%{background-position:9% 0%}
            50%{background-position:92% 100%}
            100%{background-position:9% 0%}
        }
        @-moz-keyframes movingGradient {
            0%{background-position:9% 0%}
            50%{background-position:92% 100%}
            100%{background-position:9% 0%}
        }
        @keyframes movingGradient {
            0%{background-position:9% 0%}
            50%{background-position:92% 100%}
            100%{background-position:9% 0%}
        }

        #Timesheet {
            font-family: 'Montserrat' !important;
        }

        #Timesheet .modal .modal-dialog {
            width: 600px;
            margin: 12.5% auto;
        }

        #Timesheet .modal .modal-header {
            background: #1980d5 linear-gradient(to right bottom, #1980d5, #0094db, #00a6dc, #00b6d8, #32c5d2);
            color: white;
        }

        #Timesheet .modal .form-group {
            display: table;
            width: 100%;
        }

        #Timesheet--header {
            background: #1980d5 linear-gradient(to right bottom, #1980d5, #0094db, #00a6dc, #00b6d8, #32c5d2);
            /*animation: movingGradient 50s ease infinite;*/
            height: 340px;
            padding: 30px 35px;
        }

        .Timesheet--page-title {
            font-family: 'Montserrat';
            color: #dfdfdf;
            font-size: 16pt;
            margin-bottom: 36px;
        }

        .Timesheet--page-title .badge {
            padding: 5px 10px;
            height: auto;
            border-radius: 12px !important;
        }

        #Timesheet .select-dropdown.dropdown-trigger {
            height: auto;
            color: #e5e3e3;
        }

        .Timesheet--options.disabled * {
            color: #c9ced8 !important;
            cursor: no-drop !important;
            pointer-events: none;

        }
        .Timesheet--options .caret {
            display: none;
        }
        .Timesheet--options input, .Timesheet--options input:focus {
            padding-bottom: 11px !important;
            border-bottom: 2px solid #ccccccb3 !important;
        }
        .Timesheet--options.disabled input {
            border-bottom: 2px solid #ccccccb3;
            padding-bottom: 11px !important;
        }

        .Timesheet--department-container {
            width: 80%;
        }
        .Timesheet--project-container {
            position: relative;
            width: 80%;
        }
        .Timesheet--task-container {
            position: relative;
            width: 80%;
        }



        .Timesheet--add-button {
            position: absolute;
            top: 50%;
            color: white;
            right: -35px;
            margin-top: -16px;
            font-size: 17pt;
        }

        .Timesheet--add-button:hover,
        .Timesheet--add-button:focus,
        .Timesheet--add-button:visited {
            color: white;
            opacity: .65;
        }

        .Timesheet--department-container .select-dropdown.dropdown-trigger {
            font-size: 16pt;
            margin-bottom: 15px;
        }
        .Timesheet--project-container .select-dropdown.dropdown-trigger {
            font-size: 16pt;
            margin-bottom: 22px;
        }
        .Timesheet--task-container .select-dropdown.dropdown-trigger {
            font-size: 16pt;
        }


        #Timesheet .select-wrapper {

        }

        .Timesheet--time-wrapper {
            position: relative;
        }

        .Timesheet--time-count {
            font-size: 68pt;
            font-family: 'Montserrat';
            color: #e5e3e3;
            line-height: 230px;
            display: inline;
            float: left;
            width: 100%;
            padding-left: 30px;
        }

        .Timesheet--trigger {
            padding: 40px !important;
            border-radius: 100% !important;
            background: white;
            float: left;
            margin-top: 115px;
            top: -27px;
            position: absolute !important;
            right: 45px;
        }

        .Timesheet--project-link {
            line-height: 36px;
        }
        .Timesheet--item-icon {
            border-radius: 100%;
            padding: 10px;
            height: 36px;
            width: 36px;
            display: inline-block;
            float: left;
            text-align: center;
            font-size: 18px;
            vertical-align: middle;
            line-height: 18px;
            margin-right: 15px;
        }

        select.materialize {
            text-transform: uppercase;
        }

        .select-wrapper .caret {
            color: white;
            position: absolute;
            right: 0;
            bottom: 0;
            margin: 0;
            z-index: 0;
            transform: rotate(-45deg);
            top: unset;
        }

        .btn.btn-default.btn-circle.text-center.actions {
            height: 36px;
            width: 36px;
            text-align: center;
            vertical-align: middle;
            position: relative
        }

        #Timesheet .btn.btn-default.btn-circle.text-center.actions::after {
            content: '...';
            position: absolute;
            top: 50%;
            margin-top: -12px;
            left: 50%;
            margin-left: -7.45px;
            letter-spacing: 2px;
            font-family: 'Montserrat';
        }

        #Timesheet .dropdown .dropdown-menu {
            left: unset;
            right: 50%;
            float: right;
            padding: 5px 10px;
            margin: 2px 0 0;
            width: auto;
            min-width: unset;
        }

        /* Common Desktop */
        @media only screen and (min-device-width: 1025px) and (max-device-width: 1366px) {
            .Timesheet--time-count {
                font-size: 60pt;
            }
            .Timesheet--trigger {
                padding: 35px !important;
            }
        }

        /**
          Square Desktop
         */
        @media only screen and (min-device-width: 769px) and (max-device-width: 1024px){
            .Timesheet--time-wrapper {
                display: table;
                width: 100%;
            }
            .Timesheet--trigger {
                display: block;
                bottom: 0;
                top: unset;
                padding: 25px !important;
                right: 50%;
                margin-right: -50px;
            }
            .Timesheet--time-count {
                font-size: 40pt;
                margin-right: 10px;
                width: 230px;
            }
        }


        @media only screen and (max-device-width: 500px) and (orientation: portrait) {
            .Timesheet--department-container {
                width: 80%;
            }
            .Timesheet--project-container {
                position: relative;
                width: 93%;
            }
            .Timesheet--task-container {
                position: relative;
                width: 80%;
            }

            .Timesheet--time-wrapper {
                width: 100%;
            }
            .Timesheet--time-count {
                line-height: 123px;
                font-size: 35pt;
                padding-top: 20px;
                text-align: center;
                padding-right: 30px;
            }
            .Timesheet--trigger {
                top: -118px;
                padding: 25px !important;
                right: -5px;
            }
        }
        /**
         Portrait iPad
         */
        @media only screen and (max-device-width: 768px) and (orientation: portrait) {
            .Timesheet--time-count {
                color: #00A6DC;
            }
            #Timesheet .modal .modal-dialog {
                margin: 50% auto;
                width: 95%;
            }
            .modal-backdrop, .modal-backdrop.fade.in {
                opacity: .6;
                filter: alpha(opacity=60);
            }

            .table-responsive>.table>tbody>tr>td,
            .table-responsive>.table>tbody>tr>th,
            .table-responsive>.table>tfoot>tr>td,
            .table-responsive>.table>tfoot>tr>th,
            .table-responsive>.table>thead>tr>td,
            .table-responsive>.table>thead>tr>th {
                white-space: normal;
            }


        }
    </style>


    <style>
        .select2.select2-container {
            width: 100% !important;
        }

        .select2.select2-container .selection {
            color: #e5e3e3;
            font-family: 'Montserrat' !important;
        }
        .select2.select2-container .selection span.select2-selection {
            border-radius: 0;
            background: transparent;
            border: none;
            border-bottom: 2px solid #ccccccb3 !important;
            padding-bottom: 11px !important;
            font-size: 16pt;
            padding-left: 0;
            margin-bottom: 22px;
        }
        .select2-container--bootstrap .select2-selection--single {
            height: auto;
        }
        .select2-container--bootstrap .select2-selection--single .select2-selection__placeholder,
        .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
            color: #e5e3e3;
            text-transform: uppercase;
            font-family: 'Montserrat';
        }

    </style>
@endsection

@section('content')

    <div class="row" id="Timesheet">
        <section id="Timesheet--header">
            <div class="row">
                <div class="col-sm-5 col-md-7 col-lg-5">
                    <div class="Timesheet--page-title">
                        <span class="badge bg-white bg-font-white">TIMESHEET&nbsp;<i class="fa fa-clock-o" aria-hidden="true"></i></span>
                    </div>
                    <div class="Timesheet--options"
                         v-bind:class="{ 'disabled' : shouldStop }">
                        <div class="Timesheet--department-container">
                            <select name="department" id="Timesheet-department" v-model="selection.department" class="select2-vue-departments" @change="departmentChanged"
                                    placeholder="DEPARTAMENTOS">
                                <option value="" disabled selected>DEPARTAMENTOS</option>
                                <option v-for="d in departments" :value="d">@{{ d.nome }}</option>
                            </select>
                        </div>
                        <div class="Timesheet--project-container">
                            <select name="project" id="Timesheet-project" v-model="selection.project" class="select2-vue-projects" @change="projectChanged">
                                <option value="" disabled selected>PROJETOS</option>
                                <option v-for="p in projects" :value="p">@{{ p.nome }}</option>
                            </select>
                            <a href="#Timesheet--modal-project" v-if="selection.department" class="Timesheet--add-button" data-toggle="modal">
                                <span class="fa fa-plus"></span>
                            </a>
                        </div>
                        <div class="Timesheet--task-container">
                            <select name="task"  v-model="selection.task"  @change="taskChanged"  id="Timesheet--task" class="select2-vue-tasks">
                                <option value="" disabled selected>TAREFAS</option>
                                <option v-for="t in tasks" :value="t">@{{ t.nome }}</option>
                            </select>
                            <a href="#Timesheet--modal-task" v-if="selection.project && selection.department" class="Timesheet--add-button" data-toggle="modal">
                                <span class="fa fa-plus"></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-6 col-lg-offset-1 col-sm-offset-1">
                    <div class="Timesheet--time-wrapper">
                        <h2 class="Timesheet--time-count" id="timer">--:--:--</h2>
                        <a  @click="operate()"
                            href="#"
                            class="Timesheet--trigger btn  btn-circle btn-lg"
                            v-bind:class="{ 'disabled' : !canPlay && !shouldStop }">
                            <i class="fa fa-play" v-if="!shouldStop"></i>
                            <i class="fa fa-stop" v-if="shouldStop"></i>
                        </a>
                    </div>

                </div>
            </div>
        </section>
        <section id="Timesheet--table">
            <div class="table-responsive">
                <table class="table table-hover table-condensed table-striped">
                    <thead>
                    <tr>
                        <th>Projeto</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Duração</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="sheet in sheets">
                        <td>
                            <a href="#" class="Timesheet--project-link">
                                <span class="Timesheet--item-icon" v-bind:class="sheet.projeto.classes">
                                    @{{ sheet.projeto.sigla }}
                                </span>
                                <span class="Timesheet--item-name">
                                    @{{ sheet.projeto.nome }}
                                </span>
                            </a>
                        </td>
                        <td>
                            @{{ sheet.inicio }}
                        </td>
                        <td>
                            @{{ sheet.fim ? sheet.fim : "--:--:--" }}
                        </td>
                        <td>
                            @{{ sheet.duracao ? sheet.duracao : "--:--:--" }}
                        </td>
                        <td>
                            <div class="dropdown">
                                <a class="btn btn-default btn-circle text-center actions" data-target="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">

                                </a>

                                <ul class="dropdown-menu" aria-labelledby="dLabel">
                                    <li @click="openEditSheet(sheet)">
                                        <span class="hidden-md fa fa-edit"></span>
                                        <span class="hidden-sm hidden-xs">Editar</span>
                                    </li>
                                    <li @click="deleteSheet(sheet)">
                                        <span class="hidden-md fa fa-trash"></span>
                                        <span class="hidden-sm hidden-xs">Excluir</span>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </section>
        <div id="Timesheet--modal-project" v-if="selection.department" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Novo projeto</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label class="control-label col-md-4">Departamento</label>
                                <div class="col-md-6">
                                    <input type="text" :value="selection.department.nome" disabled="disabled" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Projeto</label>
                                <div class="col-md-6">
                                    <input type="text" v-model="record.project" class="form-control">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline btn-success" @click="saveProject()"
                                v-bind:class="{ 'disabled' : !record.project }">Enviar</button>
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="Timesheet--modal-task" v-if="selection.department && selection.project" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Nova tarefa</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-md-4">Departamento</label>
                            <div class="col-md-6">
                                <input type="text" :value="selection.department.nome" disabled="disabled" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Projeto</label>
                            <div class="col-md-6">
                                <input type="text" :value="selection.project.nome" disabled="disabled" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Tarefa</label>
                            <div class="col-md-6">
                                <input type="text" v-model="record.task" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline btn-success" @click="saveTask()"
                                v-bind:class="{ 'disabled' : !record.task }">Enviar</button>
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="Timesheet--modal-sheet" v-if="selection.sheet" class="modal fade" tabindex="-1" data-replace="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title">Editar registro</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label col-md-4">Departamento</label>
                            <div class="col-md-6">
                                <input type="text" :value="selection.sheet._department.nome" disabled="disabled" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Projeto</label>
                            <div class="col-md-6">
                                <input type="text" :value="selection.sheet._project.nome" disabled="disabled" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Tarefa</label>
                            <div class="col-md-6">
                                <input type="text" v-model="selection.sheet._task.nome" disabled="disabled" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Início</label>
                            <div class="col-md-6">
                                <input type="datetime-local" v-model="selection.sheet.inicio" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Fim</label>
                            <div class="col-md-6">
                                <input type="datetime" v-model="selection.sheet.fim" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline btn-success" @click="editSheet()">Enviar</button>
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script src="{!! asset('js/materialize/anime.min.js') !!}"></script>
    <script src="{!! asset('js/materialize/component.js') !!}"></script>
    <script src="{!! asset('js/materialize/cash.js') !!}"></script>
    <script src="{!! asset('js/materialize/dropdown.js') !!}"></script>
    <script src="{!! asset('js/materialize/select.js') !!}"></script>
    <script src="{!! asset('js/moment/moment.js') !!}"></script>
    <script src="{!! asset('js/timesheet.vue.js') !!}"></script>
    <script>
    </script>
@endsection