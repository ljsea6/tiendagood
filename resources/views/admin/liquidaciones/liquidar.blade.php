@section('titulo','Good')

@section('content')


    {!! Html::style('css/material-dashboard.css?act=4') !!}

    <div class="row">
        <div class="col-xs-12">
            <h3 class="text-center title-actualizar  col-xs-12">Generar liquidación</h3><hr>
        </div>
        <div class="col-sm-12">
            {!! Form::open(['route' => 'liquidacion.liquidar.envio', 'method' => 'POST', 'class' => 'form-access submit']) !!}
            <input type="hidden" name="liquidar" value="1">
            <button class="btn btn-warning" type="submit" data-toggle="modal" data-target="#myModal" id="enviar">Generar|</button>
            </form>
        </div>
    </div>

    <script type="text/javascript"> function link(nivel){ location.href = "/nivel/"+nivel; }

        $('.submit').on('submit',function(event){

            $('#enviar').prop('disabled', true);

        });

        @if(Session::has('flash_msg'))
        swal(
            'Felicitaciones se realizo la liquidación correctamente.',
            ''
        );
        @endif

    </script>

    </section>

@endsection