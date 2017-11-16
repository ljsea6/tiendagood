<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Good</title>
    <!-- CSS -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
    <link href="https://fonts.googleapis.com/css?family=Dosis:200,300,400,500,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/3.1.3/js/bootstrap-datetimepicker.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.min.css" />
    <!-- Special version of Bootstrap that only affects content wrapped in .bootstrap-iso -->
    <link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" />

    <!--Font Awesome (added because you use icons in your prepend/append)-->
    <link rel="stylesheet" href="https://formden.com/static/cdn/font-awesome/4.4.0/css/font-awesome.min.css" />

    <link rel="stylesheet" href="assets/css/form-elements.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Inline CSS based on choices in "Settings" tab -->
    <style>.bootstrap-iso .formden_header h2, .bootstrap-iso .formden_header p, .bootstrap-iso form{font-family: Arial, Helvetica, sans-serif; color: black}.bootstrap-iso form button, .bootstrap-iso form button:hover{color: white !important;} .asteriskField{color: red;}</style>

</head>

<body>

<!-- Top menu -->
<nav class="navbar navbar-inverse navbar-no-bg" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#top-navbar-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="/">Home</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="top-navbar-1">
            <ul class="nav navbar-nav navbar-right">
                <li>
                            <span class="li-text">
                                Put some text or
                            </span>
                    <a href="#"><strong>Good</strong></a>
                    <span class="li-text">
                                here, or some icons:
                            </span>
                    <span class="li-social">
                                <a href="https://www.facebook.com/pages/Azmindcom/196582707093191" target="_blank"><i class="fa fa-facebook"></i></a>
                                <a href="https://twitter.com/anli_zaimi" target="_blank"><i class="fa fa-twitter"></i></a>
                                <a href="https://plus.google.com/+AnliZaimi_azmind" target="_blank"><i class="fa fa-google-plus"></i></a>
                                <a href="https://github.com/AZMIND" target="_blank"><i class="fa fa-github"></i></a>
                            </span>
                </li>
            </ul>
        </div>
    </div>
</nav>


<!-- Top content -->
<div class="top-content">
    <div class="container">

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

        <div class="row">
            <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 form-box">
                <form enctype="multipart/form-data" role="form" action="/register" method="post" class="f1">

                    <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">

                    <img src="assets/img/logo-good.png" alt="" style="width: 50%; margin: 20px auto; display: block">
                    <h3>Registrate en Good</h3>

                    <p style="border-bottom: 2px solid #80808014; padding-bottom: 30px;">En tres simples pasos pertenecerás a nuestro exclusivo club.</p>
                    <div class="f1-steps">


                        <div id="cero" class="f1-step active">
                            <div class="f1-step-icon"><i class="fa fa-user"></i></div>
                            <p>Tus datos</p>
                        </div>

                        <div id="one" class="f1-step">
                            <div class="f1-step-icon"><i class="fa fa-key"></i></div>
                            <p>Tu usuario</p>
                        </div>

                        <div id="two" class="f1-step">
                            <div class="f1-step-icon"><i class="fa fa-check-square-o"></i></div>
                            <p>Tus documentos</p>
                        </div>
                    </div>

                    <fieldset id="tree">

                        <div class="col-xs-12 col-md-12" style="text-align: center">
                            <h4 class="dinos">Dinos quién eres:</h4>
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <div class="form-group">
                                <label for="type_client">Tipo Cliente</label>
                                <select id="type_client" name="type_client" class="form-control campo" required>
                                    <option value=""></option>
                                    @foreach($tipos->tipos as $tipo)
                                        <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="type_dni">Tipo Documento</label>
                                <select id="type_dni" name="type_dni" class="form-control campo" required>
                                    <option value=""></option>
                                    @foreach($documentos->tipos as $tipo)
                                        <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="dni">Número Documento</label>
                                <input type="text" name="dni" placeholder="Documento..." class="f1-first-name form-control campo" id="dni" required>
                            </div>


                            <div class="form-group">
                                <label for="city">Ciudad</label>
                                <select id="city" name="city" class="form-control campo" required>
                                    <option value=""></option>
                                    @foreach($cities as $tipo)

                                        <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sex">Sexo</label>
                                <select id="sex" name="sex" class="form-control campo" required>
                                    <option value=""></option>
                                    <option value="1">M</option>
                                    <option value="2">F</option>
                                </select>
                            </div>

                        </div>


                        <div class="col-md-6 col-xs-12">
                            <div class="form-group">
                                <label for="f1-first-name">Nombres</label>
                                <input type="text" name="first-name" placeholder="Nombres..." class="f1-first-name form-control campo" id="first-name" required>
                            </div>

                            <div class="form-group">
                                <label for="f1-last-name">Apellidos</label>
                                <input type="text" name="last-name" placeholder="Apellidos..." class="f1-last-name form-control campo" id="last-name" required>
                            </div>

                            <div class="form-group">
                                <label for="birthday">Fecha de Nacimiento</label>
                                <input type="date" id="birthday" name="birthday"  placeholder="Fecha de nacimiento..." class="f1-last-name form-control campo" required/>
                            </div>
                            <div class="form-group">
                                <label for="address">Direccion</label>
                                <input type="text" id="address" name="address"  placeholder="Direccion..." class="f1-last-name form-control campo" required/>
                            </div>

                            <div class="form-group">
                                <label for="phone">Telefono</label>
                                <input type="tel" min="7" max="" id="phone" name="phone"  placeholder="Telefono..." class="f1-last-name form-control campo" required/>
                            </div>

                            <div class="f1-buttons">
                                <button type="button" class="btn btn-next">Siguiente</button>
                            </div>
                        </div>


                    </fieldset>

                    <fieldset id="four">

                        <h4 class="dinos">Configurar tu usuario:</h4>

                        @if(isset($email))
                            <div class="form-group">
                                <label for="code">Código de tu referido</label>
                                <input type="text" name="code" placeholder="Código..." class="f1-email form-control campo" id="code"  value="{{$email}}" readonly>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="code">Código de tu referido</label>
                                <input type="text" name="code" placeholder="Código..." class="f1-email form-control campo" id="code" required>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="f1-email">Email</label>
                            <input type="email" name="email" placeholder="Email..." class="f1-email form-control campo" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="f1-password">Contraseña</label>
                            <input type="password" name="password" placeholder="Contraseña..." class="f1-password form-control campo" id="password" required>
                        </div>
                        <div class="form-group">
                            <label  for="f1-repeat-password">Repetir Contraseña</label>
                            <input type="password" name="password_confirmation" placeholder="Repeter Contraseña..."
                                   class="f1-repeat-password form-control campo" id="password_confirmation" required>
                        </div>

                        <div class="f1-buttons">
                            <button type="button" class="btn btn-previous">Anterior</button>
                            <button type="button" class="btn btn-next">Siguiente</button>
                            <button type="submit" class="btn btn-submit">Crear</button>
                        </div>


                    </fieldset>

                    <fieldset id="five">

                        <h4 class="dinos">Documentos y condiciones:</h4>

                        <div class="form-group">
                            <label for="bank">Seleccionar Entidad Bancaria</label>
                            <select id="bank" name="bank" class="form-control campo" style="width: 100% !important;">
                                <option value=""></option>
                                @foreach($bancos as $tipo)

                                    <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type_acount_bank">Tipo de cuenta </label>
                            <select id="type_acount_bank" name="type_acount_bank" class="form-control campo" style="width: 100% !important;">
                                <option value=""></option>
                                @foreach($cuentas->tipos as $tipo)
                                    <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="acount">Número de cuenta</label>
                            <input type="number" name="acount" placeholder="Documento..." class="f1-first-name form-control campo" id="acount">
                        </div>

                        <div class="form-group">
                            <label class="custom-file">
                                Certificación bancaria
                                <input type="file" id="banco" name="banco" class="custom-file-input campo">
                                <span class="custom-file-control"></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="custom-file">
                                Cédula o Documento
                                <input type="file" id="cedula" name="cedula" class="custom-file-input campo">
                                <span class="custom-file-control"></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="custom-file">
                                RUT
                                <input type="file" id="rut" name="rut" class="custom-file-input campo">
                                <span class="custom-file-control"></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-check-label">
                                <input id="prime" name="prime" class="form-check-input campo" type="checkbox">
                                Usuario Prime
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="contrato" class="form-check-label">
                                <input class="form-check-input campo"  type="checkbox" id="contract" name="contract" required />
                                Contrato <a href="pagina_condiciones.html">terminos</a>
                            </label>
                        </div>


                        <div class="form-group">
                            <label for="condiciones" class="form-check-label">
                                <input class="form-check-input campo"  type="checkbox" id="terms" name="terms" required />
                                ¿Acepta <a href="pagina_condiciones.html">terminos</a> y condiciones?
                            </label>
                        </div>

                        <div class="f1-buttons">
                            <button type="button" class="btn btn-previous">Anterior</button>
                            <button type="submit" class="btn btn-submit">Crear</button>
                        </div>

                    </fieldset>

                </form>
            </div>
        </div>

    </div>
</div>


<!-- Javascript -->
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.backstretch.min.js"></script>
<script src="assets/js/retina-1.1.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script src="assets/js/scripts.js?u={{random_int(1, 100)}}"></script>

<!--[if lt IE 10]>
<script src="assets/js/placeholder.js"></script>

<![endif]-->


</body>

</html>