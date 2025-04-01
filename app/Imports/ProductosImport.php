<?php 
namespace App\Imports;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Actividad;
use App\Models\ProductoServicio;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductosImport implements ToModel, WithMapping, WithStartRow
{
    public function map($row): array
    {
        // Utilizamos 'null' para los valores que puedan estar vacíos
        return [
            'codigo' => $row[0] ?? null,
            'descripcion' => $row[1], // La descripción no debe ser nula
            'actividad_sin' => $row[2] ?? null,
            'codigo_producto' => $row[3] ?? null,
            'unidad_medida' => $row[4] ?? null,
            'categoria' => $row[5] ?? null,
            'proveedor' => $row[6] ?? null,
            'compra' => $row[7] ?? null,
            'venta' => $row[8], // El precio de venta no debe ser nulo
            'stock' => $row[9] ?? 0, // Asumimos que el stock puede ser 0 si está vacío
        ];
    }


public function model(array $row)  // ESTE FUCNIONA ACTUALIZANDO SI TIENE MAS DATOS 
{
    // Asegurarse de que la descripción no sea nula.
    if (empty($row['descripcion'])) {
        Log::info('La descripción del producto no puede ser nula.', $row);
        return null;
    }

    // Buscar por las entidades relacionadas sin crearlas si no existen.
    $actividad = Actividad::where('codigo_caeb', $row['actividad_sin'])->first();
    $productoServicio = ProductoServicio::where('codigo_producto', $row['codigo_producto'])->first();
    $unidadMedida = UnidadMedida::where('descripcion', $row['unidad_medida'])->first();

    // Si alguna de las entidades requeridas no se encuentra, se establece en null.
    $actividadId = $actividad ? $actividad->id : null;
    $productoServicioId = $productoServicio ? $productoServicio->id : null;
    $unidadMedidaId = $unidadMedida ? $unidadMedida->id : null;

    // Verificar si el producto ya existe.
    $productoExistente = Producto::where('descripcion', $row['descripcion'])
                                 ->orWhere('codigo_producto', $row['codigo'])->first();

    // Si el producto ya existe, actualizar los campos del producto existente.
    if ($productoExistente) {
        $categoriaId = null;
        if (!empty($row['categoria'])) {
            $categoria = Categoria::firstOrCreate(['nombre' => $row['categoria']]);
            $categoriaId = $categoria->id;
        }

        $proveedorId = null;
        if (!empty($row['proveedor'])) {
            $proveedor = Proveedor::firstOrCreate(['nombre' => $row['proveedor']]);
            $proveedorId = $proveedor->id;
        }

        $productoExistente->fill([
            'codigo_producto' => $row['codigo'],
            'actividad_id' => $actividadId,
            'producto_servicio_id' => $productoServicioId,
            'unidad_medida_id' => $unidadMedidaId,
            'categoria_id' => $categoriaId,
            'proveedor_id' => $proveedorId,
            'precio_compra' => $row['compra'],
            'precio_venta' => $row['venta'],
            'stock' => $row['stock'],
        ])->save();

        Log::info('Producto existente actualizado', ['descripcion' => $row['descripcion']]);
        return $productoExistente; // Retornar el producto existente actualizado.
    } else {
        // Si el producto no existe, crear uno nuevo.
        $categoriaId = null;
        if (!empty($row['categoria'])) {
            $categoria = Categoria::firstOrCreate(['nombre' => $row['categoria']]);
            $categoriaId = $categoria->id;
        }

        $proveedorId = null;
        if (!empty($row['proveedor'])) {
            $proveedor = Proveedor::firstOrCreate(['nombre' => $row['proveedor']]);
            $proveedorId = $proveedor->id;
        }

        // Crear el nuevo producto con los datos existentes y las referencias correctas.
        return new Producto([
            'codigo_producto' => $row['codigo'],
            'descripcion' => $row['descripcion'],
            'actividad_id' => $actividadId,
            'producto_servicio_id' => $productoServicioId,
            'unidad_medida_id' => $unidadMedidaId,
            'categoria_id' => $categoriaId,
            'proveedor_id' => $proveedorId,
            'precio_compra' => $row['compra'],
            'precio_venta' => $row['venta'],
            'stock' => $row['stock'],
        ]);
    }
}



    public function startRow(): int
    {
        return 2; // Comienza a importar desde la segunda fila, omitiendo la primera fila de encabezados
    }
}
