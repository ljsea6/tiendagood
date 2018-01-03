@extends('templates.dash')

@section('titulo', 'Listado de liquidaciones')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de liquidaciones</div>
            <div class="panel-body"> 
           <span style="color: #FF5733; font-size: 16px">Nota: sdfsdfdsf ds fds f sd fsd</span>
                <br>
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "asc" ]]' id="tabla_liquidaciones" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th style="text-align: center">Fecha de inicio y final de corte</th> 
                            <th style="text-align: center">Comisión antes de descuentos</th>
                            <th style="text-align: center">Descuentos</th>
                            <th style="text-align: center">Comisión total</th> 
                            <th style="text-align: center">Transferencia</th>
                            <th style="text-align: center">Bono 30%</th>
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
 
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('admin.liquidaciones.data')}}',
                columns: [  
                    { data: 'date', name: 'date', orderable: true, searchable: true }, 
                    { data: 'total', name: 'total', orderable: true, searchable: true },
                    { data: 'rete_fuente', name: 'rete_fuente', orderable: true, searchable: true },
                    { data: 'total_paga', name: 'total_paga', orderable: true, searchable: true },     
                    { data: 'consignacion', name: 'consignacion', orderable: true, searchable: true },
                    { data: 'bono', name: 'bono', orderable: true, searchable: true },
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