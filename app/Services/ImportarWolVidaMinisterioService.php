<?php

namespace App\Services;

use App\Models\VidaMinisterio;
use App\Models\VidaMinisterioParte;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class ImportarWolVidaMinisterioService
{
    private string $baseUrl = 'https://www.jw.org';

    public function importarPeriodo(int $anio, string $periodo, int $congregacionId, int $userId): array
    {
        $slugPeriodo = $this->slugPeriodo($periodo, $anio);
        $urlPeriodo = "{$this->baseUrl}/es/biblioteca/guia-actividades-reunion-testigos-jehova/{$slugPeriodo}/";

        Log::info('IMPORTAR JW - inicio', [
            'anio' => $anio,
            'periodo' => $periodo,
            'url_periodo' => $urlPeriodo,
            'congregacion_id' => $congregacionId,
            'user_id' => $userId,
        ]);

        $response = Http::timeout(30)
            ->connectTimeout(10)
            ->withUserAgent('Mozilla/5.0')
            ->get($urlPeriodo);

        Log::info('IMPORTAR JW - respuesta periodo', [
            'status' => $response->status(),
            'url' => $urlPeriodo,
            'body_length' => strlen($response->body()),
        ]);

        $response->throw();

        $crawler = new Crawler($response->body(), $urlPeriodo);

        $linksSemana = collect();

        $crawler->filter('a')->each(function (Crawler $a) use (&$linksSemana) {
            $texto = trim(preg_replace('/\s+/', ' ', $a->text('')));
            $href = $a->attr('href');

            if (!$href) {
                return;
            }

            if (!str_contains($href, 'Vida-y-Ministerio-Cristianos-')) {
                return;
            }

            $linksSemana->push($this->normalizarUrl($href));
        });

        $linksSemana = $linksSemana->unique()->values();

        Log::info('IMPORTAR JW - links encontrados', [
            'cantidad' => $linksSemana->count(),
            'links' => $linksSemana->toArray(),
        ]);

        if ($linksSemana->isEmpty()) {
            $posiblesLinks = collect();

            $crawler->filter('a')->each(function (Crawler $a) use (&$posiblesLinks) {
                $posiblesLinks->push([
                    'texto' => trim(preg_replace('/\s+/', ' ', $a->text(''))),
                    'href' => $a->attr('href'),
                ]);
            });

            Log::warning('IMPORTAR JW - no se encontraron links semanales', [
                'url_periodo' => $urlPeriodo,
                'primeros_links' => $posiblesLinks->take(30)->toArray(),
            ]);

            throw new \RuntimeException('No se encontraron links semanales en JW.org para este período.');
        }

        $creadas = 0;
        $existentes = 0;
        $programaIds = [];

        foreach ($linksSemana as $urlSemana) {
            Log::info('IMPORTAR JW - procesando semana', [
                'url_semana' => $urlSemana,
            ]);

            $dataSemana = $this->leerSemana($urlSemana, $anio);

            if (!$dataSemana || empty($dataSemana['fecha'])) {
                Log::warning('IMPORTAR JW - semana omitida, sin datos válidos', [
                    'url_semana' => $urlSemana,
                    'data' => $dataSemana,
                ]);

                continue;
            }

            $yaExistia = VidaMinisterio::where('congregacion_id', $congregacionId)
                ->whereDate('fecha', $dataSemana['fecha'])
                ->exists();

            $programa = DB::transaction(function () use ($dataSemana, $congregacionId, $userId) {
                $programa = VidaMinisterio::updateOrCreate(
                    [
                        'congregacion_id' => $congregacionId,
                        'fecha' => $dataSemana['fecha'],
                    ],
                    [
                        'user_id' => $userId,
                        'estado' => 'normal',
                        'lectura_semanal' => $dataSemana['lectura_semanal'],
                        'cancion_inicio' => $dataSemana['cancion_inicio'],
                        'cancion_medio' => $dataSemana['cancion_medio'],
                        'cancion_final' => $dataSemana['cancion_final'],
                    ]
                );

                $this->crearActualizarPartes($programa, $dataSemana['partes']);

                return $programa;
            });

            Log::info('IMPORTAR JW - programa guardado', [
                'programa_id' => $programa->id,
                'fecha' => $programa->fecha,
                'lectura_semanal' => $dataSemana['lectura_semanal'],
                'cancion_inicio' => $dataSemana['cancion_inicio'],
                'cancion_medio' => $dataSemana['cancion_medio'],
                'cancion_final' => $dataSemana['cancion_final'],
                'partes_count' => count($dataSemana['partes']),
                'ya_existia' => $yaExistia,
            ]);

            $programaIds[] = $programa->id;

            $yaExistia ? $existentes++ : $creadas++;
        }

        if (empty($programaIds)) {
            throw new \RuntimeException('Se encontraron ' . $linksSemana->count() . ' links, pero ninguna semana pudo procesarse.');
        }

        Log::info('IMPORTAR JW - finalizado', [
            'creadas' => $creadas,
            'existentes' => $existentes,
            'programa_ids' => $programaIds,
        ]);

        return [
            'creadas' => $creadas,
            'existentes' => $existentes,
            'programa_ids' => $programaIds,
        ];
    }

    private function leerSemana(string $url, int $anio): ?array
{
    $response = Http::timeout(30)
        ->connectTimeout(10)
        ->withUserAgent('Mozilla/5.0')
        ->get($url);

    Log::info('IMPORTAR JW - respuesta semana', [
        'status' => $response->status(),
        'url' => $url,
        'body_length' => strlen($response->body()),
    ]);

    $response->throw();

    $crawler = new Crawler($response->body(), $url);

    $texto = $this->normalizarTexto($crawler->filter('body')->text(' '));

    Log::info('IMPORTAR JW - texto normalizado semana', [
        'url' => $url,
        'preview' => mb_substr($texto, 0, 1500),
    ]);

    $patronSemana = '(\d{1,2}-\d{1,2}\s+de\s+[a-záéíóúñ]+|\d{1,2}\s+de\s+[a-záéíóúñ]+\s+a\s+\d{1,2}\s+de\s+[a-záéíóúñ]+)';

    if (!preg_match('/' . $patronSemana . '\s+([A-ZÁÉÍÓÚÑ0-9,\s\-]+?)\s+REPRODUCIR EN/iu', $texto, $m)) {
        Log::warning('IMPORTAR JW - no detectó fecha/lectura', [
            'url' => $url,
            'preview' => mb_substr($texto, 0, 2500),
        ]);

        return null;
    }

    $fechaTexto = trim($m[1]);
    $lecturaSemanal = trim($m[2]);

    $contenido = $this->recortarContenidoPrograma($texto);

    if (!$contenido) {
        Log::warning('IMPORTAR JW - no pudo recortar contenido principal', [
            'url' => $url,
            'preview' => mb_substr($texto, 0, 2500),
        ]);

        return null;
    }

    $cancionInicio = null;
    $cancionMedio = null;
    $cancionFinal = null;

    if (preg_match('/Canción\s+(\d+)\s+y\s+oración\s+\|\s+Palabras de introducción/iu', $contenido, $m)) {
        $cancionInicio = $m[1];
    }

    if (preg_match('/NUESTRA VIDA CRISTIANA\s+Canción\s+(\d+)/iu', $contenido, $m)) {
        $cancionMedio = $m[1];
    }

    if (preg_match('/Palabras de conclusión\s+\((\d+)\s+min(?:s)?\.\)\s+\|\s+Canción\s+(\d+)/iu', $contenido, $m)) {
        $cancionFinal = $m[2];
    }

    $posTesoros = strpos($contenido, 'TESOROS DE LA BIBLIA');
    $posMaestros = strpos($contenido, 'SEAMOS MEJORES MAESTROS');
    $posVida = strpos($contenido, 'NUESTRA VIDA CRISTIANA');

  $partes = $this->extraerPartesPrograma(
    contenido: $contenido,
    url: $url,
    posTesoros: $posTesoros,
    posMaestros: $posMaestros,
    posVida: $posVida
);

    if (preg_match('/Palabras de conclusión\s+\((\d+)\s+min(?:s)?\.\)\s+\|\s+Canción\s+(\d+)/iu', $contenido, $m)) {
        $partes[] = [
            'seccion' => 'final',
            'tipo_asignacion' => 'oracion',
            'titulo' => 'Palabras de conclusión, canción final y oración',
            'duracion_minutos' => (int) $m[1],
        ];
    }

    $data = [
        'fecha' => $this->fechaInicioSemana($fechaTexto, $anio),
        'lectura_semanal' => $lecturaSemanal,
        'cancion_inicio' => $cancionInicio,
        'cancion_medio' => $cancionMedio,
        'cancion_final' => $cancionFinal,
        'partes' => $partes,
    ];

    Log::info('IMPORTAR JW - data detectada semana', [
        'url' => $url,
        'fecha_texto' => $fechaTexto,
        'data' => $data,
    ]);

    return $data;
}

private function normalizarTexto(string $texto): string
{
    $texto = str_replace("\xc2\xa0", ' ', $texto);
    $texto = html_entity_decode($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $texto = preg_replace('/\s+/u', ' ', $texto);

    return trim($texto);
}

private function recortarContenidoPrograma(string $texto): ?string
{
    $inicio = strpos($texto, 'REPRODUCIR EN');

    if ($inicio === false) {
        return null;
    }

    $contenido = substr($texto, $inicio);

    $fin = strpos($contenido, 'Anterior Siguiente');

    if ($fin === false) {
        $fin = strpos($contenido, 'Mostrar índice');
    }

    if ($fin !== false) {
        $contenido = substr($contenido, 0, $fin);
    }

    return trim($contenido);
}

private function seccionPorPosicion(int $posicion, $posTesoros, $posMaestros, $posVida): ?string
{
    if ($posTesoros !== false && $posicion >= $posTesoros && ($posMaestros === false || $posicion < $posMaestros)) {
        return 'tesoros';
    }

    if ($posMaestros !== false && $posicion >= $posMaestros && ($posVida === false || $posicion < $posVida)) {
        return 'maestros';
    }

    if ($posVida !== false && $posicion >= $posVida) {
        return 'vida';
    }

    return null;
}

private function tipoAsignacionPorParte(string $seccion, int $numero, string $titulo): ?string
{
    if ($seccion === 'tesoros' && $numero === 1) {
        return 'tesoro';
    }

    if ($seccion === 'tesoros' && $numero === 2) {
        return 'perlas';
    }

    if ($seccion === 'tesoros' && $numero === 3) {
        return 'lectura_biblia';
    }

    if ($seccion === 'maestros') {
        return 'maestro_estudiante';
    }

    if ($seccion === 'vida' && str_contains(mb_strtolower($titulo), 'estudio bíblico de la congregación')) {
        return 'estudio_conductor';
    }

    if ($seccion === 'vida') {
        return 'vida_cristiana';
    }

    return null;
}

    private function crearActualizarPartes(VidaMinisterio $programa, array $partesImportadas): void
    {

    if (!$programa->asignaciones()->exists()) {
        $programa->partes()->delete();
    } else {
        $programa->partes()
            ->whereDoesntHave('asignaciones')
            ->delete();
    }

        $partes = [
            [
                'seccion' => 'encabezado',
                'tipo_asignacion' => 'presidente',
                'titulo' => 'Presidente',
                'duracion_minutos' => null,
                'orden' => 10,
            ],
            [
                'seccion' => 'encabezado',
                'tipo_asignacion' => 'ayudante_auditorio',
                'titulo' => 'Ayudante auditorio principal',
                'duracion_minutos' => null,
                'orden' => 20,
            ],
            [
                'seccion' => 'encabezado',
                'tipo_asignacion' => 'consejero_auxiliar',
                'titulo' => 'Consejero sala auxiliar',
                'duracion_minutos' => null,
                'orden' => 30,
            ],
            [
                'seccion' => 'encabezado',
                'tipo_asignacion' => 'ayudante_auxiliar',
                'titulo' => 'Ayudante sala auxiliar',
                'duracion_minutos' => null,
                'orden' => 40,
            ],
            [
                'seccion' => 'encabezado',
                'tipo_asignacion' => 'oracion',
                'titulo' => 'Canción, oración y palabras de introducción',
                'duracion_minutos' => 6,
                'orden' => 50,
            ],
        ];

        $ordenes = [
            'tesoros' => 100,
            'maestros' => 200,
            'vida' => 300,
            'final' => 500,
        ];

        foreach ($partesImportadas as $parteImportada) {
            $seccion = $parteImportada['seccion'];
            $orden = $ordenes[$seccion] ?? 800;

            $partes[] = [
                'seccion' => $seccion,
                'tipo_asignacion' => $parteImportada['tipo_asignacion'],
                'titulo' => $parteImportada['titulo'],
                'duracion_minutos' => $parteImportada['duracion_minutos'],
                'orden' => $orden,
            ];

            $ordenes[$seccion] = $orden + 10;
        }

        foreach ($partes as $parte) {
            VidaMinisterioParte::updateOrCreate(
                [
                    'vida_ministerio_id' => $programa->id,
                    'seccion' => $parte['seccion'],
                    'tipo_asignacion' => $parte['tipo_asignacion'],
                    'orden' => $parte['orden'],
                ],
                [
                    'congregacion_id' => $programa->congregacion_id,
                    'titulo' => $parte['titulo'],
                    'duracion_minutos' => $parte['duracion_minutos'],
                ]
            );
        }

        Log::info('IMPORTAR JW - partes guardadas', [
            'programa_id' => $programa->id,
            'cantidad' => count($partes),
            'partes' => $partes,
        ]);
    }

    private function extraerLineas(Crawler $crawler)
    {
        $lineas = collect();

        try {
            $textoBody = $crawler->filter('body')->text("\n");

            $lineas = collect(preg_split('/\R+/', $textoBody))
                ->map(fn ($linea) => trim(preg_replace('/\s+/', ' ', $linea)))
                ->filter()
                ->values();
        } catch (\Throwable $e) {
            Log::warning('IMPORTAR JW - error leyendo body', [
                'error' => $e->getMessage(),
            ]);
        }

        if ($lineas->count() >= 20) {
            return $lineas;
        }

        $fallback = collect();

        $crawler->filter('h1,h2,h3,h4,h5,p,li,span,div')->each(function (Crawler $node) use (&$fallback) {
            $texto = trim(preg_replace('/\s+/', ' ', $node->text('')));

            if ($texto === '') {
                return;
            }

            $fallback->push($texto);
        });

        return $fallback
            ->filter()
            ->values();
    }

    private function detectarDuracion($lineas, int $index): ?int
    {
        for ($i = $index; $i <= $index + 3; $i++) {
            $linea = $lineas[$i] ?? '';

            $duracion = $this->detectarDuracionEnLinea($linea);

            if ($duracion !== null) {
                return $duracion;
            }
        }

        return null;
    }

    private function detectarDuracionEnLinea(string $linea): ?int
    {
        if (preg_match('/\((\d+)\s+mins?\.\)/iu', $linea, $m)) {
            return (int) $m[1];
        }

        return null;
    }

private function fechaInicioSemana(string $texto, int $anio): string
{
    $meses = [
        'enero' => 1,
        'febrero' => 2,
        'marzo' => 3,
        'abril' => 4,
        'mayo' => 5,
        'junio' => 6,
        'julio' => 7,
        'agosto' => 8,
        'septiembre' => 9,
        'octubre' => 10,
        'noviembre' => 11,
        'diciembre' => 12,
    ];

    $texto = mb_strtolower(trim($texto));

    if (preg_match('/(\d{1,2})-\d{1,2}\s+de\s+([a-záéíóúñ]+)/iu', $texto, $m)) {
        return Carbon::create($anio, $meses[$m[2]] ?? 1, (int) $m[1])->toDateString();
    }

    if (preg_match('/(\d{1,2})\s+de\s+([a-záéíóúñ]+)\s+a\s+\d{1,2}\s+de\s+[a-záéíóúñ]+/iu', $texto, $m)) {
        return Carbon::create($anio, $meses[$m[2]] ?? 1, (int) $m[1])->toDateString();
    }

    throw new \RuntimeException('No se pudo convertir la fecha de semana: ' . $texto);
}

    private function esTextoSemana(string $texto): bool
    {
        $texto = trim(mb_strtolower($texto));

        return (bool) preg_match(
            '/^\d{1,2}-\d{1,2}\s+de\s+[a-záéíóúñ]+$/iu',
            $texto
        ) || (bool) preg_match(
            '/^\d{1,2}\s+de\s+[a-záéíóúñ]+\s+a\s+\d{1,2}\s+de\s+[a-záéíóúñ]+$/iu',
            $texto
        );
    }

    private function pareceLecturaSemanal(string $linea): bool
    {
        $linea = trim($linea);

        if (preg_match('/canción|oración|leer en|guía de actividades|anterior|siguiente/iu', $linea)) {
            return false;
        }

        return (bool) preg_match('/^[A-ZÁÉÍÓÚÑ0-9,\s]+$/u', $linea);
    }

    private function slugPeriodo(string $periodo, int $anio): string
    {
        return match ($periodo) {
            'enero' => "enero-febrero-{$anio}-mwb",
            'marzo' => "marzo-abril-{$anio}-mwb",
            'mayo' => "mayo-junio-{$anio}-mwb",
            'julio' => "julio-agosto-{$anio}-mwb",
            'septiembre' => "septiembre-octubre-{$anio}-mwb",
            'noviembre' => "noviembre-diciembre-{$anio}-mwb",
            default => throw new \InvalidArgumentException('Período no válido: ' . $periodo),
        };
    }

    private function normalizarUrl(string $href): string
    {
        if (str_starts_with($href, 'http')) {
            return $href;
        }

        if (str_starts_with($href, '/')) {
            return $this->baseUrl . $href;
        }

        return $this->baseUrl . '/' . $href;
    }


private function extraerPartesPrograma(string $contenido, string $url, $posTesoros, $posMaestros, $posVida): array
{
    $partes = [];
    $desde = 0;

    for ($numeroEsperado = 1; $numeroEsperado <= 20; $numeroEsperado++) {
        $matchInicio = $this->buscarInicioParte($contenido, $numeroEsperado, $desde);

        if (!$matchInicio) {
            break;
        }

        $inicioParte = $matchInicio['pos'];
        $textoDesdeParte = substr($contenido, $inicioParte);

        if (!preg_match(
            '/^' . $numeroEsperado . '\.\s+(.+?)\s+\((\d+)\s+min(?:s)?\.\)/iu',
            $textoDesdeParte,
            $m
        )) {
            $desde = $inicioParte + strlen((string) $numeroEsperado) + 1;
            continue;
        }

        $titulo = $this->limpiarTituloImportado($m[1]);
        $duracion = (int) $m[2];

        if (mb_strtolower($titulo) === 'palabras de conclusión') {
            $desde = $inicioParte + strlen($m[0]);
            continue;
        }

        $seccion = $this->seccionPorPosicion($inicioParte, $posTesoros, $posMaestros, $posVida);

        if (!$seccion) {
            $desde = $inicioParte + strlen($m[0]);
            continue;
        }

        $tipoAsignacion = $this->tipoAsignacionPorParte($seccion, $numeroEsperado, $titulo);

        if (!$tipoAsignacion) {
            Log::warning('IMPORTAR JW - parte omitida sin tipo', [
                'url' => $url,
                'numero' => $numeroEsperado,
                'titulo' => $titulo,
                'seccion' => $seccion,
                'posicion' => $inicioParte,
            ]);

            $desde = $inicioParte + strlen($m[0]);
            continue;
        }

        $partes[] = [
            'seccion' => $seccion,
            'tipo_asignacion' => $tipoAsignacion,
            'titulo' => $titulo,
            'duracion_minutos' => $duracion,
        ];

        $desde = $inicioParte + strlen($m[0]);
    }

    return $partes;
}

private function buscarInicioParte(string $contenido, int $numero, int $desde): ?array
{
    $patron = '/(?<![\pL\pN:])' . $numero . '\.\s+/u';

    if (!preg_match($patron, $contenido, $m, PREG_OFFSET_CAPTURE, $desde)) {
        return null;
    }

    return [
        'texto' => $m[0][0],
        'pos' => $m[0][1],
    ];
}

private function limpiarTituloImportado(string $titulo): string
{
    $titulo = trim(preg_replace('/\s+/u', ' ', $titulo));

    $cortes = [
        ' DE CASA EN CASA.',
        ' PREDICACIÓN INFORMAL.',
        ' lmd ',
        ' th ',
        ' Título:',
        ' Tema:',
        ' Análisis con el auditorio.',
    ];

    foreach ($cortes as $corte) {
        $pos = mb_stripos($titulo, $corte);

        if ($pos !== false) {
            $titulo = mb_substr($titulo, 0, $pos);
        }
    }

    return trim($titulo);
}


}