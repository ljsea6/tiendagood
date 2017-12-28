@extends('templates.dash')

@section('titulo', 'Good')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Extracto de comisiones del mes de {{$mes}}</div>
            <div class="panel-body">
                {!! Alert::render() !!}
                {{--<input type="button" class="btn btn-danger" id="update" value="Actualizar">--}}
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th style="text-align: left">Nombres</th>
                            <th style="text-align: left">Apellidos</th>
                            <th style="text-align: left">Fecha</th>
                            <th style="text-align: left">Orden</th>
                            <th style="text-align: right">Puntos</th>
                            <th style="text-align: right">Valor de comisión</th> 
                        </tr>
                        </thead>
                        <tbody>

                        {{--*/  $TOTAL_COMISION = 0 /*--}}

                        @foreach ($liquidaciones_detalles as $value)
                        <tr>
                            <td style="text-align: left">{{ucwords($value->nombres)}}</td>
                            <td style="text-align: left">{{ucwords($value->apellidos)}}</td>
                            <td style="text-align: left">{{$value->created_at}}</td>
                            <td style="text-align: left">{{$value->name}}</td>
                            <td style="text-align: right;">{{$value->puntos}}</td>
                            <td style="text-align: right">{{number_format($value->valor_comision)}}</td> 
                        </tr>  

                        {{--*/  $TOTAL_COMISION += $value->valor_comision /*--}}

                        @endforeach
                        <tr>
                            <td style="text-align: left"></td>
                            <td style="text-align: left"></td>
                            <td style="text-align: left"></td>
                            <td style="text-align: left"></td>
                            <td style="text-align: left"></td>
                            <td style="text-align: right; font-weight: bold;">
                              Total: {{number_format($TOTAL_COMISION)}}<br>
                              Retefuente: {{number_format($TOTAL_COMISION * $parametros->rete_fuente)}}<br>
                              Rete ICA: {{number_format($TOTAL_COMISION * $parametros->rete_ica)}}<br>
                              Prime: {{number_format($parametros->prime)}}<br>
                              IVA Prime: {{number_format($parametros->prime * $parametros->prime_iva)}}<br>
                              Transferencia: {{number_format($parametros->transferencia)}}<br>
                              Extractos: {{number_format($parametros->extracto)}}<br>
                              Administrativos: {{number_format($parametros->administrativo)}}<br>
                              Comision con descuentos: {{number_format($TOTAL_COMISION - ($TOTAL_COMISION * $parametros->rete_fuente) - ($TOTAL_COMISION * $parametros->rete_ica) - ($parametros->prime) - ($parametros->prime * $parametros->prime_iva)  - ($parametros->transferencia) - $parametros->extracto - $parametros->administrativo )}}
                            </td> 
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop
@push('scripts')
    <script>
/*
        $(function() {
            var table = $('#tabla_liquidaciones_general').DataTable({
 
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('liquidacion.liquidaciones_extracto_comisiones_datos', ['id' => $id])}}',
                columns: [
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true },
                    { data: 'apellidos', name: 'apellidos', orderable: true, searchable: true },
                    { data: 'name', name: 'name', orderable: true, searchable: true },
                    { data: 'puntos', name: 'puntos', orderable: true, searchable: true },
                    { data: 'valor_comision', name: 'valor_comision', orderable: true, searchable: true },
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                }
            });

        });
*/
    </script>
@endpush