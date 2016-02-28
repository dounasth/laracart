@var $count_from = (isset($count_from)) ? $count_from : 100
@var $tags = (isset($tags)) ? $tags : Conner\Tagging\Tag::where('count', '>=', $count_from)->orderBy('count', 'desc')->get()

@if (fn_is_not_empty($tags))
<div class="row">
    <div class="col-lg-12 col-xs-12">
        <h3 class="section-title"><span> TAGS</span></h3>
        <div class="box-body">
            @foreach ($tags as $tag)
                <a class="btn btn-text" href="{{ route('site.cart.tag.view', $tag->slug) }}">
                    {{$tag->name}}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif