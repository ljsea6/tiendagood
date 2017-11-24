@extends('templates.dash')

@section('titulo','Good')

@section('content')

    <section class="invoice">
        <div class="page-header no-breadcrumb font-header"><i class="fa fa-user"></i>¡Bienvenido(a) {{ currentUser()->nombre_completo }}!</div>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-xs-8">
                        <div class="panel panel-default bg-info panel-stat no-icon">
                            <div class="panel-body content-wrap">
                                <div class="value">
                                    <h2 class="font-header no-m">{{number_format($send->numero_referidos)}}</h2>
                                </div>
                                <div class="detail text-right">
                                    <div class="text-upper">Tus puntos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-xs-8">
                        <div class="panel panel-default bg-success panel-stat no-icon">
                            <div class="panel-body content-wrap">
                                <div class="value">
                                    <h2 class="font-header no-m">{{number_format($send->numero_ordenes_referidos)}}</h2>
                                </div>
                                <div class="detail text-right">
                                    <div class="text-upper">Tus compras</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-xs-8">
                        <div class="panel panel-default bg-purple panel-stat no-icon">
                            <div class="panel-body content-wrap">
                                <div class="value">
                                    <h2 class="font-header no-m">{{number_format($send->total_price_orders)}}</h2>
                                </div>
                                <div class="detail text-right">
                                    <small class="text-upper">$ Total de tus puntos</small>
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
                    @if (session('status'))
                        <div class="alert alert-info fade in col-sm-12 col-md-12 col-lg-12">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <ul>
                                <li>{{ session('status') }}</li>
                            </ul>
                        </div>
                    @endif
                    <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                        <table data-order='[[ 0, "asc" ]]' id="terceros1" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
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
                    @if (session('status'))
                        <div class="alert alert-info fade in col-sm-12 col-md-12 col-lg-12">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <ul>
                                <li>{{ session('status') }}</li>
                            </ul>
                        </div>
                    @endif
                    <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                        <table data-order='[[ 0, "asc" ]]' id="terceros2" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
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
                    @if (session('status'))
                        <div class="alert alert-info fade in col-sm-12 col-md-12 col-lg-12">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <ul>
                                <li>{{ session('status') }}</li>
                            </ul>
                        </div>
                    @endif
                    <div id="datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                        <table data-order='[[ 0, "asc" ]]' id="terceros3" class="table table-striped font-12 dataTable no-footer" role="grid" aria-describedby="datatable_info">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombres</th>
                                <th>Email</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

</section>


@endsection

@push('scripts')
    <script>
        $(document).ready(function(){

                $('#terceros1').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    pagingType: "full_numbers",
                    ajax: {
                        url: '{{route('admin.one')}}',
                        type: 'post',
                        data: function ( d ) {
                            d.level = 1;
                            d.id = '{{currentUser()->id}}'
                        }
                    },
                   columns: [
                        { data: 'id', name: 'id', orderable: true, searchable: false },
                        { data: 'nombres', name: 'nombres', orderable: true, searchable: true  },
                        { data: 'email', name: 'email', orderable: true }
                    ],
                    language: {
                        url: "{{ asset('css/Spanish.json') }}"
                    },

                });

            $('#terceros2').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",
                ajax: {
                    url: '{{route('admin.two')}}',
                    type: 'post',
                    data: function ( d ) {
                        d.level = 2;
                        d.id = '{{currentUser()->id}}'
                    }
                },
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true  },
                    { data: 'email', name: 'email', orderable: true }
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },

            });

            $('#terceros3').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                deferRender: true,
                pagingType: "full_numbers",
                ajax: {
                    url: '{{route('admin.tree')}}',
                    type: 'post',
                    data: function ( d ) {
                        d.level = 3;
                        d.id = '{{currentUser()->id}}'
                    }
                },
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: false },
                    { data: 'nombres', name: 'nombres', orderable: true, searchable: true  },
                    { data: 'email', name: 'email', orderable: true }
                ],
                language: {
                    url: "{{ asset('css/Spanish.json') }}"
                },

            });

        });



    </script>
@endpush

