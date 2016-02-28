@extends('laradmin::site.site-layout')

@section('page-title')
    @foreach ($tags as $tag)
    {{ $tag->name }}
    @endforeach
@stop

@section('page-subtitle')

@stop

@section('breadcrumb')
    @parent
    @var $prefix = []
    @foreach ($tags as $tag)
        <li><a href="{{ route('site.cart.tag.view', implode(',', array_merge($prefix, [$tag->slug]))) }}">{{ $tag->name }}</a></li>
        @var $prefix[] = $tag->slug
    @endforeach
@stop

@section('page-menu')
@stop

@section('styles')
@stop

@section('scripts')
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
  @var $prefix = []
  @foreach ($tags as $i => $tag)
    {
      "@type": "ListItem",
      "position": {{ $i+1 }},
      "item": {
        "@id": "{{ route('site.cart.tag.view', implode(',', array_merge($prefix, [$tag->slug]))) }}",
        "name": "{{ $tag->name }}"
      }
    } {{ ($tag != end($tags)) ? ',' : '' }}
  @var $prefix[] = $tag->slug
  @endforeach
  ]
}
</script>
@stop

@section('content')




<div class="container main-container headerOffset">

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
                @if (fn_is_not_empty($mtags))
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-toggle="collapse" href="category.html#collapseTags" class="collapseWill active ">
                                Tags <span class="pull-left"> <i class="fa fa-caret-right"></i></span> </a></h4>
                    </div>
                    <div id="collapseTags" class="panel-collapse collapse in">
                        <div class="panel-body smoothscroll maxheight300">
                            @foreach ($mtags as $tag)
                            <div class="block-element">
                                <label>
                                    <a class="" href="{{ route('site.cart.tag.view', implode(',', [$slugs, $tag->slug])) }}">
                                        {{$tag->name}} ({{$tag->count}})
                                    </a>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-9 col-md-9 col-sm-12">
            <div class="w100 clearfix category-top">
                <h2> @yield('page-title') </h2>
                <small>@yield('page-subtitle')</small>
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