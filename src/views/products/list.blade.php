@extends('laradmin::layout')

@section('page-title')
Manage Products
@stop

@section('page-subtitle')
dashboard subtitle, some description must be here
@stop

@section('breadcrumb')
@parent
<li class="active">Manage Products</li>
@stop

@section('page-menu')
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.products.update') }}"><i class="fa fa-plus"></i> Add a new product</a></li>
<li role="presentation" class="divider"></li>
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.products.delete-many') }}" data-root="#example1 tbody" class="delete-selected"><i class="fa fa-trash-o"></i> Delete selected products</a></li>
<li role="presentation" class="divider"></li>
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.products.trashed') }}"><i class="fa fa-trash-o"></i> Trashed</a></li>
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.products.list') }}"><i class="fa fa-trash-o"></i> Not Trashed</a></li>
@stop

@section('styles')
<!-- DATA TABLES -->
<link href="{{ Config::get('laradmin::general.asset_path') }}/css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
@stop

@section('scripts')
<!-- DATA TABES SCRIPT -->
<script src="{{ Config::get('laradmin::general.asset_path') }}/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
<script src="{{ Config::get('laradmin::general.asset_path') }}/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#example1").dataTable();
    });
</script>
@stop

@section('content')
<div class="box box-primary">
    <div class="box-body table-responsive">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Price</th>
                <th>List Price</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
            <tr>
                <td>
                    <input type="checkbox" value="{{ $product->id }}">{{ $product->id }}
                </td>
                <td><a href="{{route('cart.products.update', [$product->id])}}">{{ $product->title }}</a></td>
                <td><a href="{{route('cart.products.update', [$product->id])}}">{{ $product->slug }}</a></td>
                <td>{{ $product->prices->price }}</td>
                <td>{{ $product->prices->list_price }}</td>
                <td>
                    @if ($product->trashed())
                        <a class="btn btn-flat btn-info" href="{{route('cart.products.restore', [$product->id])}}"><i class="fa fa-refresh"></i> {{trans('laradmin::actions.restore')}}</a>
                    @endif
                    <a class="btn btn-flat btn-info" href="{{route('cart.products.update', [$product->id])}}"><i class="fa fa-edit"></i> {{trans('laradmin::actions.edit')}}</a>
                    <a class="btn btn-flat btn-danger" href="{{route('cart.products.delete', [$product->id])}}"><i class="fa fa-edit"></i> {{trans('laradmin::actions.delete')}}</a>
                    <a class="btn btn-flat btn-link" href="{{route('site.cart.product.view', [$product->slug])}}" target="_blank"><i class="fa fa-link"></i> {{trans('laradmin::actions.preview')}}</a>
                </td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Price</th>
                <th>List Price</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
        </table>
        <button type="checkbox" class="btn btn-default toggle-select-all" data-root="#example1 tbody">Select / Deselect All</button>
    </div>
</div>
@stop