@extends('templates.dash')

@section('titulo','Cambiar Padre')

@section('content')
<<<<<<< HEAD
<div class="panel panel-default">
    <div class="panel-body">
        <div class="stepwizard">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step">
                    <a href="#step-1"  class="btn btn-primary btn-circle">1</a>
                    <p>Buscar</p>
                </div>
            </div>
        </div>
        <form action="#" method="post" class="form-horizontal">
            <div class="row setup-content" id="step-1">
                <div class="col-xs-12">
                    <div class="col-md-12">
                        <h3 class="text-center">Cambiar Padre</h3>
                        <hr>
                        <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" id="id" name="id" value="{{currentUser()->id}}">
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <label for="email">Ingresar correo o el numero de documento de la persona</label>
                                <input id="tercero" name="email" type="text" class="form-control" required>
                                <a class="btn btn-danger" onclick="buscarTercero()" role="button">Buscar</a>
                            </div>
                        </div>

                        <table id="referidos" class="table table-bordered font-12">
                            <thead>
                                <tr><th class="text-center" colspan="3">Datos Tercero</th></tr>
                                <tr>
                                    <th>Nombres</th>
                                    <th>Email</th>
                                    <th>Tipo Cliente</th>
                                </tr>
                            </thead>
                            <tbody class="tercero">
                                <tr class="primer">
                                    <td class="text-left" colspan="3"></td>
                                </tr>
                            </tbody>
                            <thead>
                                <tr><th class="text-center" colspan="3">Datos Padre</th></tr>
                                <tr>
                                    <th>Nombres</th>
                                    <th>Email</th>
                                    <th>Tipo Cliente</th>
                                </tr>
                            </thead>
                            <tbody class="padre">
                                <tr class="primer">
                                    <td class="text-left" colspan="3"></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

                                    function buscarTercero() {
                                        var padre = '';
                                        var tercero = '';
                                        $.ajax({url: '{{route("admin.terceros.padretercero")}}',
                                            dataType: "json", type: "POST",
                                            data: {email: $("#tercero").val(), id: $("#id").val(), _token: $("#_token").val()},
                                            success: function (data) {
                                                if (!data.error) {
                                                    tercero += '<tr>';
                                                    tercero += '<td class="text-left">' + data.tercero.nombre + '</td>';
                                                    tercero += '<td class="text-left">' + data.tercero.email + '</td>';
                                                    tercero += '<td class="text-left">' + data.tercero.tipo_cliente + '</td>';
                                                    tercero += '</tr>';
                                                    if (!data.error) {
                                                        padre += '<tr>';
                                                        padre += '<td class="text-left">' + data.padre.nombre + '</td>';
                                                        padre += '<td class="text-left">' + data.padre.email + '</td>';
                                                        padre += '<td class="text-left">' + data.padre.tipo_cliente + '</td>';
                                                        padre += '</tr>';
                                                    } else {
                                                        padre += '<tr>';
                                                        padre += '<td style="text-align:center" colspan="3">' + data.tercero.error + '</td>';
                                                        padre += '</tr>';
                                                    }
                                                } else {
                                                    tercero += '<tr>';
                                                    tercero += '<td style="text-align:center" colspan="3">' + data.error + '</td>';
                                                    tercero += '</tr>';
                                                }
                                                $(".tercero").html(tercero);
                                                $(".padre").html(padre);
                                            }
                                        });
                                    }

</script>

