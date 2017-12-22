@extends('templates.dash')

@section('titulo', 'Listado de productos')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de liquidaciones</div>
            <div class="panel-body">
                {!! Alert::render() !!}
                {{--<input type="button" class="btn btn-danger" id="update" value="Actualizar">--}}
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "asc" ]]' id="tabla_liquidaciones_general" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th style="text-align: center">#</th>
                            <th style="text-align: center">Usuario</th>
                            <th style="text-align: center">Fecha Inicio</th>
                            <th style="text-align: center">Fecha Final</th>
                            <th style="text-align: center">Fecha liquidacion</th> 
                            <th style="text-align: center"></th> 
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
            var table = $('#tabla_liquidaciones_general').DataTable({
 
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('liquidacion.liquidaciones_general_datos')}}',
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: true },
                    { data: 'usuario_id', name: 'usuario_id', orderable: true, searchable: true },
                    { data: 'fecha_inicio', name: 'fecha_inicio', orderable: true, searchable: true },
                    { data: 'fecha_final', name: 'fecha_final', orderable: true, searchable: true },
                    { data: 'fecha_liquidacion', name: 'fecha_liquidacion', orderable: true, searchable: true },
                    { data: 'excel', name: 'excel', orderable: true, searchable: true }, 
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                }
            });

        });

    </script>
@endpush