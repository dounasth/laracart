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
<link href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
@stop

@section('scripts')
<!-- DATA TABES SCRIPT -->
<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        $('#example1').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("cart.products.data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'photo', name: 'photo' },
                { data: 'title', name: 'title' },
                { data: 'slug', name: 'slug' },
                { data: 'prices.price', name: 'price' },
                { data: 'prices.list_price', name: 'list_price' },
                { data: 'action', name: 'action' }
            ]
        });
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
                <th>Image</th>
                <th>Title</th>
                <th>Slug</th>
                <th>Price</th>
                <th>List Price</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
        </table>
        <button type="checkbox" class="btn btn-default toggle-select-all" data-root="#example1 tbody">Select / Deselect All</button>
    </div>
</div>
@stop