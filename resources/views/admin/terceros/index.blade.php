@extends('templates.dash')

@section('titulo', 'Listado de Terceros')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado Referidos</div>
            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-info fade in col-sm-12 col-md-12 col-lg-12">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <ul>
                            <li>{{ session('status') }}</li>  
                        </ul>
                    </div>
                @endif
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "asc" ]]' id="terceros" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Identificacón</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Nivel 1</th>
                            <th>Nivel 2</th>
                            <th>Nivel 3</th>
                            <th>Mis Puntos</th>
                            <th>Puntos Vendidos</th>
                            <th style="text-align: center;">Ver redes</th>
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
    $(document).ready(function(){
        $(function() {
            $('#terceros').DataTable({
               //dom: 'Bfrtip',
               //buttons: [
                //    'copy', 'csv', 'excel', 'pdf', 'print'
               // ],
               responsive: true,
               processing: true,
               serverSide: true,
               deferRender: true,
               pagingType: "full_numbers",
               ajax: '{{route('admin.terceros.data')}}',
               columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'identificacion', name: 'identificacion', orderable: true, searchable: true },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true  },
                    { data: 'apellidos', name: 'apellidos', orderable: true },
                    { data: 'nivel_1', name: 'nivel_1', orderable: true },
                    { data: 'nivel_2', name: 'nivel_2', orderable: true },
                    { data: 'nivel_3', name: 'nivel_3', orderable: true },
                    { data: 'mispuntos', name: 'mispuntos', orderable: true },
                    { data: 'puntos_vendidos', name: 'puntos_vendidos', orderable: true },
                    { data: 'action', name: 'red', orderable: false, searchable: false, className: "centrar"},
                    { data: 'edit', name: 'editar', orderable: false, searchable: false, className: "centrar"}
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },
         
            });

        });
    });
</script>
@endpush