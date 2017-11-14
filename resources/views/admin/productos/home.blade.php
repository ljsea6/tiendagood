@extends('templates.dash')

@section('titulo', 'Listado de productos')

@section('content')
    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Listado de productos</div>
            <div class="panel-body">
                {!! Alert::render() !!}
                {{--<input type="button" class="btn btn-danger" id="update" value="Actualizar">--}}
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "asc" ]]' id="tabla_productos" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Título</th>
                            <th>Tipo Producto</th>
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
        var table = $('#tabla_productos').DataTable({
      
            dom: 'Bfrtip',
            buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
               responsive: true,
               processing: true,
               serverSide: false,
               deferRender: true,
               pagingType: "full_numbers",
               ajax: '{{route('admin.products.data')}}',
               columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'title', name: 'title', orderable: true, searchable: true },
                    { data: 'tipo_producto', name: 'tipo_producto', orderable: true, searchable: true}

                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                }
        });

       /* $('#update').click( function() {
            var data = table.$('input, select').serialize();

            $.ajax({
                url: "",
                data: { value: data, _token: ''},
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    alert(data.data);
                },
                error : function(xhr, status) {
                    alert('Disculpe, existió un problema');
                },

            });

            table._fnAjaxUpdate();
            return false;
        } );*/
    });

</script>
@endpush