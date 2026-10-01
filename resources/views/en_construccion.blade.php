@extends('layouts.app')
@section('titulo', 'En construcción')

@section('contenido')
    <h2>{{ $titulo ?? 'Módulo' }}</h2>
    <div class="alert alert-info">Este módulo está en construcción.</div>
@endsection
