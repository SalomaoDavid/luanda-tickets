@extends('layouts.app')
@section('title', 'Editar Perfil — Luanda Tickets')
@section('content')

{{-- CSS partilhado do perfil --}}
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

{{-- Componente Livewire leve — trata toda a lógica de edição --}}
@livewire('profile-edit')

@endsection