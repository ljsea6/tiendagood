@extends('templates.dash')

@section('titulo','Good')

@section('content')

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="text-center title-actualizar  col-xs-12">Generar liquidación</h3><hr>
                </div>
            </div><br>
            {!! Form::open(['route' => 'liquidacion.liquidar.envio', 'method' => 'POST', 'class' => 'form-access submit']) !!} 
                    <input type="hidden" name="liquidar" value="1">
            <div class="row"> 
                <div class="col-sm-6"><input type="text" class="form-control" name="fecha_inicio" id="datepicker_1" readonly  value="Fecha Inicio"></div>
                <div class="col-sm-6"><input type="text" class="form-control" name="fecha_final"  id="datepicker_2" readonly  value="Fecha Final"> </div>                
            </div><br> 
            <div class="row"> 
                <div class="col-sm-12">
                    <button class="btn btn-warning" type="submit" data-toggle="modal" data-target="#myModal" id="enviar">Generar</button>
                </div>
            </div><br> 
            </form>
        </div>
    </div>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript"> function link(nivel){ location.href = "/nivel/"+nivel; }

        $('.submit').on('submit',function(event){

            $('#enviar').prop('disabled', true);

        });

        $( "#datepicker_1, #datepicker_2" ).datepicker({ dateFormat: 'yy-mm-dd' });

        @if(Session::has('flash_msg'))
        swal(
            'Felicitaciones se realizo la liquidación correctamente.',
            ''
        );
        @endif

    </script>

    </section>

@endsection