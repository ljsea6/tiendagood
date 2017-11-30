@extends('templates.dash')

@section('titulo','Good')

@section('content')


        {!! Html::style('css/material-dashboard.css?act=4') !!}

            <section class="invoice">
                <div class="col-lg-12">
    

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
        <img src="https://cdn.shopify.com/s/files/1/2256/3751/files/logo-good.png?3282421516940970096" alt="" style="width: 300px; padding: 20px 0">
    </div>
</div>
<div class="col-lg-4 col-md-6 col-sm-6">
   <div class="card card-stats">
      <div class="card-header" data-background-color="green">
         <i class="fa fa-money" aria-hidden="true"></i>
      </div>
      <div class="card-content">
         <p class="category">Estos son tus puntos</p>
         <h3 class="title">15,347,329
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
         <p class="category">Estos son tus puntos Vendidos</p>
         <h3 class="title">111
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
    <h1 class="hidden-xs" style="text-transform: uppercase; color: gray; padding: 20px; font-size: 1.8em;">Bienvenidos a my good</h1>
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
                     <div class="card-header" data-background-color="orange" style="width: 50px; height: 50px; margin-top: 10px; border-radius: 20px;">
                        <i class="fa" style="font-size: 25px">1</i>
                     </div>
                  </td>
                  <td>#{{$uno}} <span class="fa fa-user" aria-hidden="true"></span></td>
                  <td>467 Puntos</td>
                  <td>$ 3,334</td>
                  </a>
               </tr>
               <tr onclick="link(2)">
                  <td>
                     <div class="card-header" data-background-color="orange" style="width: 50px; height: 50px; margin-top: 10px; border-radius: 20px;">
                        <i class="fa">2</i>
                     </div>
                  </td>
                  <td>#{{$dos}} <span class="fa fa-user" aria-hidden="true"></span></td>
                  <td>353 Puntos</td>
                  <td>$ 3,334</td>
               </tr>
               <tr onclick="link(3)">
                  <td>
                     <div class="card-header" data-background-color="orange" style="width: 50px; height: 50px; margin-top: 10px; border-radius: 20px;">
                        <i class="fa">3</i>
                     </div>
                  </td>
                  <td>#{{$tres}} <span class="fa fa-user" aria-hidden="true"></span></td>
                  <td>353 Puntos</td>
                  <td>$ 3,334</td>
               </tr>
            </tbody>
         </table><br><br><br>
      </div>
   </div>
</div>

<script type="text/javascript"> function link(nivel){ location.href = "/nivel/"+nivel; } </script>

</section> 

@endsection