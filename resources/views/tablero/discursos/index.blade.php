@extends('layouts.app')

@section('content')
<div class="container">

    {{-- BOTONES --}}
    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
        <a href="{{ route('tablero.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Volver al Tablero
        </a>
        <a href="{{ route('discursos.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Agregar discurso
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-print"></i> Imprimir
        </button>
    </div>

    {{-- CARD VISITAS --}}
    <div class="mb-5">
        <div class="banner-programa">
            <h3 class="titulo">DISCURSOS PÚBLICOS</h3>
            <h1 class="subtitulo fw-bold">VISITAS</h1>
        </div>

        <div class="table-responsive">
            <table class="tabla-programa text-center align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Discurso</th>
                        <th>Disertante</th>
                        <th>Congregación</th>
                        <th class="no-print"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitas as $v)
                        <tr>
                            <td><strong>{{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y') }}</strong></td>
                            <td>{{ $v->conferencia }}</td>
                            <td>{{ $v->disertante }}</td>
                            <td>{{ $v->congregacion }}</td>
                            <td class="no-print">
                                <form action="{{ route('discursos.destroy', $v) }}" method="POST" onsubmit="return confirm('¿Eliminar?')">
                                    @csrf @method('DELETE')
                                    <a href="{{ route('discursos.edit', $v) }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-edit"></i></a>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Sin registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- CARD SALIDAS --}}
    <div>
        <div class="banner-programa">
            <h3 class="titulo">DISCURSOS PÚBLICOS</h3>
            <h1 class="subtitulo fw-bold">SALIDAS</h1>
        </div>

        <div class="table-responsive">
            <table class="tabla-programa text-center align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Discurso</th>
                        <th>Disertante</th>
                        <th>Congregación</th>
                        <th>Horario</th>
                        <th class="no-print"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salidas as $s)
                        <tr>
                            <td><strong>{{ \Carbon\Carbon::parse($s->fecha)->format('d/m/Y') }}</strong></td>
                            <td>{{ $s->conferencia }}</td>
                            <td>{{ $s->disertante }}</td>
                            <td>{{ $s->congregacion }}</td>
                            <td>{{ $s->horario }}</td>
                            <td class="no-print">
                                <form action="{{ route('discursos.destroy', $s) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{ route('discursos.edit', $s) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('¿Eliminar?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Sin registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
