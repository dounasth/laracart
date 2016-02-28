@extends('laradmin::site.site-layout')

@section('page-title')
@var $catTitle = ($category->seo && $category->seo->title) ? $category->seo->title : $category->title
{{ \Bonweb\Laracart\Filter::currentAsText($catTitle) }}
@stop

@section('page-subtitle')

@stop

@section('breadcrumb')
@parent
<li class="active"><a href="{{ route('site.cart.category.view', $category->slug) }}">{{ $category->path() }}</a></li>
@stop

@section('page-menu')
@stop

@section('meta')
<link rel="canonical" href="{{ route('site.cart.category.view', $category->slug).\Bonweb\Laracart\Filter::makeCanonicalLink() }}" />
<meta name="robots" content="{{\Bonweb\Laracart\Filter::makeIndexFollow()}}" />
@stop

@section('styles')
@stop

@section('scripts')
@var $j=0
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
  @foreach ($category->getAncestors() as $i => $ancestor)
    {
      "@type": "ListItem",
      "position": {{ $i+1 }},
      "item": {
        "@id": "{{ route('site.cart.category.view', $ancestor->slug) }}",
        "name": "{{ $ancestor->title }}"
      }
    },
    @var $j=$i+1
  @endforeach
    {
      "@type": "ListItem",
      "position": {{ $j+1 }},
      "item": {
        "@id": "{{ route('site.cart.category.view', $category->slug) }}",
        "name": "{{ $category->title }}"
      }
    }
  ]
}
</script>
@stop

@section('content')


<div class="container main-container globalPaddingTop">

<div class="row">
    <div class="breadcrumbDiv col-lg-12">
        <ul class="breadcrumb">
            @include('laradmin::site.breadcrumb')
        </ul>
    </div>
</div>

<div class="row">

<div class="col-lg-3 col-md-3 col-sm-12">
<div class="panel-group" id="accordionNo">

@include('laracart::site.filters', ['product_ids'=>$product_ids])

</div>
</div>

<div class="col-lg-9 col-md-9 col-sm-12">
<div class="w100 clearfix category-top">
    <h2> @yield('page-title') </h2>
    <small>@yield('page-subtitle')</small>
    @if ($category->description)
    <p>{{ $category->description }}</p>
    @endif

    @if ($category->children)
    <div class="row subCategoryList clearfix">
        @foreach ($category->children as $sub)
        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-4  text-center ">
            <div class="thumbnail equalheight" style="height: 123px;">
                <!-- <a href="{{ route('site.cart.category.view', $sub->slug) }}" class="subCategoryThumb">
                    <img alt="{{ ($sub->seo && $sub->seo->title) ? $sub->seo->title : $sub->title }}" class="img-rounded " src="images/product/3.jpg">
                </a> -->
                <a href="{{ route('site.cart.category.view', $sub->slug) }}" class="subCategoryTitle"><span>{{ ($sub->seo && $sub->seo->title) ? $sub->seo->title : $sub->title }}</span></a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@include('laracart::site.category-products-grid')

<div class="w100 categoryFooter">
    <div class="pagination pull-left no-margin-top">
        {{ $products->appends(\Bonweb\Laracart\Filter::getLinkParams())->links() }}
    </div>
</div>

</div>

</div>

</div>


@stop