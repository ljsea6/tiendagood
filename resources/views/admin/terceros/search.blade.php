@extends('templates.dash')

@section('titulo','Tercero - Buscar')

@section('content')

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="stepwizard">
                <div class="stepwizard-row setup-panel">
                    <div class="stepwizard-step">
                        <a href="#step-1"  class="btn btn-primary btn-circle">1</a>
                        <p>Buscar</p>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-1">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3 class="text-center">Buscar Información de usuario</h3>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <label for="email">Ingresar correo o el numero de documento de la persona</label>
                                <input id="search" name="search" type="text" class="form-control" required>
                                <br>
                                <button id="searching" class="btn btn-danger">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="stepwizard">
                <div class="setup-panel text-left">

                    <h3 class="text-center">Información de usuario</h3>
                    <img id="imagen" class="img-circle" width="150" height="150"><br>
                    <input type="hidden" id="padre_id">
                    Nombre: <span id="first_name"></span><br>
                    Apellido: <span id="last_name"></span><br>
                    Identificación: <span id="dni"></span><br>
                    Email: <span id="email"></span><br>
                    Telefono: <span id="phone"></span><br>
                    Fecha Inscripción: <span id="d"></span><br>
                </div>
            </div>
            <div class="row setup-content" id="step-1">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Nivel 1</div>
            <div class="panel-body">
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "desc" ]]' id="nivel_1" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Nivel 2</div>
            <div class="panel-body">
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "desc" ]]' id="nivel_2" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="box oculto">
        <div class="panel panel-default">
            <div class="panel-heading font-header">Nivel 3</div>
            <div class="panel-body">
                <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table data-order='[[ 0, "desc" ]]' id="nivel_3" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('scripts')

    <script type="text/javascript">


        $(function() {

            $("#padre_id").val('');

            var uno = $('#nivel_1').DataTable({

                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",

                ajax: {
                    url:'{{route('admin.terceros.index.searching.levels')}}',
                    type: 'post',
                    dataType: 'json',
                    data :function ( d ) {
                        d.padre_id = Number($("#padre_id").val());
                        d.level = 1;
                    },
                    /*success: function (data) {
                        if(data) {
                            console.log('Se ha buscado bien.');
                        }
                    }*/
                },
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: true },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true },
                    { data: 'apellidos', name: 'apellidos', orderable: true, searchable: true },
                    { data: 'email', name: 'email', orderable: true, searchable: true }

                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                }
            });

            var dos = $('#nivel_2').DataTable({

                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",

                ajax: {
                    url:'{{route('admin.terceros.index.searching.levels')}}',
                    type: 'post',
                    dataType: 'json',
                    data :function ( d ) {
                        d.padre_id = Number($("#padre_id").val());
                        d.level = 2;
                    },
                    /*success: function (data) {
                        if(data) {
                            console.log('Se ha buscado bien.');
                        }
                    }*/
                },
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: true },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true },
                    { data: 'apellidos', name: 'apellidos', orderable: true, searchable: true },
                    { data: 'email', name: 'email', orderable: true, searchable: true }

                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                }
            });

            var tres = $('#nivel_3').DataTable({

                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",

                ajax: {
                    url:'{{route('admin.terceros.index.searching.levels')}}',
                    type: 'post',
                    dataType: 'json',
                    data :function ( d ) {
                            d.padre_id = Number($("#padre_id").val());
                            d.level = 3;
                    },
                    /*success: function (data) {
                        if(data) {
                            console.log('Se ha buscado bien.');
                        }
                    }*/
                },
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: true },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true },
                    { data: 'apellidos', name: 'apellidos', orderable: true, searchable: true },
                    { data: 'email', name: 'email', orderable: true, searchable: true }

                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                }
            });


            function isEmpty(value) {

                if (value.length == 0) {

                    return false
                }

                return true;
            }

            function search() {

                var value = $('#search').val();

                if(isEmpty(value)) {

                    console.log(value);

                    var date = JSON.parse( $.ajax({
                        url: '{{route('admin.terceros.index.searching.data')}}',
                        type: 'post',
                        data: {search: value},
                        dataType: 'json',
                        async:false,
                        success: function (json) {
                            console.log(json);
                            return json;
                        },

                        error : function(xhr, status) {
                            console.log('Disculpe, existió un problema');
                        },

                        complete : function(xhr, status) {
                            console.log('Petición realizada');
                        }
                    }).responseText);


                    if (date.msg) {
                        swal(
                            'Lo sentimos, no se encontró información. Verifique los datos.',
                            '¡Ok!',
                            'success'
                        );
                    }

                    if (date.info) {
                        swal(
                            'Datos encontrados',
                            '¡Ok!',
                            'success'
                        );

                        $("#padre_id").val(date.info.id);
                        $('#first_name').html('' + date.info.nombres);
                        $('#last_name').html('' + date.info.apellidos);
                        $('#email').html('' + date.info.email);
                        $('#dni').html('' + date.info.identificacion);
                        $('#phone').html('' + date.info.telefono);
                        $('#d').html('' + date.info.created_at);

                        $( "#imagen" ).attr('src', "{{url()}}/" + date.info.avatar);

                        uno.ajax.reload();
                        dos.ajax.reload();
                        tres.ajax.reload();

                    }

                } else {

                    uno.ajax.reload();
                    dos.ajax.reload();
                    tres.ajax.reload();

                    swal(
                        'Por favor, ingrese un correo o el número de documento a buscar...',
                        '¡Error!',
                        'error'
                    );
                }
            }

            $( "#searching" ).click(function() {
                search();
            });
        });








    </script>
@stop
