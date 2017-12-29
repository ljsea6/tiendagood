@extends('templates.dash')

@section('titulo', 'Listado de liquidaciones')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de liquidaciones</div>
            <div class="panel-body">
                {!! Alert::render() !!}
                {{--<input type="button" class="btn btn-danger" id="update" value="Actualizar">--}}
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "asc" ]]' id="tabla_liquidaciones" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th style="text-align: center">Fecha</th>
                            <th style="text-align: center">Transferencia/Cheque</th>
                            <th style="text-align: center">Bono</th>
                            @if ($prime_val == 'si')     
                            <th style="text-align: center">Comisión antes de descuentos</th>
                            <th style="text-align: center">Retefuente</th>
                            <th style="text-align: center">Rete ICA</th>
                            <th style="text-align: center">Prime</th>
                            <th style="text-align: center">IVA Prime</th>
                            <th style="text-align: center">Transferencia</th>
                            <th style="text-align: center">Extractos</th>
                            <th style="text-align: center">Administrativos</th>
                            <th style="text-align: center">Comisión con descuentos</th>
                            @else
                            <th style="text-align: center">Comisión</th>
                            @endif
                            <th style="text-align: center">Estado</th>
                            <th style="text-align: center">Extractos</th>
                           <!-- <th style="text-align: center">Acción</th> -->
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
@push('scripts')
    <script>

        $(function() {
            var table = $('#tabla_liquidaciones').DataTable({

                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('admin.liquidaciones.data')}}',
                columns: [
                    { data: 'date', name: 'date', orderable: true, searchable: true },
                    { data: 'consignacion', name: 'consignacion', orderable: true, searchable: true },
                    { data: 'bono', name: 'bono', orderable: true, searchable: true },
                    @if ($prime_val == 'si')     
                    { data: 'total', name: 'total', orderable: true, searchable: true },
                    { data: 'rete_fuente', name: 'rete_fuente', orderable: true, searchable: true },
                    { data: 'rete_ica', name: 'rete_ica', orderable: true, searchable: true },
                    { data: 'prime', name: 'prime', orderable: true, searchable: true },
                    { data: 'prime_iva', name: 'prime_iva', orderable: true, searchable: true },
                    { data: 'transferencia', name: 'transferencia', orderable: true, searchable: true },
                    { data: 'extracto', name: 'extracto', orderable: true, searchable: true },
                    { data: 'administrativo', name: 'administrativo', orderable: true, searchable: true },
                    { data: 'total_paga', name: 'total_paga', orderable: true, searchable: true },                    
                            @else
                            { data: 'total_paga', name: 'total_paga', orderable: true, searchable: true },    
                            @endif
                    { data: 'estado', name: 'estado', orderable: true, searchable: true },
                    { data: 'extractos', name: 'extracto', orderable: true, searchable: true },
                   // { data: 'edit', name: 'edit', orderable: true, searchable: false}
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                }
            });

        });

    </script>
@endpush