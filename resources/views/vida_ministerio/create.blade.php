@extends('layouts.app')

@section('content')
    @include('vida_ministerio.partials._form', [
        'modo' => 'create',
        'programa' => null,
        'publicadoresPorTipo' => $publicadoresPorTipo,
        'historial' => $historial,
        'fechaReferencia' => $fechaReferencia,
        'asignacionesActuales' => collect(),
    ])
@endsection