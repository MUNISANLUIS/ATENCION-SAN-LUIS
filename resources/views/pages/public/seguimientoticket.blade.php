@extends('layouts.app')

@section('title', 'Seguimiento de Tickets')

@section('icon')
    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
@endsection

@section('page-title', 'Seguimiento de Tickets')

@section('page-description', 'Consulta el estado de tu solicitud')

@section('content')
    <livewire:public.ticket-seguimiento />
@endsection