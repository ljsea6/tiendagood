@extends('templates.dash')

@section('titulo','Good')

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
         <p class="category">Estos son sus puntos</p>
         <h3 class="title">{{ number_format($my_points) }}
            <small>Pts</small>
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
      <div class="card-header" data-background-color="blue">
         <i class="fa fa-money" aria-hidden="true"></i>
      </div>
      <div class="card-content">
         <p class="category">Estos son sus puntos Vendidos</p>
         <h3 class="title">{{ number_format($points_level_1 + $points_level_2 + $points_level_3) }}
            <small>Pts</small>
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
      <div class="card-content">
         <p class="category">Su patrocinador es</p> <!--<h1 class="hidden-xs" style="text-transform: uppercase; color: gray; padding: 20px; font-size: 1.8em;">  <br>-->
         <h3 class="title">{{ ucwords($nombre_completo) }}</h3>
         <p class="category">Email: {{$email}} - Teléfono: {{$telefono}}</p>
      </div>
      <div class="card-footer" style="display: block;">
         <div class="stats">
            <i class="material-icons text-danger"></i>
            <span style="font-family: 'Bungee', cursive; color: black;font-size: 16px;"> Fecha del proximo corte 21 de diciembre</span>
         </div>
      </div>
   </div>

</div>

<div class="col-lg-12 col-md-12">
   <div class="card">
      <div class="card-content table-responsive">
         <table class="table table-hover">
            <thead class="text-warning">
            </thead>
            <tbody>
               <tr onclick="link(1)">
                  <td>
                     <div class="card-header" data-background-color="orange" style="width: 80px; height: 80px; margin-top: 10px; border-radius: 50%; position: relative;">
                        <i class="fa" style="font-size: 25px; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); font-family: 'Roboto Slab', serif; font-weight: 800;">1</i>
                     </div>
                  </td>

                  <td>#{{$uno}} <span class="fa fa-user" aria-hidden="true"></span></td> 
                  <td>{{ number_format($points_level_1) }} Puntos</td> 
                  <td>$ 0</td>
                  </a>
               </tr>
               <tr onclick="link(2)">
                  <td>
                     <div class="card-header" data-background-color="orange" style="width: 80px; height: 80px; margin-top: 10px; border-radius: 50%; position: relative;">
                        <i class="fa" style="font-size: 25px; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); font-family: 'Roboto Slab', serif; font-weight: 800;">2</i>
                     </div>
                  </td>
                  <td>#{{$dos}} <span class="fa fa-user" aria-hidden="true"></span></td>
                  <td>{{ number_format($points_level_2) }} Puntos</td> 
                  <td>$ 0</td>
               </tr>
               <tr onclick="link(3)">
                  <td>
                     <div class="card-header" data-background-color="orange" style="width: 80px; height: 80px; margin-top: 10px; border-radius: 50%; position: relative;">
                        <i class="fa" style="font-size: 25px; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); font-family: 'Roboto Slab', serif; font-weight: 800;">3</i>
                     </div>
                  </td>
                  <td>#{{$tres}} <span class="fa fa-user" aria-hidden="true"></span></td>
                  <td>{{ number_format($points_level_3) }} Puntos</td> 
                  <td>$ 0</td>
               </tr>
            </tbody>
         </table><br><br><br>
      </div>
   </div>
</div>

<script type="text/javascript"> function link(nivel){ location.href = "/nivel/"+nivel; } </script>

</section> 

@endsection