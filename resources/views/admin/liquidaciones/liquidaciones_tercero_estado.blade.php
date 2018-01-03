@extends('templates.dash')

@section('titulo', 'Good')

@section('content')
<input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Cambio de estado de las liquidaciones</div>
            <div class="panel-body">
                {!! Alert::render() !!}
                {{--<input type="button" class="btn btn-danger" id="update" value="Actualizar">--}}
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "desc" ]]' id="liquidaciones_terceros_estados_datos" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th style="text-align: left">Identificación</th>
                            <th style="text-align: left">Nombres</th>
                            <th style="text-align: left">Apellidos</th>
                            <th style="text-align: left">Teléfono</th>
                            <th style="text-align: left">Email</th> 
                            <th style="text-align: left">Prime</th> 
                            <th style="text-align: left">Estado</th> 
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

            var table = $('#liquidaciones_terceros_estados_datos').DataTable({
 
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",
                ajax: '{{route('liquidacion.liquidaciones_terceros_estados_datos')}}',
                columns: [
                    { data: 'identificacion', name: 'identificacion', orderable: true, searchable: true },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true },
                    { data: 'apellidos', name: 'apellidos', orderable: true, searchable: true },
                    { data: 'telefono', name: 'telefono', orderable: true, searchable: true },
                    { data: 'email', name: 'email', orderable: true, searchable: true },
                    { data: 'prime', name: 'prime', orderable: true, searchable: true },
                    { data: 'estado', name: 'estado', orderable: true, searchable: true },
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },
                drawCallback: function () {
                  $('.pendiente').hide();
                }
            });       
   });

      function cambio_estado(id, valor, tipo){

       if(valor == 87){
            $('.tercero_pendiente_'+id).hide();
       }
       else{
           $('.tercero_pendiente_'+id).show();
       }
 
          $.ajax({
                    url: '../liquidacion/cambiar_estado',
                    type: 'post',
                    data: { id: id, valor: valor, tipo: tipo, _token: $('#_token').val() },
                    dataType: 'json',
                    async:false
                });  
      }

</script>
@endpush