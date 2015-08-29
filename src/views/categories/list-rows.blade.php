@foreach ($categories as $category)
<tr>
    <td>
        <input type="checkbox" value="{{ $category->slug }}">{{ $category->id }}
    </td>
    <td><a href="{{route('cart.categories.update', [$category->id])}}">{{ $prefix }} {{ $category->title }}</a></td>
    <td><a href="{{route('cart.categories.update', [$category->id])}}">{{ $category->slug }}</a></td>
    <td>
        @if ($category->trashed())
            <a class="btn btn-flat btn-info" href="{{route('cart.categories.restore', [$category->id])}}"><i class="fa fa-refresh"></i> {{trans('laradmin::actions.restore')}}</a>
        @endif
        <a class="btn btn-flat btn-info" href="{{route('cart.categories.update', [$category->id])}}"><i class="fa fa-edit"></i> {{trans('laradmin::actions.edit')}}</a>
        <a class="btn btn-flat btn-danger" href="{{route('cart.categories.delete', [$category->id])}}"><i class="fa fa-edit"></i> {{trans('laradmin::actions.delete')}}</a>
    </td>
</tr>
@if (fn_is_not_empty($category->children()->get()))
@include('laracart::categories.list-rows', array('categories'=>$category->children()->get(), 'prefix'=>$prefix.'|---'))
@endif
@endforeach