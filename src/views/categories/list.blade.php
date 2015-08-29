@extends('laradmin::layout')

@section('page-title')
Manage Categories
@stop

@section('page-subtitle')
dashboard subtitle, some description must be here
@stop

@section('breadcrumb')
@parent
<li class="active">Manage Categories</li>
@stop

@section('page-menu')
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.categories.update') }}"><i class="fa fa-plus"></i> Add a new category</a></li>
<li role="presentation" class="divider"></li>
<li role="presentation"><a role="menuitem" tabindex="-1" href="#"><i class="fa fa-trash-o"></i> Delete selected categories</a></li>
<li role="presentation" class="divider"></li>
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.categories.trashed') }}"><i class="fa fa-trash-o"></i> Trashed</a></li>

<li role="presentation" class="pull-right"><a role="menuitem" tabindex="-1" href="{{ route('cart.categories.makeoldcats') }}"><i class="fa fa-trash-o"></i> makeoldcats</a></li>
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
//        jQuery("#example1").dataTable();
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
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            @include('laracart::categories.list-rows', array('categories'=>$categories, 'prefix'=>''))
            </tbody>
            <tfoot>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>&nbsp;</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
@stop