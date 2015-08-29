@foreach ($tree as $category)
<li class="{{ $category->children->count() > 0 ? isset($class) ? $class : 'dropdown' : '' }}">
    <a data-toggle="{{ ($category->getDescendantCount() == 0) ? '' : 'dropdown' }}" class="{{ $category->children->count() > 0 ? 'dropdown-toggle' : '' }}"
       href="{{ ($category->getDescendantCount() == 0) ? route('site.cart.category.view', [$category->slug]) : '#' }}">
        <span>{{ $category->title }}</span>
    </a>
    @if ( $category->children->count() > 0 )
    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
        @include('laracart::site.menu', ['tree'=>$category->children, 'class'=>'dropdown-submenu'])
    </ul>
    @endif
</li>
@endforeach