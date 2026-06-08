@if(!$valor)
    -
@elseif($campo->tipo === 'numero')
    {{ $valor->valor_numero }}
@elseif($campo->tipo === 'fecha')
    {{ $valor->valor_fecha ? \Carbon\Carbon::parse($valor->valor_fecha)->format('d/m/Y') : '-' }}
@elseif($campo->tipo === 'hora')
    {{ $valor->valor_hora ? \Carbon\Carbon::parse($valor->valor_hora)->format('H:i') : '-' }}
@elseif($campo->tipo === 'checkbox')
    {{ data_get($valor->valor_json, 'checked') ? 'Sí' : 'No' }}
@else
    {{ $valor->valor_texto ?: '-' }}
@endif