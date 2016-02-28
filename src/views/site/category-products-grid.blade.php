
<div class="row  categoryProduct xsResponse clearfix">
    @foreach ($products as $product)
        <div class="item col-sm-4 col-lg-4 col-md-4 col-xs-6">
            @include('laracart::site.product-mini', ['product'=>$product])
        </div>
    @endforeach
</div>