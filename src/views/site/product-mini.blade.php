
@if ($product instanceof Bonweb\Laracart\Product)
@else
@var $product = Bonweb\Laracart\Product::find($product)
@endif

<div class="product mini" data-id="{{$product->id}}">
    <div class="image">
        <a target="_blank" rel="nofollow" href="{{ $product->getAffiliateRoute() }}">
            @if (@$product->imported->merchant->id == 18)
            <img src="{{ $product->mainPhoto()->httpPath() }}" alt="{{ $product->title }}" class="img-responsive">
            @else
            <img src="{{ $product->mainPhoto()->photon(260) }}" alt="{{ $product->title }}" class="img-responsive">
            @endif
        </a>
        <div class="promotion">
            @if (false)
            <span class="new-product"> NEW</span>
            @endif
            @if ( (int)$product->prices->discount() > 0 )
            <span class="discount">{{(int)$product->prices->discount() }}% Έκπτωση</span>
            @endif
        </div>
    </div>
    <div>{{$product->meta('brand')}}</div>
    <div class="">
        <h4><a target="_blank" rel="nofollow" href="{{ $product->getAffiliateRoute() }}">{{ $product->title }}</a></h4>
        <div class="">
            <p>
                <a target="_blank" rel="nofollow" href="{{ @$product->imported->merchant->merchant_url }}" target="_blank" style="display: inline;">
                    στο {{ @$product->imported->merchant->name }}
                </a>
            </p>
        </div>
    </div>
    <div class="price">
        <span>&euro;{{$product->prices->price}}</span>
        @if ($product->prices->list_price > $product->prices->price)
        <span class="old-price">&euro;{{$product->prices->list_price}}</span>
        @endif
    </div>
</div>