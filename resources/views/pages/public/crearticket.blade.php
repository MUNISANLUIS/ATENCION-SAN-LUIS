@extends('layouts.app')

@section('title', 'ATENCION SAN LUIS')

@section('icon')
    <div class="flex items-center justify-center">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
    </div>
@endsection

@section('page-title', 'Municipalidad Distrital de San Luis')

@section('page-description', 'Gestión de Solicitudes y Servicios Municipales')

@section('content')
    <livewire:public.ticket />
@endsection