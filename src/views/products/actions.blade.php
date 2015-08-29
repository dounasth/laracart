@if ($product->trashed())
<a class="btn btn-flat btn-info" href="{{route('cart.products.restore', [$product->id])}}"><i class="fa fa-refresh"></i> {{trans('laradmin::actions.restore')}}</a>
@endif
<a class="btn btn-flat btn-info" href="{{route('cart.products.update', [$product->id])}}"><i class="fa fa-edit"></i> {{trans('laradmin::actions.edit')}}</a>
<a class="btn btn-flat btn-danger" href="{{route('cart.products.delete', [$product->id])}}"><i class="fa fa-edit"></i> {{trans('laradmin::actions.delete')}}</a>
<a class="btn btn-flat btn-link" href="{{route('site.cart.product.view', [$product->slug])}}" target="_blank"><i class="fa fa-link"></i> {{trans('laradmin::actions.preview')}}</a>