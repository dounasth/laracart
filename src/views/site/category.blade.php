@extends('laradmin::site.site-layout')

@section('page-title')
{{ $category->title }}
@stop

@section('page-subtitle')

@stop

@section('breadcrumb')
@parent
:: {{ $category->path() }}
@stop

@section('page-menu')
@stop

@section('styles')
@stop

@section('scripts')
@stop

@section('content')

<div class="row">
    <div class="col-lg-4">
        @include('laracart::site.filters', ['product_ids'=>$category->products->lists('id')])
    </div>
    <div class="col-lg-8">
        <div class="row">
            @foreach ($products as $product)
            <div class="col-lg-3 col-xs-6">
                <div class="box box-primary">
                    <div class="box-body">
                        <img src="{{ $product->mainPhoto()->httpPath() }}" class="img-responsive img-rounded" style="max-width: 100%; max-height: 200px;" />
                        <div style="height: 50px; text-align: center;">
                            <a href="{{ route('site.cart.product.view', [$product->slug]) }}">
                                {{ $product->title }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@stop