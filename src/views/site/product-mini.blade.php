@var $imp = \ImportProducts::whereProductId($product->id)->first()
@var $merchant = \Merchant::whereId($imp->merchant_id)->first()

<div class="product mini" data-id="{{$product->id}}">
    <div class="image">
        <a target="_blank" rel="nofollow" href="{{ ($product->affiliateUrl) ? $product->affiliateUrl->url.'&subid1=shoes-findemall' : '#' }}">
            @if ($merchant->id == 18)
            <img src="{{ $product->mainPhoto()->httpPath() }}" alt="{{ $product->title }}" class="img-responsive">
            @else
            <img src="{{ $product->mainPhoto()->photon(260) }}" alt="{{ $product->title }}" class="img-responsive">
            @endif
        </a>
        <div class="promotion">
            @if (false)
            <span class="new-product"> NEW</span>
            @endif
            @if (fn_is_not_empty($product->meta('discount')))
            <span class="discount">{{(int)$product->meta('discount')}}% OFF</span>
            @endif
        </div>
    </div>
    <div class="">
        <h4><a target="_blank" rel="nofollow" href="{{ ($product->affiliateUrl) ? $product->affiliateUrl->url.'&subid1=shoes-findemall' : '#' }}">{{ $product->title }}</a></h4>
        <div class="">
            <p class="tags truncate">
                @foreach ($product->tagged as $tag)
                {{$tag->tag_name}},
                @endforeach
            </p>
            <p>

                <a target="_blank" rel="nofollow" href="{{ $merchant->merchant_url }}" target="_blank" style="display: inline;">
                    {{ $merchant->name }}
                </a>
            </p>
        </div>
    </div>
</div>