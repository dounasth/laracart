@var $count_from = (isset($count_from)) ? $count_from : 100
@var $tags = (isset($tags)) ? $tags : Conner\Tagging\Tag::where('count', '>=', $count_from)->orderBy('count', 'desc')->get()

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