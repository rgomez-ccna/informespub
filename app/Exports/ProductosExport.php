<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductosExport implements FromQuery, WithHeadings
{
    use Exportable;

    public function query()
    {
        return Producto::query()
        ->leftJoin('proveedors', 'productos.proveedor_id', '=', 'proveedors.id')
        ->leftJoin('categorias', 'productos.categoria_id', '=', 'categorias.id')
        ->leftJoin('actividads', 'productos.actividad_id', '=', 'actividads.id')
        ->leftJoin('unidad_medidas', 'productos.unidad_medida_id', '=', 'unidad_medidas.id')
        ->leftJoin('producto_servicios', 'productos.producto_servicio_id', '=', 'producto_servicios.id')
        ->select(
                'productos.codigo_producto',
                'productos.descripcion',

                'actividads.codigo_caeb as actividad_caeb',
                'producto_servicios.codigo_producto as codigo_producto_sin',
                'unidad_medidas.descripcion as unidad_medida',

                'categorias.nombre as categoria',
                'proveedors.nombre as proveedor',

                'productos.precio_compra',
                'productos.precio_venta',
                'productos.stock'
            );
    }
    
    public function headings(): array
    {
        return [
            'Código Producto',
            'Descripción',

            'Actividad SIN',
            'Código Producto SIN',
            'Unidad de Medida SIN',

            'Categoría',
            'Proveedor',
            
            'Precio de Compra',
            'Precio de Venta',
            'Stock',
        ];
    }
    
}
