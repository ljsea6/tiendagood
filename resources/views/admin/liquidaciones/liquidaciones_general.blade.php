@extends('templates.dash')

@section('titulo', 'Good')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de liquidaciones</div>
            <div class="panel-body">
                {!! Alert::render() !!}
                {{--<input type="button" class="btn btn-danger" id="update" value="Actualizar">--}}
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table  id="tabla_liquidaciones_general" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th style="text-align: left;">#</th>
                            <th style="text-align: left">Usuario</th>
                            <th style="text-align: left">Fecha Inicio</th>
                            <th style="text-align: left">Fecha Final</th>
                            <th style="text-align: left">Fecha liquidacion</th> 
                            <th style="text-align: center;">Acciones</th> 
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
                 "order": [[ 4, 'desc' ]],
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('liquidacion.liquidaciones_general_datos')}}',
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: true },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true },
                    { data: 'fecha_inicio', name: 'fecha_inicio', orderable: true, searchable: true },
                    { data: 'fecha_final', name: 'fecha_final', orderable: true, searchable: true },
                    { data: 'fecha_liquidacion', name: 'fecha_liquidacion', orderable: true, searchable: true },
                    { data: 'excel', name: 'excel' }, 
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                }
            });

        });

    </script>
@endpush