@extends('templates.dash')

@section('titulo','Editar estado')

@section('content')
<div class="panel panel-default">
    <div class="panel-body">
        <h3 class="box-title">Editar estado</h3>
        <div class="box-body">
        <div class="col-xs-6">
            {!! Form::model($estado,['route' => ['admin.estados.update', $estado->id] , 'method' => 'PUT' ]) !!}
                {!! Field::text('nombre', ['ph' => 'Nombre','required']) !!}
                {!! Field::select('modulo', ['envios'=>'Envios','ordenes'=>'Ordenes'], ['required']) !!}
                {!! Field::text('alias', ['ph' => 'Alias/Abreviatura'],['required']) !!}
                {!! Field::number('prioridad',0, ['ph' => 'Prioridad']) !!}
                {!! Field::select('padre_id', $estados->toArray(), ['required']) !!}
                {!! Form::submit('Enviar!', ['class' => 'btn btn-success btn-lg pull-right']) !!}
            {!! Form::close() !!}
        </div>
        </div>
    </div>
</div>
@endsection