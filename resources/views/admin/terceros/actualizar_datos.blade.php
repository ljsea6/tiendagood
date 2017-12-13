@extends('templates.dash')

@section('titulo','Editar Tercero')

@section('content')

<style type="text/css">
.btn-file {
    position: relative;
    overflow: hidden;
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
            <form action="{{route('admin.terceros.actualizar_mis_datos')}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                <div class="row">
               
                    <div class="col-sm-2">
                            <div class="row">
                            @if (currentUser()->avatar !== NULL)
                            <img src="{{ asset(currentUser()->avatar) }}" class="hidden-sm" alt="{{ currentUser()->nombre_completo }}"/>
                            @else
                            <img src="{{ asset("img/avatar-bg.png") }}" class="hidden-sm" alt="{{ currentUser()->nombre_completo }}"/>
                            @endif
                             </div> <br>  
                             <span class="btn btn-default btn-file" style="background: black; color: white"> Cambiar mi foto  <input type="file" id="foto" name="foto"> </span>
                    </div>                   
                    <div class="col-sm-10"> 
                            <h3 class="text-center">Actualizar mis datos</h3>
                            <hr>
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">


                            <div class="row">
                                  <label  class="col-sm-1">Nombres </label> 
                                  <div class="col-sm-5"> <input type="text" name="first-name" placeholder="Nombres" class="f1-first-name form-control campo input-error" id="dni" required="" value="{{$tercero['nombres']}}"> </div>
                                  <label  class="col-sm-1">Apellidos </label> 
                                  <div class="col-sm-5"> <input type="text" name="last-name" placeholder="Apellidoss" class="f1-first-name form-control campo" id="first-name" required=""value="{{$tercero['apellidos']}}"> </div>
                            </div> <br>
                            <div class="row">
                                  <label  class="col-sm-1">Teléfono </label> 
                                  <div class="col-sm-5"> <input type="text" name="phone" placeholder="Teléfono" class="f1-first-name form-control campo input-error" id="dni" required=""value="{{$tercero['direccion']}}"> </div>
                                  <label  class="col-sm-1">Dirección </label> 
                                  <div class="col-sm-5"> <input type="text" name="address" placeholder="Dirección" class="f1-first-name form-control campo" id="first-name" required=""value="{{$tercero['telefono']}}"> </div>
                            </div> <br>
                            <div class="row">
                                  <label  class="col-sm-1">Email </label> 
                                  <div class="col-sm-5"> <input type="text" name="email" placeholder="Email" class="f1-first-name form-control campo input-error" id="dni" required=""value="{{$tercero['email']}}"> </div>
                                  <label  class="col-sm-1">Fecha de nacimiento </label> 
                                  <div class="col-sm-5"> <span class="fech"><input type="text" id="birthday" style="background-color: white; border-top-left-radius: 20px; border-bottom-left-radius: 20px;" name="birthday"  placeholder="Fecha de nacimiento..." class="f1-last-name form-control input-group-addon" data-role="datebox" data-options='{"mode":"datebox", "overrideDateFormat": "%d/%m/%Y", "useFocus": true }' readonly="readonly"
                                   value="{{ $fecha_nacimiento }}"/></span>
                                  </div>
                            </div> <br>
                             <h3 class="text-center">Documentos</h3>
                             <hr>
                            <div class="row"> 
                                    <label  class="col-sm-6">RUT <label style="color: #F00707"> Recuerde que su RUT debe tener código de actividad 8299 </label> </label>
                                    <div class="col-sm-3"> <span class="btn btn-default btn-file" style="background: #3783F9; color: white"> descargar <input type="file" id="rut" name="rut"> </span>  </div>     
                                    <div class="col-sm-3"> <span class="btn btn-default btn-file" style="background: black; color: white"> Cambiar <input type="file" id="rut" name="rut"> </span>  </div>                                                       
                            </div>  <br>
                            <div class="row"> 
                                    <label  class="col-sm-6">Cédula o Documento</label>
                                    <div class="col-sm-3"> <span class="btn btn-default btn-file" style="background: #3783F9; color: white"> descargar <input type="file" id="cedula" name="cedula"> </span> </div>   
                                    <div class="col-sm-3"> <span class="btn btn-default btn-file" style="background: black; color: white"> Cambiar <input type="file" id="cedula" name="cedula"> </span> </div>                                      
                            </div> <br>
                            <div class="row"> 
                                    <label  class="col-sm-6">Certificación bancaria (No obligatorio para registro) </label>
                                    <div class="col-sm-3">  <span class="btn btn-default btn-file" style="background: #3783F9; color: white"> descargar <input type="file" id="banco" name="banco"> </span> </div>  
                                    <div class="col-sm-3">  <span class="btn btn-default btn-file" style="background: black; color: white"> Cambiar <input type="file" id="banco" name="banco"> </span> </div>                                  
                            </div>  <br>
                            <div class="row"> 
                                <div class="col-sm-12">
                                    <button class="btn btn-primary" type="submit" data-toggle="modal" data-target="#myModal">Actualizar mis datos</button>
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

@endsection




