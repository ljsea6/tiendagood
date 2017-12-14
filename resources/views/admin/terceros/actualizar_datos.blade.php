@extends('templates.dash')

@section('titulo','Editar Tercero')

@section('content')

<style type="text/css">
.btn-file {
    position: relative; 
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: black;
    cursor: inherit;
    display: block;
    color: white;
}    

</style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.0.0/sweetalert2.min.css">
    <link rel="stylesheet" href="http://cdn.jtsage.com/jtsage-datebox/4.2.3/jtsage-datebox-4.2.3.bootstrap.min.css" />
    <link rel="stylesheet" href="http://dev.jtsage.com/DateBox/css/syntax.css" />

    <div class="panel panel-default">
        <div class="panel-body">
          <!--  <form action="{{route('terceros.actualizar_mis_datos')}}" method="post" class="form-horizontal" enctype="multipart/form-data"> -->
                {!! Form::open(['route' => 'terceros.actualizar_mis_datos', 'method' => 'POST', 'class' => 'form-access submit', 'enctype' => 'multipart/form-data']) !!}
                <div class="row">
               
                    <div class="col-sm-3" style="height: 90">
                            <div class="row">
                            @if (currentUser()->avatar !== NULL)
                            <img src="{{ asset(currentUser()->avatar) }}" class="hidden-sm" alt="{{ currentUser()->nombre_completo }}" width="90%" />
                            @else
                            <img src="{{ asset("img/avatar-bg.png") }}" class="hidden-sm" alt="{{ currentUser()->nombre_completo }}" width="90%" />
                            @endif
                             </div> <br>  
                             <span class="btn btn-default" style="background: black; color: white"> Cambiar mi foto  <input type="file" id="foto" name="foto"> </span>
                             <hr>
                            <div class="row">
                            @if ($tercero['tipo_cliente_id'] == 83)
                              <a href="#">
                                          <input class="btn btn-default" style="background: #3783F9; color: white" type="button" id="d" name="d" value="Descargar contrato">
                            </a> 
                            @endif  
                            </div>                        
                    </div>                   
                    <div class="col-sm-9"> 
                            <h3 class="text-center">Actualizar mis datos</h3>
                            <hr>
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">


                            <div class="row">
                                  <label  class="col-sm-1">Nombres </label> 
                                  <div class="col-sm-5"> <input type="text" name="first-name" placeholder="Nombres" class="f1-first-name form-control campo input-error" id="first-name" required="" value="{{$tercero['nombres']}}"> </div>
                                  <label  class="col-sm-1">Apellidos </label> 
                                  <div class="col-sm-5"> <input type="text" name="last-name" placeholder="Apellidos" class="f1-first-name form-control campo" id="last-name" required=""value="{{$tercero['apellidos']}}"> </div>
                            </div> <br>
                            <div class="row">
                                  <label  class="col-sm-1">Teléfono </label> 
                                  <div class="col-sm-5"> <input type="text" name="phone" placeholder="Teléfono" class="f1-first-name form-control campo input-error" id="phone" required=""value="{{$tercero['telefono']}}"> </div>
                                  <label  class="col-sm-1">Dirección </label> 
                                  <div class="col-sm-5"> <input type="text" name="address" placeholder="Dirección" class="f1-first-name form-control campo" id="address" required=""value="{{$tercero['direccion']}}"> </div>
                            </div> <br>
                            <div class="row">
                                  <label  class="col-sm-1">Email </label> 
                                  <div class="col-sm-5"> <input type="text" name="email" placeholder="Email" class="f1-first-name form-control campo input-error" id="email" required=""value="{{$tercero['email']}}">
                                    <input type="hidden" name="email_old" id="email_old" value="{{$tercero['email']}}"> </div>
                                  <label  class="col-sm-1">Fecha de nacimiento </label> 
                                  <div class="col-sm-5"> <span class="fech"><input type="text" id="birthday" style="background-color: white; border-top-left-radius: 20px; border-bottom-left-radius: 20px;" name="birthday"  placeholder="Fecha de nacimiento..." class="f1-last-name form-control input-group-addon" data-role="datebox" data-options='{"mode":"datebox", "overrideDateFormat": "%d/%m/%Y", "useFocus": true }' readonly="readonly"
                                   value="{{ $fecha_nacimiento }}"/></span>
                                  </div>
                            </div> <br>
                             <h3 class="text-center">Documentos</h3>
                             <hr>
                            <div class="row"> 
                                    <label  class="col-sm-6">RUT <label style="color: #F00707"> Recuerde que su RUT debe tener código de actividad 8299 </label> </label>
                                    <div class="col-sm-3"> 
                                      @if ($tercero['rut'] != '')  
                                        <a href="{{route('admin.terceros.descargar_documentos', ['id' => currentUser()->id, 'tipo' => 'rut'])}}">
                                          <input class="btn btn-default" style="background: #3783F9; color: white" type="button" id="d" name="d" value="descargar">
                                        </a> 
                                      @endif 
                                    </div>     
                                    <div class="col-sm-3"> <span class="btn btn-default" style="background: black; color: white"> 
                                      @if ($tercero['rut'] != '')  
                                        Cambiar 
                                      @endif 
                                      @if ($tercero['rut'] == '')  
                                        Agregar 
                                      @endif 
                                        <input type="file" id="rut" name="rut">  </span>  </div>                                                       
                            </div>  <br>
                            <div class="row"> 
                                    <label  class="col-sm-6">Cédula o Documento</label>
                                    <div class="col-sm-3">
                                      @if ($tercero['cedula'] != '')  
                                        <a href="{{route('admin.terceros.descargar_documentos', ['id' => currentUser()->id, 'tipo' => 'cedula'])}}">
                                          <input class="btn btn-default" style="background: #3783F9; color: white" type="button" id="d" name="d"  value="descargar"> 
                                        </a>
                                      @endif 
                                    </div>   
                                    <div class="col-sm-3"> <span class="btn btn-default" style="background: black; color: white"> 
                                      @if ($tercero['cedula'] != '')  
                                        Cambiar 
                                      @endif 
                                      @if ($tercero['cedula'] == '')  
                                        Agregar 
                                      @endif 
                                     <input type="file" id="cedula" name="cedula">   </span> </div>                                      
                            </div> <br>
                            <div class="row"> 
                                    <label  class="col-sm-6">Certificación bancaria (No obligatorio para registro) </label>
                                    <div class="col-sm-3">  
                                      @if ($tercero['cuenta'] != '')   
                                       <a href="{{route('admin.terceros.descargar_documentos', ['id' => currentUser()->id, 'tipo' => 'cuenta'])}}">
                                       <input class="btn btn-default" style="background: #3783F9; color: white" type="button" id="d" name="d"  value="descargar"> 
                                      </a> 
                                      @endif 
                                    </div>  
                                    <div class="col-sm-3">  
                                        <span class="btn btn-default " style="background: black; color: white"> 
                                      @if ($tercero['cuenta'] != '')  
                                        Cambiar 
                                      @endif 
                                      @if ($tercero['cuenta'] == '')  
                                        Agregar 
                                      @endif 
                                         <input type="file" id="banco" name="banco">   </span> 
                                    </div>                                  
                            </div>  <br>
                            <div class="row"> 
                                <div class="col-sm-12">
                                    <button class="btn btn-primary" type="submit" data-toggle="modal" data-target="#myModal" id="enviar">Actualizar mis datos</button>
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger fade in col-sm-offset-3 col-sm-6">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif 
                    </div>
                </div>
            </form>
        </div>
    </div>

<script src="http://backoffice.tiendagood.com/assets/js/jquery-1.11.1.min.js"></script>
<script src="http://backoffice.tiendagood.com/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="http://backoffice.tiendagood.com/assets/js/jquery.backstretch.min.js"></script>
<script src="http://backoffice.tiendagood.com/assets/js/retina-1.1.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.0.0/sweetalert2.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://cdn.jtsage.com/external/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="http://dev.jtsage.com/DateBox/js/doc.js"></script>
<script type="text/javascript" src="http://cdn.jtsage.com/jtsage-datebox/4.2.3/jtsage-datebox-4.2.3.bootstrap.min.js"></script>
<script type="text/javascript" src="http://cdn.jtsage.com/jtsage-datebox/i18n/jtsage-datebox.lang.utf8.js"></script>

<script type="text/javascript">
 @if(Session::has('flash_msg'))
                  swal({ 
                  html: $('<div>').text('{{Session::get('flash_msg')}}'),
                  animation: false,
                  customClass: 'animated tada'
                  });
@endif   

    var exp_number = /^[a-zA-Z0-9]{6,15}$/;
    var exp_acount = /^[0-9\-]{7,}$/;
    var exp_names =/^[a-zA-ZáéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ\s]+$/;
    var exp_date = /^([0-9]{2}\/[0-9]{2}\/[0-9]{4})$/;
    var exp_address = /^[0-9a-zA-ZáéíóúàèìòùÀÈÌÒÙÁÉÍÓÚñÑüÜ#.\-_\s]+$/;
    var exp_phone = /^[0-9-()+]{3,20}$/;
    var exp_email = /^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\.]+$/; 

$('.submit').on('submit',function(event){
 

            var first_name = $('#first-name').val();
            var last_name = $('#last-name').val();
            var phone = $('#phone').val();
            var address = $('#address').val();

            if (first_name.length < 2) {
                swal({ 
                  html: $('<div>').text('El campo nombres es demasiado corto (usa mínimo 4 caracteres).'),
                  animation: false,
                  customClass: 'animated tada'
                  });    
                event.preventDefault() ;
            }
           else if (last_name.length < 2 ) {
                swal({ 
                  html: $('<div>').text('El campo apellidos es demasiado corto (usa mínimo 4 caracteres).'),
                  animation: false,
                  customClass: 'animated tada'
                  });    
                event.preventDefault() ;
            } 

            else if (phone.length != 10 ) {
                swal({ 
                  html: $('<div>').text('El Teléfono demasiado corto (se usa 10 dígitos).'),
                  animation: false,
                  customClass: 'animated tada'
                  });    
                event.preventDefault() ;
            } 

            else if (address.length < 5 ) {
                swal({ 
                  html: $('<div>').text('La dirección no puede estar en blanco y no se puede usar caracteres especiales.'),
                  animation: false,
                  customClass: 'animated tada'
                  });    
                event.preventDefault() ;
            } 


    if($('#email').val() != $('#email_old').val()){
        var email = $('#email').val();
        if (exp_email.test(email) && email.length > 0) {
                result_email = JSON.parse( $.ajax({
                    url: '../validate/email',
                    type: 'post',
                    data: {email: email, _token: $('#_token').val() },
                    dataType: 'json',
                    async:false
                }).responseText);

                if (result_email.msg == 'email valido') { 
                    return true;
                }
                else if(result_email.err == 'email existe') { 
                  swal({ 
                  html: $('<div>').text('El email que ingresó existe, ingrese otro por favor.'),
                  animation: false,
                  customClass: 'animated tada'
                  });                    
                    event.preventDefault() ;
                } 
        }
        else{
                swal({ 
                  html: $('<div>').text('Ingresa un correo electrónico válido.'),
                  animation: false,
                  customClass: 'animated tada'
                  });                     
                    event.preventDefault() ;
        }
    }
 
// event.preventDefault() ;
        //$('#enviar').prop('disabled', true);        
});    

</script>

@endsection




