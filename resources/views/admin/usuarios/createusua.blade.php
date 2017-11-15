<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Good</title>
    <!-- CSS -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/form-elements.css">
    <link rel="stylesheet" href="assets/css/style.css">

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

                    <h3>Registrate en Good</h3>
                    <p>En tres simples pasos pertenecerás a nuestro exclusivo club.</p>
                    <div class="f1-steps">

                        <div class="f1-progress">
                            <div class="f1-progress-line" data-now-value="16.66" data-number-of-steps="3" style="width: 16.66%;"></div>
                        </div>

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
                        <h4>Dinos quién eres:</h4>



                        <div class="form-group">
                            <label for="type_client">Tipo Cliente</label>
                            <select id="type_client" name="type_client" class="form-control" required>
                                @foreach($tipos->tipos as $tipo)
                                    <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type_dni">Tipo Documento</label>
                            <select id="type_dni" name="type_dni" class="form-control" required>
                                @foreach($documentos->tipos as $tipo)
                                    <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dni">Número Documento</label>
                            <input type="number" name="dni" placeholder="Documento..." class="f1-first-name form-control" id="dni" required>
                        </div>


                        <div class="form-group">
                            <label for="city">Ciudad</label>
                            <select id="city" name="city" class="form-control" required>
                                    @foreach($cities as $tipo)
                                        <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                    @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sex">Sexo</label>
                            <select id="sex" name="sex" class="form-control" required>

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="f1-first-name">Nombres</label>
                            <input type="text" name="first-name" placeholder="Nombres..." class="f1-first-name form-control" id="first-name" required>
                        </div>

                        <div class="form-group">
                            <label for="f1-last-name">Apellidos</label>
                            <input type="text" name="last-name" placeholder="Apellidos..." class="f1-last-name form-control" id="last-name" required>
                        </div>

                        <div class="form-group">
                            <label for="birthday">Fecha de Nacimiento</label>
                            <input type="date" id="birthday" name="birthday"  placeholder="Fecha de nacimiento..." class="f1-last-name form-control" required/>
                        </div>

                        <div class="form-group">
                            <label for="address">Direccion</label>
                            <input type="text" id="address" name="address"  placeholder="Direccion..." class="f1-last-name form-control" required/>
                        </div>

                        <div class="form-group">
                            <label for="phone">Telefono</label>
                            <input type="tel" min="7" max="" id="phone" name="phone"  placeholder="Telefono..." class="f1-last-name form-control" required/>
                        </div>

                        <div class="f1-buttons">
                            <button type="button" class="btn btn-next">Siguiente</button>
                        </div>
                    </fieldset>

                    <fieldset id="four">
                        <h4>Configurar tu usuario:</h4>

                        @if(isset($email))
                            <div class="form-group">
                                <label for="f1-email">Código de tu referido</label>
                                <input type="number" name="code" placeholder="Código..." class="f1-email form-control" id="code"  value="{{$email}}" required disabled="true">
                            </div>
                        @else
                            <div class="form-group">
                                <label for="f1-email">Código de tu referido</label>
                                <input type="number" name="code" placeholder="Código..." class="f1-email form-control" id="code" required>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="f1-email">Email</label>
                            <input type="email" name="email" placeholder="Email..." class="f1-email form-control" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="f1-password">Contraseña</label>
                            <input type="password" name="password" placeholder="Contraseña..." class="f1-password form-control" id="password" required>
                        </div>
                        <div class="form-group">
                            <label  for="f1-repeat-password">Repetir Contraseña</label>
                            <input type="password" name="password_confirmation" placeholder="Repeter Contraseña..."
                                   class="f1-repeat-password form-control" id="password_confirmation" required>
                        </div>

                        <div class="f1-buttons">
                            <button type="button" class="btn btn-previous">Anterior</button>
                            <button type="button" class="btn btn-next">Siguiente</button>
                        </div>
                    </fieldset>

                    <fieldset id="five">
                        <h4>Documentos y condiciones:</h4>

                        <div class="form-group">
                            <label for="bank">Seleccionar Entidad Bancaria</label>
                            <select id="bank" name="bank" class="form-control" required style="width: 100% !important;">
                                @foreach($bancos as $tipo)
                                    <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type_acount_bank">Tipo de cuenta </label>
                            <select id="type_acount_bank" name="type_acount_bank" class="form-control" required style="width: 100% !important;">
                                @foreach($cuentas->tipos as $tipo)
                                    <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="acount">Número de cuenta</label>
                            <input type="number" name="acount" placeholder="Documento..." class="f1-first-name form-control" id="acount">
                        </div>

                        <div id="cd-div" class="form-group">
                            <label class="custom-file">
                                Certificación bancaria
                                <input type="file" id="cuenta" name="cuenta" class="custom-file-input">
                                <span class="custom-file-control"></span>
                            </label>
                        </div>

                        <div id="cd-div" class="form-group">
                            <label class="custom-file">
                                Cédula o Documento
                                <input type="file" id="cedula" name="cedula" class="custom-file-input">
                                <span class="custom-file-control"></span>
                            </label>
                        </div>

                        <div id="rut-div" class="form-group">
                            <label class="custom-file">
                                RUT
                                <input type="file" id="rut" name="rut" class="custom-file-input">
                                <span class="custom-file-control"></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-check-label">
                                <input id="prime" name="prime" class="form-check-input" type="checkbox">
                                Usuario Prime
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="contrato" class="form-check-label">
                                <input class="form-check-input"  type="checkbox" id="contract" name="contract" required />
                                Contrato <a href="pagina_condiciones.html">terminos</a>
                            </label>
                        </div>


                        <div class="form-group">
                            <label for="condiciones" class="form-check-label">
                                <input class="form-check-input"  type="checkbox" id="terms" name="terms" required />
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
<script src="assets/js/scripts.js"></script>

<!--[if lt IE 10]>
<script src="assets/js/placeholder.js"></script>

<![endif]-->

<script>
    $( document ).ready(function() {

        var valor = $('#type_client').val();

        if (valor != 83) {

            $("#two").remove();

            $("#four").remove();

            campo = '<fieldset id="four">\n' +
                '                        <h4>Configurar tu usuario:</h4>\n' +
                '\n' +
                '                        @if(isset($email))\n' +
                '                            <div class="form-group">\n' +
                '                                <label for="f1-email">Código</label>\n' +
                '                                <input type="email" name="code" placeholder="Código..." class="f1-email form-control" id="code"  value="{{$email}}" required disabled="true">\n' +
                '                            </div>\n' +
                '                        @else\n' +
                '                            <div class="form-group">\n' +
                '                                <label for="f1-email">Código</label>\n' +
                '                                <input type="email" name="code" placeholder="Código..." class="f1-email form-control" id="code" required>\n' +
                '                            </div>\n' +
                '                        @endif\n' +
                '\n' +
                '                        <div class="form-group">\n' +
                '                            <label for="f1-email">Email</label>\n' +
                '                            <input type="email" name="email" placeholder="Email..." class="f1-email form-control" id="email" required>\n' +
                '                        </div>\n' +
                '                        <div class="form-group">\n' +
                '                            <label for="f1-password">Contraseña</label>\n' +
                '                            <input type="password" name="password" placeholder="Contraseña..." class="f1-password form-control" id="password" required>\n' +
                '                        </div>\n' +
                '                        <div class="form-group">\n' +
                '                            <label  for="f1-repeat-password">Repetir Contraseña</label>\n' +
                '                            <input type="password" name="password_confirmation" placeholder="Repeter Contraseña..."\n' +
                '                                   class="f1-repeat-password form-control" id="password_confirmation" required>\n' +
                '                        </div>\n' +
                '\n' +
                '                        <div class="form-check">\n' +
                '                            <label for="contrato" class="form-check-label">\n' +
                '                                <input class="form-check-input"  type="checkbox" id="contract" name="contract" required />\n' +
                '                                Contrato <a href="pagina_condiciones.html">terminos</a>\n' +
                '                            </label>\n' +
                '                        </div>\n' +
                '\n' +
                '\n' +
                '                        <div class="form-check">\n' +
                '                            <label for="condiciones" class="form-check-label">\n' +
                '                                <input class="form-check-input"  type="checkbox" id="terms" name="terms" required />\n' +
                '                                ¿Acepta <a href="pagina_condiciones.html">terminos</a> y condiciones?\n' +
                '                            </label>\n' +
                '                        </div>\n' +
                '\n' +
                '                        <div class="f1-buttons">\n' +
                '                            <button type="button" class="btn btn-previous">Anterior</button>\n' +
                '                            <button type="submit" class="btn btn-submit">Crear</button>\n' +
                '                        </div>\n' +
                '                    </fieldset>';
            $("#tree").after(campo);

            $("#five").remove();

        }

        $('#birthday').datepicker();


        $('#type_client').select2({
            placeholder: "Tipo Cliente"
        });

        $('#type_dni').select2({
            placeholder: "Tipo Documento"
        });

        $('#city').select2({
            placeholder: "Ciudad"
        });

        var sex = [
            {
                id: "masculino",
                text: "Masculino"
            },
            {
                id: "femenimo",
                text: "Femenino"
            }
        ];

        $("#bank").select2({
            placeholder: "Escoger Banco..."
        });

        $("#type_acount_bank").select2({
            placeholder: "Tipo de cuenta bancaria",
        });

        $('#sex').select2({
            placeholder: "Sexo",
            data: sex
        });

        $('#type_client')
            .select2()
            .on('change', function (e) {

                  var valor = $('#type_client').val();

                  console.log(valor);

                  if (valor == 83) {

                      $("#one").remove();


                      campo =  campo = '<div id="one" class="f1-step">\n' +
                          '                            <div class="f1-step-icon"><i class="fa fa-key"></i></div>\n' +
                          '                            <p>Tu usuario</p>\n' +
                          '                        </div>';
                      $("#cero").after(campo);


                      campo1 = '<div id="two" class="f1-step">\n' +
                          '                            <div class="f1-step-icon"><i class="fa fa-check-square-o"></i></div>\n' +
                          '                            <p>Tus documentos</p>\n' +
                          '                        </div>';

                      $("#one").after(campo1);

                      $("#four").remove();

                      campo = '<fieldset id="four">\n' +
                          '                        <h4>Configurar tu usuario:</h4>\n' +
                          '\n' +
                          '                        @if(isset($email))\n' +
                          '                            <div class="form-group">\n' +
                          '                                <label for="f1-email">Código</label>\n' +
                          '                                <input type="email" name="code" placeholder="Código..." class="f1-email form-control" id="code"  value="{{$email}}" required disabled="true">\n' +
                          '                            </div>\n' +
                          '                        @else\n' +
                          '                            <div class="form-group">\n' +
                          '                                <label for="f1-email">Código</label>\n' +
                          '                                <input type="email" name="code" placeholder="Código..." class="f1-email form-control" id="code" required>\n' +
                          '                            </div>\n' +
                          '                        @endif\n' +
                          '\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label for="f1-email">Email</label>\n' +
                          '                            <input type="email" name="email" placeholder="Email..." class="f1-email form-control" id="email" required>\n' +
                          '                        </div>\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label for="f1-password">Contraseña</label>\n' +
                          '                            <input type="password" name="password" placeholder="Contraseña..." class="f1-password form-control" id="password" required>\n' +
                          '                        </div>\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label  for="f1-repeat-password">Repetir Contraseña</label>\n' +
                          '                            <input type="password" name="password_confirmation" placeholder="Repeter Contraseña..."\n' +
                          '                                   class="f1-repeat-password form-control" id="password_confirmation" required>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div class="f1-buttons">\n' +
                          '                            <button type="button" class="btn btn-previous">Anterior</button>\n' +
                          '                            <button type="button" class="btn btn-next">Siguiente</button>\n' +
                          '                        </div>\n' +
                          '                    </fieldset>';
                      $("#tree").after(campo);


                      campo1 = '<fieldset id="five">\n' +
                          '                        <h4>Documentos y condiciones:</h4>\n' +
                          '\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label for="bank">Seleccionar Entidad Bancaria</label>\n' +
                          '                            <select id="bank" name="bank" class="form-control" required style="width: 100% !important;">\n' +
                          '                                @foreach($bancos as $tipo)\n' +
                          '                                    <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>\n' +
                          '                                @endforeach\n' +
                          '                            </select>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label for="type_acount_bank">Tipo de cuenta </label>\n' +
                          '                            <select id="type_acount_bank" name="type_acount_bank" class="form-control" required style="width: 100% !important;">\n' +
                          '                                @foreach($cuentas->tipos as $tipo)\n' +
                          '                                    <option value="{{$tipo->id}}">{{ucwords($tipo->nombre)}}</option>\n' +
                          '                                @endforeach\n' +
                          '                            </select>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label for="acount">Número de cuenta</label>\n' +
                          '                            <input type="number" name="acount" placeholder="Documento..." class="f1-first-name form-control" id="acount">\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div id="cd-div" class="form-group">\n' +
                          '                            <label class="custom-file">\n' +
                          '                                Certificación bancaria\n' +
                          '                                <input type="file" id="cuenta" name="cuenta" class="custom-file-input">\n' +
                          '                                <span class="custom-file-control"></span>\n' +
                          '                            </label>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div id="cd-div" class="form-group">\n' +
                          '                            <label class="custom-file">\n' +
                          '                                Cédula o Documento\n' +
                          '                                <input type="file" id="cedula" name="cedula" class="custom-file-input">\n' +
                          '                                <span class="custom-file-control"></span>\n' +
                          '                            </label>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div id="rut-div" class="form-group">\n' +
                          '                            <label class="custom-file">\n' +
                          '                                RUT\n' +
                          '                                <input type="file" id="rut" name="rut" class="custom-file-input">\n' +
                          '                                <span class="custom-file-control"></span>\n' +
                          '                            </label>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label class="form-check-label">\n' +
                          '                                <input id="prime" name="prime" class="form-check-input" type="checkbox">\n' +
                          '                                Usuario Prime\n' +
                          '                            </label>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label for="contrato" class="form-check-label">\n' +
                          '                                <input class="form-check-input"  type="checkbox" id="contract" name="contract" required />\n' +
                          '                                Contrato <a href="pagina_condiciones.html">terminos</a>\n' +
                          '                            </label>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label for="condiciones" class="form-check-label">\n' +
                          '                                <input class="form-check-input"  type="checkbox" id="terms" name="terms" required />\n' +
                          '                                ¿Acepta <a href="pagina_condiciones.html">terminos</a> y condiciones?\n' +
                          '                            </label>\n' +
                          '                        </div>\n' +
                          '                        <div class="f1-buttons">\n' +
                          '                            <button type="button" class="btn btn-previous">Anterior</button>\n' +
                          '                            <button type="submit" class="btn btn-submit">Crear</button>\n' +
                          '                        </div>\n' +
                          '                    </fieldset>';
                      $("#four").after(campo1);



                      $("#bank").select2({
                          placeholder: "Escoger Banco...",
                      });


                      $("#type_acount_bank").select2({
                          placeholder: "Tipo de cuenta bancaria",
                      });

                      // next step
                      $('.f1 .btn-next').on('click', function() {
                          var parent_fieldset = $(this).parents('fieldset');
                          var next_step = true;
                          // navigation steps / progress steps
                          var current_active_step = $(this).parents('.f1').find('.f1-step.active');
                          var progress_line = $(this).parents('.f1').find('.f1-progress-line');

                          // fields validation
                          parent_fieldset.find('input[type="text"], input[type="password"], textarea').each(function() {
                              if( $(this).val() == "" ) {
                                  $(this).addClass('input-error');
                                  next_step = false;
                              }
                              else {
                                  $(this).removeClass('input-error');
                              }
                          });
                          // fields validation

                          if( next_step ) {
                              parent_fieldset.fadeOut(400, function() {
                                  // change icons
                                  current_active_step.removeClass('active').addClass('activated').next().addClass('active');
                                  // progress bar
                                  bar_progress(progress_line, 'right');
                                  // show next step
                                  $(this).next().fadeIn();
                                  // scroll window to beginning of the form
                                  scroll_to_class( $('.f1'), 20 );
                              });
                          }

                      });

                      // previous step
                      $('.f1 .btn-previous').on('click', function() {
                          // navigation steps / progress steps
                          var current_active_step = $(this).parents('.f1').find('.f1-step.active');
                          var progress_line = $(this).parents('.f1').find('.f1-progress-line');

                          $(this).parents('fieldset').fadeOut(400, function() {
                              // change icons
                              current_active_step.removeClass('active').prev().removeClass('activated').addClass('active');
                              // progress bar
                              bar_progress(progress_line, 'left');
                              // show previous step
                              $(this).prev().fadeIn();
                              // scroll window to beginning of the form
                              scroll_to_class( $('.f1'), 20 );
                          });
                      });

                  } else {

                      $("#two").remove();

                      $("#four").remove();

                      campo = '<fieldset id="four">\n' +
                          '                        <h4>Configurar tu usuario:</h4>\n' +
                          '\n' +
                          '                        @if(isset($email))\n' +
                          '                            <div class="form-group">\n' +
                          '                                <label for="f1-email">Código</label>\n' +
                          '                                <input type="email" name="code" placeholder="Código..." class="f1-email form-control" id="code"  value="{{$email}}" required disabled="true">\n' +
                          '                            </div>\n' +
                          '                        @else\n' +
                          '                            <div class="form-group">\n' +
                          '                                <label for="f1-email">Código</label>\n' +
                          '                                <input type="email" name="code" placeholder="Código..." class="f1-email form-control" id="code" required>\n' +
                          '                            </div>\n' +
                          '                        @endif\n' +
                          '\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label for="f1-email">Email</label>\n' +
                          '                            <input type="email" name="email" placeholder="Email..." class="f1-email form-control" id="email" required>\n' +
                          '                        </div>\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label for="f1-password">Contraseña</label>\n' +
                          '                            <input type="password" name="password" placeholder="Contraseña..." class="f1-password form-control" id="password" required>\n' +
                          '                        </div>\n' +
                          '                        <div class="form-group">\n' +
                          '                            <label  for="f1-repeat-password">Repetir Contraseña</label>\n' +
                          '                            <input type="password" name="password_confirmation" placeholder="Repeter Contraseña..."\n' +
                          '                                   class="f1-repeat-password form-control" id="password_confirmation" required>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div class="form-check">\n' +
                          '                            <label for="contrato" class="form-check-label">\n' +
                          '                                <input class="form-check-input"  type="checkbox" id="contract" name="contract" required />\n' +
                          '                                Contrato <a href="pagina_condiciones.html">terminos</a>\n' +
                          '                            </label>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '\n' +
                          '                        <div class="form-check">\n' +
                          '                            <label for="condiciones" class="form-check-label">\n' +
                          '                                <input class="form-check-input"  type="checkbox" id="terms" name="terms" required />\n' +
                          '                                ¿Acepta <a href="pagina_condiciones.html">terminos</a> y condiciones?\n' +
                          '                            </label>\n' +
                          '                        </div>\n' +
                          '\n' +
                          '                        <div class="f1-buttons">\n' +
                          '                            <button type="button" class="btn btn-previous">Anterior</button>\n' +
                          '                            <button type="submit" class="btn btn-submit">Crear</button>\n' +
                          '                        </div>\n' +
                          '                    </fieldset>';
                      $("#tree").after(campo);

                      $("#five").remove();

                      // next step
                      $('.f1 .btn-next').on('click', function() {
                          var parent_fieldset = $(this).parents('fieldset');
                          var next_step = true;
                          // navigation steps / progress steps
                          var current_active_step = $(this).parents('.f1').find('.f1-step.active');
                          var progress_line = $(this).parents('.f1').find('.f1-progress-line');

                          // fields validation
                          parent_fieldset.find('input[type="text"], input[type="password"], textarea').each(function() {
                              if( $(this).val() == "" ) {
                                  $(this).addClass('input-error');
                                  next_step = false;
                              }
                              else {
                                  $(this).removeClass('input-error');
                              }
                          });
                          // fields validation

                          if( next_step ) {
                              parent_fieldset.fadeOut(400, function() {
                                  // change icons
                                  current_active_step.removeClass('active').addClass('activated').next().addClass('active');
                                  // progress bar
                                  bar_progress(progress_line, 'right');
                                  // show next step
                                  $(this).next().fadeIn();
                                  // scroll window to beginning of the form
                                  scroll_to_class( $('.f1'), 20 );
                              });
                          }

                      });

                      // previous step
                      $('.f1 .btn-previous').on('click', function() {
                          // navigation steps / progress steps
                          var current_active_step = $(this).parents('.f1').find('.f1-step.active');
                          var progress_line = $(this).parents('.f1').find('.f1-progress-line');

                          $(this).parents('fieldset').fadeOut(400, function() {
                              // change icons
                              current_active_step.removeClass('active').prev().removeClass('activated').addClass('active');
                              // progress bar
                              bar_progress(progress_line, 'left');
                              // show previous step
                              $(this).prev().fadeIn();
                              // scroll window to beginning of the form
                              scroll_to_class( $('.f1'), 20 );
                          });
                      });


                  }

                $('#type_client').select2({
                    language: "es",
                    placeholder: "Tipo Cliente"
                });

                $('#type_dni').select2({
                    language: "es",
                    placeholder: "Tipo documento"
                });
                $('#sex').select2({
                    language: "es",
                    placeholder: "Sexo"
                });

                $('#city').select2({
                    language: "es",
                    placeholder: "Ciudad"
                });

                $('#banco').select2({
                    language: "es",
                    placeholder: "Banco"
                });

            });

        // next step
        $('.f1 .btn-next').on('click', function() {
            var parent_fieldset = $(this).parents('fieldset');
            var next_step = true;
            // navigation steps / progress steps
            var current_active_step = $(this).parents('.f1').find('.f1-step.active');
            var progress_line = $(this).parents('.f1').find('.f1-progress-line');

            // fields validation
            parent_fieldset.find('input[type="text"], input[type="password"], textarea').each(function() {
                if( $(this).val() == "" ) {
                    $(this).addClass('input-error');
                    next_step = false;
                }
                else {
                    $(this).removeClass('input-error');
                }
            });
            // fields validation

            if( next_step ) {
                parent_fieldset.fadeOut(400, function() {
                    // change icons
                    current_active_step.removeClass('active').addClass('activated').next().addClass('active');
                    // progress bar
                    bar_progress(progress_line, 'right');
                    // show next step
                    $(this).next().fadeIn();
                    // scroll window to beginning of the form
                    scroll_to_class( $('.f1'), 20 );
                });
            }

        });

        // previous step
        $('.f1 .btn-previous').on('click', function() {
            // navigation steps / progress steps
            var current_active_step = $(this).parents('.f1').find('.f1-step.active');
            var progress_line = $(this).parents('.f1').find('.f1-progress-line');

            $(this).parents('fieldset').fadeOut(400, function() {
                // change icons
                current_active_step.removeClass('active').prev().removeClass('activated').addClass('active');
                // progress bar
                bar_progress(progress_line, 'left');
                // show previous step
                $(this).prev().fadeIn();
                // scroll window to beginning of the form
                scroll_to_class( $('.f1'), 20 );
            });
        });

    });


</script>

</body>

</html>