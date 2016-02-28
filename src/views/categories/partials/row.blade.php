<li class="dd-item dd3-item" data-id="{{$category->id}}">
    <div class="dd-handle dd3-handle "></div>
    <div class="dd3-content ">
        <div class="pull-left">
            <a class="translatable" href="{{route('cart.categories.update', [$category->id])}}">{{ $category->title }}</a>
        </div>
        @if (@$checkable)
        <div class="pull-right">
            <div class="radio-inline">
                <label><input type="radio" name="selection" value="before"/> before</label>
            </div>
            <div class="radio-inline">
                <label><input type="radio" name="selection" value="after"/> after</label>
            </div>
        </div>
        @endif
        @if (!@$readonly)
        <div class="btn-group pull-right">
            <a class="btn btn-sm btn-default hidden-sm hidden-xs" >{{@$category->seo->title}}</a>
            <a class="btn btn-sm btn-default hidden-sm hidden-xs" >{{$category->slug}}</a>
            <a class="btn btn-sm btn-default hidden-sm hidden-xs" href="{{route('cart.categories.update', [$category->id])}}"><i class="fa fa-edit"></i></a>
            <a class="btn btn-sm btn-danger" href="{{route('cart.categories.delete', [$category->id])}}"><i class="fa fa-edit"></i></a>
        </div>
        @endif
    </div>

    @if(count($category->children)>0)
    <ol class="dd-list">
        @foreach($category->children as $sub)
        @include('laracart::categories.partials.row', ['category'=>$sub])
        @endforeach
    </ol>
    @endif
</li>