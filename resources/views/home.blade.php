@extends('layouts.app')
{{-- @if(auth()->user() && auth()->user()->role === 'admin')
     @if(auth()->user() && auth()->user()->role === 'vendedor') --}}
@section('content')
<style>
    .mensual {
        height: 350px; /* Ajusta la altura según tus necesidades */
        width: 100%; /* Ajusta el ancho al 100% del contenedor */
    }
    .formaPago {
        height: 350px; /* Ajusta la altura según tus necesidades */
        width: 100%; /* Ajusta el ancho al 100% del contenedor */
    }
</style>


<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Selector de Fechas -->
           <form action="{{ route('home') }}" method="post">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-2">
                        <input type="date" class="form-control form-control-sm rounded-3" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio', now()->startOfMonth()->toDateString()) }}" />
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control form-control-sm rounded-3" id="fecha_fin" name="fecha_fin" value="{{ request('fecha_fin', now()->endOfMonth()->toDateString()) }}" />
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary btn-sm rounded-3">Filtar Datos</button>
                    </div>
                </div>
            </form> 
           
            
            
            <!-- Gráficos -->
            <div class="row">

                   
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-4 shadow-lg">
                            <div class="card-body">
                               
                                <!-- Fila para Inventario -->
                                <div class="row">
                                    <div class="col-4 text-center">
                                        <i class="fas fa-boxes widget-icon" style="font-size: 2rem; color: #ff6385c5;"></i>
                                        <h6 class="text-muted mt-2 mb-3">Inventario</h6>
                                        <h5>{{ 111 }}</h5>
                                    </div>
                                    <div class="col-4 text-center">
                                        <i class="fas fa-tags widget-icon" style="font-size: 2rem; color: #ffd771;"></i>
                                        <h6 class="text-muted mt-2 mb-3">Valor Compra</h6>
                                        <h5>Bs. {{ number_format(1, 2, ',', '.') }}</h5>
                                    </div>
                                    <div class="col-4 text-center">
                                        <i class="fas fa-tag widget-icon" style="font-size: 2rem; color: #35dda2;"></i>
                                        <h6 class="text-muted mt-2 mb-3">Valor Venta</h6>
                                        <h5>Bs. {{ number_format(111, 2, ',', '.') }}</h5>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                    

                   

 <!-- CARD 1: Total Operaciones -->
 <div class="col-md-6 col-lg-4 mb-4">
    <div class="card border-0 rounded-4 shadow-lg">
        <div class="card-body text-center">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-chart-line widget-icon" style="font-size: 2rem; color: #526cff;"></i>
                <h5 class="text-muted mb-0 ms-2" title="Total Operaciones"> Total Operaciones </h5>
            </div>
            <h3>{{ 'Bs. '.number_format(100, 2, ',', '.') }}</h3>
            <p class="text-muted mb-1"><small>Mes en curso</small></p>
            <p class="mb-0 text-muted">
                <span class="text-primary"><b>{{ 'Bs. '.number_format(1010, 2, '.', ',') }}</b></span>
                <span class="text-primary">- En el año</span>
            </p>
        </div>
        
    </div> 
</div>
                
                    <!-- CARD 2: Facturado SIN -->
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card border-0 rounded-4 shadow-lg">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <i class="fas fa-receipt widget-icon" style="font-size: 2rem; color: #35dda2;"></i>
                                    <h5 class="text-muted mb-0 ms-2" title="Facturado SIN">Facturado Sujeto a IVA <span class="badge bg-success">SIN ✓</span></h5>
                                </div>
                                <h3>Bs. {{ number_format(788, 2, '.', ',') }}</h3>
                                <p class="text-muted mb-1"><small>Mes en curso</small></p>
                                <p class="mb-0 text-muted">
                                    <span class="text-success"><b>Bs. {{ number_format(7878, 2, '.', ',') }}</b></span>
                                    <span class="text-success"> - En el año</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                
                  
               


                <!-- Gráfico Circular -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-0 rounded-4 shadow-lg">
                        <div class="card-header rounded-top-4 border-0">
                            <h5 class="card-title">Ingresos Forma Pago</h5>
                        </div>
                        <div class="card-body">
                            <div class="formaPago">
                                <canvas id="formaPago"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfico de Barras - Más grande -->
                <div class="col-md-6 col-lg-8 mb-4">
                    <div class="card border-0 rounded-4 shadow-lg">
                        <div class="card-header rounded-top-4 border-0">
                            <h5 class="card-title">Estadísticas Mensuales</h5>
                        </div>
                        <div class="card-body ">
                            <div class="mensual">
                                <canvas id="mensual"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                

               
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Prepara los datos para el gráfico de Doughnut
    var ingresosPorFormaPago = [
    { descripcion: "Efectivo", total: 12000 },
    { descripcion: "Tarjeta", total: 19000 },
    { descripcion: "Transferencia", total: 5000 }
];

    
    var ctx = document.getElementById('formaPago').getContext('2d');
    var formaPagoChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ingresosPorFormaPago.map(function(item) { return item.descripcion; }),
            datasets: [{
                label: 'Ingreso ',
                data: ingresosPorFormaPago.map(function(item) { return item.total; }),
                backgroundColor: [
                    'rgba(75, 200, 192, 0.2)',  // Verde
                    'rgba(153, 102, 255, 0.2)', // Violeta
                    'rgba(54, 162, 235, 0.2)'   // Celeste
                ],
                borderColor: [
                    'rgba(75, 200, 192, 1)',    // Verde
                    'rgba(153, 102, 255, 1)',   // Violeta
                    'rgba(54, 162, 235, 1)'     // Celeste
                ],

                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
        }
    });
    </script>


<script>
    var ctx = document.getElementById('mensual').getContext('2d');
    var datosMensuales = {
    ingresos: [1000, 2000, 3000, 4000, 5000],
    egresos: [500, 1000 ],
    diferencia: [500, 1000, 1500, 2000, 2500]
};


    var mensualChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [{
                label: 'Ingresos',
                data: datosMensuales.ingresos,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },{
                label: 'Egresos',
                data: datosMensuales.egresos,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            },{
                label: 'Proyección de Ganancias',
                data: datosMensuales.diferencia,
                type: 'line',
                fill: false,
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1.5,
                pointRadius: 5,
                tension: 0.3
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>






@endsection




