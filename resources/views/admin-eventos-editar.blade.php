@extends('layouts.app')
@section('title', 'Editar Evento')
@section('content')
    @livewire('evento-form', ['eventoId' => $evento->id])
@endsection