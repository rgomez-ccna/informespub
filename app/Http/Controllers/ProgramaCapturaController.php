<?php

namespace App\Http\Controllers;

use App\Models\ProgramaCaptura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgramaCapturaController extends Controller
{
    public function index(Request $req)
    {
        $desde = $req->input('desde') ?? now()->startOfMonth()->toDateString();
        $hasta = $req->input('hasta') ?? now()->copy()->addMonths(2)->endOfMonth()->toDateString();

        $items = ProgramaCaptura::whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->get();

        return view('tablero.programa_capturas.index', compact('items', 'desde', 'hasta'));
    }

    public function create()
    {
        $item = null;
        return view('tablero.programa_capturas.form', compact('item'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'fecha'        => 'required|date',
            'imagenes.*'   => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'nota'         => 'nullable|string',
        ]);

        $paths = [];

        // archivos
        if ($r->hasFile('imagenes')) {
            foreach ($r->file('imagenes') as $f) {
                if ($f->isValid()) {
                   // $paths[] = $f->store('public/vidaministerio/capturas');
                   // $paths[] = $f->store('vidaministerio/capturas', 'public');
                    // usá esto:
                    $filename = $f->hashName();
                    $f->storeAs('vidaministerio/capturas', $filename, 'public');
                    $paths[] = 'storage/vidaministerio/capturas/' . $filename;

                }
            }
        }

        // nota como pseudo-imagen (sin tocar DB)
        if (trim($r->input('nota', '')) !== '') {
            $paths[] = '::text::' . trim($r->input('nota'));
        }

        ProgramaCaptura::create([
            'fecha'    => $r->fecha,
            'imagenes' => $paths,
        ]);

        return redirect()->route('tablero.programa-capturas.index')->with('ok', 'Capturas guardadas.');
    }

    public function edit($id)
    {
        $item = ProgramaCaptura::findOrFail($id);
        return view('tablero.programa_capturas.form', compact('item'));
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'fecha'        => 'nullable|date',
            'imagenes.*'   => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'nota'         => 'nullable|string',
        ]);

        $item = ProgramaCaptura::findOrFail($id);

        $nuevas = [];
        if ($r->hasFile('imagenes')) {
            foreach ($r->file('imagenes') as $f) {
                if ($f->isValid()) {
                    $nuevas[] = $f->store('public/vidaministerio/capturas');
                }
            }
        }

        // merge con existentes
        $merged = array_values(array_filter(array_merge($item->imagenes ?? [], $nuevas)));

        // nota (si viene) se agrega al final
        if (trim($r->input('nota', '')) !== '') {
            $merged[] = '::text::' . trim($r->input('nota'));
        }

        $item->update([
            'fecha'    => $r->filled('fecha') ? $r->fecha : $item->fecha,
            'imagenes' => $merged,
        ]);

        return back()->with('ok', 'Actualizado.');
    }

    public function destroy($id)
    {
        $item = ProgramaCaptura::findOrFail($id);

        // borrar solo archivos reales
        foreach (($item->imagenes ?? []) as $val) {
            if (is_string($val) && Str::startsWith($val, 'public/')) {
                Storage::delete($val);
            }
        }

        $item->delete();
        return back()->with('ok', 'Eliminado.');
    }

    public function destroyImagen($id, $idx)
    {
        $item = ProgramaCaptura::findOrFail($id);
        $arr  = $item->imagenes ?? [];

        if (!isset($arr[$idx])) {
            return back()->with('error', 'Imagen no encontrada');
        }

        $val = $arr[$idx];

        // si es archivo real, borrar del disco
        if (is_string($val) && Str::startsWith($val, 'public/')) {
            Storage::delete($val);
        }
        // si es nota (::text::...), solo quitar del array

        unset($arr[$idx]);
        $item->imagenes = array_values($arr);
        $item->save();

        return back()->with('ok', 'Elemento eliminado.');
    }
}
