@extends('templates.dash')

@section('titulo','Good')

@section('content')
    <style type="text/css"> .puntos{ font-size: 20px; font-weight: bold; } </style>
    <section class="invoice">
        <div class="page-header no-breadcrumb font-header"><i class="fa fa-user"></i>¡Bienvenido(a) {{ currentUser()->nombre_completo }}!</div>
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
        <div class="row">
            <div class="col-sm-4">
                <div class="box">
                    <div class="panel panel-default">
                        <div class="panel-heading font-header"><a href="nivel/1" >Nivel 1</a></div>
                        <div class="panel-body puntos">
                            {{number_format($uno)}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="box">
                    <div class="panel panel-default">
                        <div class="panel-heading font-header"><a href="nivel/2" >Nivel 2</a></div>
                        <div class="panel-body puntos">
                            {{number_format($dos)}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="box">
                    <div class="panel panel-default">
                        <div class="panel-heading font-header"><a href="nivel/3" >Nivel 3</a></div>
                        <div class="panel-body puntos">
                            {{number_format($tres)}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
        </div>
        <br><br><br>
    </section>

@endsection