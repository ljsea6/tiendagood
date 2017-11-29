@extends('templates.dash')

@section('titulo','Good')

@section('content')
    <style type="text/css"> .puntos{ font-size: 20px; font-weight: bold; } </style>
    <section class="invoice">
        <div class="page-header no-breadcrumb font-header"><i class="fa fa-user"></i>¡Bienvenido(a) {{ ucwords(currentUser()->nombre_completo) }}!</div>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="panel panel-default bg-info panel-stat no-icon">
                            <div class="panel-body content-wrap">
                                <div class="value">
                                    <h2 class="font-header no-m">{{number_format($send->mispuntos)}}</h2>
                                </div>
                                <div class="detail text-right">
                                    <div class="text-upper">Mis puntos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel panel-default bg-success panel-stat no-icon">
                            <div class="panel-body content-wrap">
                                <div class="value">
                                    <h2 class="font-header no-m">{{number_format($send->puntos_vendidos)}}</h2>
                                </div>
                                <div class="detail text-right">
                                    <div class="text-upper">Mis puntos vendidos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

      {{--  <div class="panel no-b col-md-6">
            <div class="panel-body no-p">
                <ul class="list-unstyled list-float list-bordered three-col clearfix b-t no-m-b">
                    <li class="p-10 text-center font-16">
                        <span class="badge">1</span>
                        <div>Nivel</div>
                    </li>
                    <li class="p-10 text-center">
                        <div class="font-12">1,282</div>
                        <div>Puntos</div>
                    </li>
                    <li class="p-10 text-center font-16">
                        <div class="font-16">{{number_format($uno)}} <i class="fa fa-user"></i></div>

                    </li>
                </ul>
                <ul class="list-unstyled list-float list-bordered three-col clearfix b-t no-m-b">
                    <li class="p-10 text-center font-12">
                        <span class="badge">$ 50</span> x punto
                    </li>
                    <li class="p-10 text-center font-12">
                        <div class="font-12">$ {{number_format($uno)}}</div>
                    </li>
                </ul>
                <ul class="list-unstyled list-float list-bordered three-col clearfix b-t no-m-b">
                    <li class="p-10 text-center font-12">
                        <span class="badge">2</span>
                        <div>Nivel</div>
                    </li>
                    <li class="p-10 text-center">
                        <div class="font-12">1,282</div>
                        <div>Puntos</div>
                    </li>
                    <li class="p-10 text-center font-12">
                        <div class="font-12">{{number_format($dos)}}</div>
                        <i class="fa fa-user"></i>
                    </li>
                </ul>
                <ul class="list-unstyled list-float list-bordered three-col clearfix b-t no-m-b">
                    <li class="p-10 text-center font-12">
                        <span class="badge">3</span>
                        <div>Nivel</div>
                    </li>
                    <li class="p-10 text-center">
                        <div class="font-12">1,282</div>
                        <div>Puntos</div>
                    </li>
                    <li class="p-10 text-center font-12">
                        <div class="font-12">{{number_format($tres)}}</div>
                        <i class="fa fa-user"></i>
                    </li>
                </ul>
            </div>

        </div><!-- /.panel -->

        <div class="panel no-b col-md-6">
            <div class="panel-body no-p">
                <ul class="list-unstyled list-float list-bordered three-col clearfix b-t no-m-b">
                    <li class="p-10 text-center font-12">
                        <span class="badge">$ 50</span> x punto
                    </li>
                    <li class="p-10 text-center font-12">
                        <div class="font-12">$ {{number_format($uno)}}</div>
                    </li>
                </ul>
                <ul class="list-unstyled list-float list-bordered three-col clearfix b-t no-m-b">
                    <li class="p-10 text-center font-12">
                        <span class="badge">100</span>
                        <div>Comisiones</div>
                    </li>
                    <li class="p-10 text-center">
                        <div class="font-12">100 x 1,282</div>
                        <div>Puntos x Comisiones</div>
                    </li>
                    <li class="p-10 text-center font-12">
                        <div class="font-12">{{number_format($dos)}}</div>
                        <i class="fa fa-money"></i>
                    </li>
                </ul>
                <ul class="list-unstyled list-float list-bordered three-col clearfix b-t no-m-b">
                    <li class="p-10 text-center font-12">
                        <span class="badge">150</span>
                        <div>Comisiones</div>
                    </li>
                    <li class="p-10 text-center">
                        <div class="font-12">150 x 1,282</div>
                        <div>Puntos x Comisiones</div>
                    </li>
                    <li class="p-10 text-center font-12">
                        <div class="font-12">{{number_format($tres)}}</div>
                        <i class="fa fa-money"></i>
                    </li>
                </ul>
            </div>

        </div><!-- /.panel -->
        <div class="row">
            <div class="col-md-9">&nbsp;</div>
            <div class="panel no-b col-md-3">

                <div class="panel-body no-p">
                    <ul class="list-unstyled list-float list-bordered three-col clearfix b-t no-m-b">
                        <li class="p-10 p-tb-10 text-right font-12 pointer">
                            <div class="font-header text-upper"></div>
                            <i class="fa fa-money"></i>
                        </li>
                        <li class="p-10 p-tb-10 text-center font-12 pointer">
                            <div class="font-header text-upper">Ganancias Totales</div>
                        </li>
                        <li class="p-10 p-tb-10 text-right font-12 pointer">
                            <div class="font-header text-upper">{{number_format(2324342)}}</div>
                            <i class="fa fa-money"></i>
                        </li>
                    </ul>
                </div>
            </div><!-- /.panel -->
        </div>--}}

        <div class="row">
            <a href="nivel/1" style="color: black;">
                <div class="col-sm-12">
                    <div class="box">
                        <div class="panel panel-default">
                            <div class="panel-heading font-header">Nivel 1</div>
                            <div class="panel-body puntos">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <div class="font-16"># {{number_format($uno)}} </div>
                                        <span class="text-mute">Referidos</span>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="font-16">$ {{number_format(353)}}</div>
                                        <span class="text-mute">Puntos</span>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="font-16">$ {{number_format(3334)}}</div>
                                        <span class="text-mute">Ganancias</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <a href="nivel/2" style="color: black;">
            <div class="col-sm-12">
                <div class="box">
                    <div class="panel panel-default">
                        <div class="panel-heading font-header">Nivel 2</div>
                        <div class="panel-body puntos">
                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="font-16"># {{number_format($dos)}} </div>
                                    <span class="text-mute">Referidos</span>
                                </div>
                                <div class="col-xs-4">
                                    <div class="font-16">$ {{number_format(353)}}</div>
                                    <span class="text-mute">Puntos</span>
                                </div>
                                <div class="col-xs-4">
                                    <div class="font-16">$ {{number_format(3334)}}</div>
                                    <span class="text-mute">Ganancias</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
            <a href="nivel/3" style="color: black;">
            <div class="col-sm-12">
                <div class="box">
                    <div class="panel panel-default">
                        <div class="panel-heading font-header">Nivel 3</div>
                        <div class="panel-body puntos">
                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="font-16"># {{number_format($tres)}} </div>
                                    <span class="text-mute">Referidos</span>
                                </div>
                                <div class="col-xs-4">
                                    <div class="font-16">$ {{number_format(353)}}</div>
                                    <span class="text-mute">Puntos</span>
                                </div>
                                <div class="col-xs-4">
                                    <div class="font-16">$ {{number_format(3334)}}</div>
                                    <span class="text-mute">Ganancias</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

    </section>

@endsection