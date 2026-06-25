@extends('layouts.app')

@section('title', 'Reservar Equipo')

@section('icon')
    <div class="flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
@endsection

@section('page-title', 'Reserva de Equipos')

@section('page-description', 'Solicita equipos de cómputo para tus proyectos')

@section('content')
    <livewire:public.reserva-equipo-simple />
@endsection
