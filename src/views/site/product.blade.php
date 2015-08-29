@extends('laradmin::site.site-layout')

@section('page-title')
{{ $product->title }}
@stop

@section('page-subtitle')

@stop

@section('breadcrumb')
@parent
:: {{ $product->mainCategory()->path() }}
@stop

@section('page-menu')
@stop

@section('styles')
@stop

@section('scripts')
@stop

@section('content')
<div class="box box-primary">
    <div class="box-body">

        <div class="row">
            <div class="col-lg-6 col-xs-12">
                <img src="{{ $product->mainPhoto()->httpPath() }}" class="img-responsive img-rounded" style="max-width: 100%; max-height: 400px;" />
            </div>
            <div class="col-lg-6 col-xs-12">
                <div>SKU: {{$product->sku}}</div>
                <h2>Price: <label class="label label-success">{{ $product->prices->price }}</label></h2>
                @if ($product->prices->list_price != $product->prices->price)
                <h2>List Price: <strike><label class="label label-danger">{{ $product->prices->list_price }}</label></strike></h2>
                @endif
                <h2>Discount: <label class="label label-default">{{ $product->meta('discount') }}%</label></strike></h2>
                <div>
                    <h3>Short Description</h3>
                    <hr>
                    {{$product->descriptions->short}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_description" data-toggle="tab">Description</a></li>
                        <li><a href="#tab_meta" data-toggle="tab">Meta</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_description">
                            {{ $product->descriptions->full }}
                        </div><!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_meta">
                            @foreach ( $product->metasArray() as $key => $meta )
                            {{ $key . ': ' . $meta }}<br/>
                            @endforeach
                        </div><!-- /.tab-pane -->
                    </div><!-- /.tab-content -->
                </div><!-- nav-tabs-custom -->
            </div><!-- /.col -->
        </div> <!-- /.row -->

    </div>
</div>
@stop