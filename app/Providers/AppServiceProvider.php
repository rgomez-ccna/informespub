<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\{CufdService, FirmadorService, FacturaService, ProcesoFacturaSINService};


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    //Esto es necesario para que funcione la vista de ventas create 
    {
        $this->app->bind(ProcesoFacturaSINService::class, function ($app) {
            $rutaCertificado = storage_path('app/siatcert.pem');
            $rutaLlave = storage_path('app/siatkey.pem');
            $cufdService = $app->make(CufdService::class);
            $firmadorService = new FirmadorService($rutaCertificado, $rutaLlave);
            $facturaService = $app->make(FacturaService::class);

            return new ProcesoFacturaSINService($cufdService, $firmadorService, $facturaService);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
