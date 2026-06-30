@extends('layouts.app')

@section('content')
    @include('vida_ministerio.partials._form', [
        'modo' => 'edit',
        'programa' => $programa,
        'publicadoresPorTipo' => $publicadoresPorTipo,
        'historial' => $historial,
        'fechaReferencia' => $fechaReferencia,
        'asignacionesActuales' => $asignacionesActuales,
    ])
@endsection