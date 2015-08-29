@extends('laradmin::layout')

@section('page-title')
Manage Filters
@stop

@section('page-subtitle')
dashboard subtitle, some description must be here
@stop

@section('breadcrumb')
@parent
<li class="active">Manage Filters</li>
@stop

@section('page-menu')
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.filters.update') }}"><i class="fa fa-plus"></i> Add a new filter</a></li>
<li role="presentation" class="divider"></li>
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.filters.delete-many') }}" data-root="#example1 tbody" class="delete-selected"><i class="fa fa-trash-o"></i> Delete selected filters</a></li>
<li role="presentation" class="divider"></li>
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.filters.trashed') }}"><i class="fa fa-trash-o"></i> Trashed</a></li>
<li role="presentation"><a role="menuitem" tabindex="-1" href="{{ route('cart.filters.list') }}"><i class="fa fa-trash-o"></i> Not Trashed</a></li>
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
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($filters as $filter)
            <tr>
                <td>
                    <input type="checkbox" value="{{ $filter->id }}">{{ $filter->id }}
                </td>
                <td><a href="{{route('cart.filters.update', [$filter->id])}}">{{ $filter->title }}</a></td>
                <td><a href="{{route('cart.filters.update', [$filter->id])}}">{{ $filter->slug }}</a></td>
                <td>
                    @if ($filter->trashed())
                        <a class="btn btn-flat btn-info" href="{{route('cart.filters.restore', [$filter->id])}}"><i class="fa fa-refresh"></i> {{trans('laradmin::actions.restore')}}</a>
                    @endif
                    <a class="btn btn-flat btn-info" href="{{route('cart.filters.update', [$filter->id])}}"><i class="fa fa-edit"></i> {{trans('laradmin::actions.edit')}}</a>
                    <a class="btn btn-flat btn-danger" href="{{route('cart.filters.delete', [$filter->id])}}"><i class="fa fa-edit"></i> {{trans('laradmin::actions.delete')}}</a>
                </td>
            </tr>
            @endforeach
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
        <button type="checkbox" class="btn btn-default toggle-select-all" data-root="#example1 tbody">Select / Deselect All</button>
    </div>
</div>
@stop