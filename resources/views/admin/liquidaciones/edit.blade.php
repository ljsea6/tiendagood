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
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3">

            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header" data-background-color="green">
                        <i class="fa fa-money" aria-hidden="true"></i>
                    </div>
                    <div class="card-content">
                        <p class="category">Este es el total de su bono # {{ $fecha }}</p>
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
        </div>

        <div class="row">

            <form id="bono" name="bono" action="{{route('admin.liquidaciones.gift_card')}}" method="post">
                <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">
                <input type="hidden" id="bono" name="bono" value="{{$bono}}">
                <input type="hidden" id="liquidacion" name="liquidacion" value="{{$id}}">
                <input type="hidden" id="good" name="good" readonly class="good-hidden">
                <input type="hidden" id="mercando" name="mercando" readonly class="mercando-hidden">
                <div class="col-ms-12">
                    <div class="card card-stats" style="margin: 5px 0;">
                        <div class="row">
                            <div class="col-sm-3">
                                <img src="https://cdn.shopify.com/s/files/1/2256/3751/files/logo-good.png?7720396070787645882" alt="Tienda Good">
                                <input id="good-1" name="good-1" readonly class="good">
                            </div>
                            <div class="col-sm-6">
                                <div id="handle1"></div>
                            </div>
                            <div class="col-sm-3">
                                <img src="//cdn.shopify.com/s/files/1/2560/2330/files/Logo-Mercando_x96.png?v=1511966864" alt="Mercando">
                                <input id="mercando-1" name="mercando-1" readonly class="mercando">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-content" style="align: center;">
                                    <span class="plan_prime_texto"> <button  class="btn btn-primary text-center" data-background-color="orange" type="submit">Crear Bonos</button> </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
            value: 50,
            handleSize: 0,
            handleShape: "square",
            circleShape: "pie",
            startAngle: 315,
            tooltipFormat: "changeTooltip"
        });

        function changeTooltip(e) {

            var val = e.value, speed;
            var M = ((-1 * (val - 100)) * parseFloat('{{$bono}}')) / 100;
            var G = (val * parseFloat('{{$bono}}')) / 100;

            $(".good-hidden").val('' + G + '');

            $(".mercando-hidden").val('' +  M + '');

            $(".good").val('$ ' + number_format(G, 2) + '');

            $(".mercando").val('$ ' +  number_format(M, 2) + '');


            return "G " + " % " + " M";
        }

        $("form").submit(function(e){

            var total = parseFloat('{{$bono}}');
            var good = parseFloat($('#good').val());
            var mercando = parseFloat($('#mercando').val());
            var suma = (good + mercando);

            if (suma > total) {
                swal(
                    '¡Por favor, verifique que la suma de los bonos para Tienda Good y Mercando no superen el total del Bono!',
                    'Clicked para corregir!',
                    'error'
                );
                e.preventDefault();
            }
        });

        function number_format(amount, decimals) {

            amount += ''; // por si pasan un numero en vez de un string
            amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

            decimals = decimals || 0; // por si la variable no fue fue pasada

            // si no es un numero o es igual a cero retorno el mismo cero
            if (isNaN(amount) || amount === 0)
                return parseFloat(0).toFixed(decimals);

            // si es mayor o menor que cero retorno el valor formateado como numero
            amount = '' + amount.toFixed(decimals);

            var amount_parts = amount.split('.'),
                regexp = /(\d+)(\d{3})/;

            while (regexp.test(amount_parts[0]))
                amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

            return amount_parts.join('.');
        }

    </script>

    @if($errors->any())
        <script>
            swal(
                '{{$errors->first()}}',
                '¡Comuniquese con soporte!',
                'error'
            );
        </script>
    @endif


@stop