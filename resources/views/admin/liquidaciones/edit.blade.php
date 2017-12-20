@extends('templates.dash')



@section('titulo','Good')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/round-slider@1.3/dist/roundslider.min.css" rel="stylesheet" />
    <style>
        #handle1 .rs-handle  {
            background-color: transparent;
            border: 8px solid transparent;
            border-right-color: black;
            margin: -6px 0px 0px 14px !important;
            border-width: 6px 104px 6px 4px;
        }
        #handle1 .rs-handle:before  {
            display: block;
            content: " ";
            position: absolute;
            height: 22px;
            width: 22px;
            background: black;
            right: -11px;
            bottom: -11px;
            border-radius: 100px;
        }
        #handle1 .rs-tooltip  {
            top: 75%;
            font-size: 11px;
        }
        #handle1 .rs-tooltip div  {
            text-align: center;
            background: orange;
            color: white;
            border-radius: 4px;
            padding: 1px 5px 2px;
            margin-top: 4px;
        }
        #handle1 .rs-range-color  {
            background-color: #FA9300;
        }
        #handle1 .rs-path-color  {
            background-color: #22B573;
        }

    </style>
@stop

@section('content')
    {!! Html::style('css/material-dashboard.css?act=4') !!}

    <link href="https://fonts.googleapis.com/css?family=Bungee|Roboto+Slab:100,300,400,700" rel="stylesheet">

    <section class="invoice">
        <div class="col-lg-12">


            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                <img src="https://cdn.shopify.com/s/files/1/2256/3751/files/logo-good2.png?4867312163605074192" alt="" style="width: 300px; padding: 20px 0">
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="green">
                    <i class="fa fa-money" aria-hidden="true"></i>
                </div>
                <div class="card-content">
                    <p class="category">Este es el total de su liquidacion # {{ $id }}</p>
                    <h3 class="title">
                        <small>Total en bono: ${{ number_format($total * 0.3) }}</small>
                    </h3>


                </div>
                <div class="card-footer" style="display: block;">
                    <div class="stats">
                        <i class="material-icons text-danger"></i>
                        <a href="#pablo"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="green">
                    <i class="fa fa-money" aria-hidden="true"></i>
                </div>
                <div class="card-content">
                    <p class="category">Bono Good</p>
                    <h3 class="title">
                        <small>${{ $bono/2 }} </small>
                    </h3>
                </div>
                <div class="card-footer" style="display: block;">
                    <div class="stats">
                        <i class="material-icons text-danger"></i>
                        <a href="#pablo"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="green">
                    <i class="fa fa-money" aria-hidden="true"></i>
                </div>
                <div class="card-content">
                    <p class="category">Bono Mercando</p>
                    <h3 class="title">
                        <small>${{ $bono/2 }}</small>
                    </h3>
                </div>
                <div class="card-footer" style="display: block;">
                    <div class="stats">
                        <i class="material-icons text-danger"></i>
                        <a href="#pablo"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-ms-12">
            <div class="card card-stats" style="margin: 5px 0;">
                <div class="row">
                    <div class="col-sm-3">

                    </div>
                    <div class="col-sm-6">
                        <div id="handle1"></div>
                    </div>
                    <div class="col-sm-3">

                    </div>
                </div>
            </div>
        </div>


        <div class="col-sm-12">
            <div class="card card-stats" style="margin: 5px 0;">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card-content" style="text-align: left;">

                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-content" style="text-align: center;">

                                <span class="plan_prime_texto"> <button class="btn btn-primary" data-background-color="orange" type="button"  id="actualizar_plan_prime" onclick="plan()">Crear Bonos</button> </span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card-content" style="text-align: center; margin-top: 20px; text-align: right;">
                            {{--<span class="category">Fecha del proximo corte:</span><br> <span style="font-family: 'Bungee', cursive; color: black;font-size: 16px;"> 21 de diciembre</span>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </section>


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@1.11.3/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/round-slider@1.3/dist/roundslider.min.js"></script>

    <script type="text/javascript">

        $("#handle1").roundSlider({

            sliderType: "min-range",
            editableTooltip: false,
            radius: 105,
            width: 16,
            value: 0,
            handleSize: 0,
            handleShape: "square",
            circleShape: "pie",
            startAngle: 315,
            tooltipFormat: "changeTooltip"
        });

        function changeTooltip(e) {

            console.log(e.value);
            var val = e.value, speed;

            if (val < 20) speed = "Slow";

            else if (val < 40) speed = "Normal";

            else if (val < 70) speed = "Speed";

            else speed = "Very Speed";

            return val + " % "  + "  " +  (-1*(val - 100))  + "<div>" + speed + "<div>";
        }
    </script>
@stop