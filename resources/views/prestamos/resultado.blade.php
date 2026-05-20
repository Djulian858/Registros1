@extends('layouts.app')

@section('content')

<div class="alert {{ $clase }}">
    <p class="alert__title">Resultado</p>
    <p class="alert__text">{{ $mensaje }}</p>
    <div class="alert__actions">
        <a class="alert__link" href="{{ route('prestamos.index') }}">Volver</a>
    </div>
</div>

@endsection
