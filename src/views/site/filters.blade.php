@var $filters = \Bonweb\Laracart\Filter::forBlock($product_ids)
@foreach ($filters as $filter)
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title"><a data-toggle="collapse" href="category.html#collapse{{$filter['data']->id}}" class="collapseWill active ">
                {{$filter['data']->title}} <span class="pull-left"> <i class="fa fa-caret-right"></i></span> </a></h4>
    </div>
    <div id="collapse{{$filter['data']->id}}" class="panel-collapse collapse in">
        <div class="panel-body smoothscroll maxheight300">
            @foreach ($filter['values'] as $fvid => $fval)
            <div class="block-element">
                <label>
                    @if ($fval == $filter['selected'])
                    <a href="{{ route('site.cart.category.view', $category->slug) }}{{ \Bonweb\Laracart\Filter::removeFromLink($filter['data']->slug) }}">
                        {{$fval}}&nbsp;<i class="fa fa-times"></i>
                    </a>
                    @else
                    <a href="{{ route('site.cart.category.view', $category->slug) }}{{ \Bonweb\Laracart\Filter::makeLink($filter['data']->slug, $fval) }}">{{$fval}}</a>
                    @endif
                </label>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

@if (fn_is_not_empty($tags))
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title"><a data-toggle="collapse" href="category.html#collapseTags" class="collapseWill active ">
                Tags <span class="pull-left"> <i class="fa fa-caret-right"></i></span> </a></h4>
    </div>
    <div id="collapseTags" class="panel-collapse collapse in">
        <div class="panel-body smoothscroll maxheight300">
            @foreach ($tags as $tag)
            <div class="block-element">
                <label>
                    <a href="{{ \Bonweb\Laracart\Filter::makeCategoryFilterUrl($category, $tag) }}">
                        {{$tag->name}} ({{$tag->count}})
                    </a>
                </label>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif