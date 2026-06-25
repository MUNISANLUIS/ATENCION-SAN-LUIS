@extends('layouts.admin')

@section('title', 'Reservas en Horario')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <livewire:admin.admin-reservas-horario />
        </div>
    </div>
</div>
@endsection
