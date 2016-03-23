@foreach ($tree as $category)
<li class="{{ $category->children->count() > 0 ? isset($class) ? $class : 'dropdown megamenu-fullwidth' : '' }}">
    <a data-toggle="{{ ($category->getDescendantCount() == 0) ? '' : 'dropdown' }}" class="{{ $category->children->count() > 0 ? 'dropdown-toggle' : '' }}"
       href="{{ route('site.cart.category.view', [$category->slug]) }}">
        <span>{{ $category->title }}  {{ $category->children->count() > 0 ? '<b class="caret"> </b>' : '' }} </span>
    </a>
    @if ( $category->children->count() > 0 )
    @var $split = floor(12/$category->children->count())
    @var $split = ($split<2) ? 2 : $split
    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
        <li class="megamenu-content ProductDetailsList">
            @foreach ($category->children as $level1)
            <ul class="col-lg-{{$split}}  col-sm-{{$split}} col-md-{{$split}} unstyled">
                <li class="no-border">
                    <a href="{{ route('site.cart.category.view', [$level1->slug]) }}">
                    <h4>{{$level1->title}}</h4>
                    </a>
                </li>
                @foreach ($level1->children()->take(12)->remember(3600*24)->get() as $level2)
                    <li><a href="{{ route('site.cart.category.view', [$level2->slug]) }}"> {{$level2->title}} </a></li>
                @endforeach
            </ul>
            @endforeach
        </li>
    </ul>
    @endif
</li>
@endforeach