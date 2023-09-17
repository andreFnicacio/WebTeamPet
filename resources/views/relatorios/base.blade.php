@extends('layouts.app')

@section('title')
    @parent
    Relatórios
@endsection

@section('css')
    @parent
    <style>
        table th {
            vertical-align: middle !important;
        }
        table tr.total {
            border-bottom: 1px solid #e7ecf1;
        }
        table {
            margin-bottom: 40px !important;
        }
    </style>
@endsection

@section('content')
    @parent

@endsection

@section('scripts')
    @parent
@endsection