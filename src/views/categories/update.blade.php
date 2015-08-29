@extends('laradmin::layout')

@section('page-title')
{{ !$category->id ? 'Add' : 'Edit' }} Category
@stop

@section('page-subtitle')
{{ $category->name }} ({{ $category->id }})
@stop

@section('breadcrumb')
@parent
<li><a href="{{ route('cart.categories.list') }}" class="goOnCancel"><i class="fa fa-group"></i> Manage Categories</a></li>
<li class="active">{{ !$category->id ? 'Add' : 'Edit' }} Category</li>
@stop

@section('page-menu')
@stop

@section('styles')
@stop

@section('scripts')
@stop

@section('content')
<div class="box box-primary">
    {{ Form::open(array('route' => ['cart.categories.save', $category->id], 'method' => 'POST', 'role' => 'form')) }}
    <div class="box-body">
        <div class="row">
        	<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                <h2>General</h2>
                <div class="form-group">
                    {{ Form::label('parent_id', 'Parent:') }}
                    <select name="category[parent_id]" class="form-control">
                        <option value="">{{trans('laracart::general.select_one')}}</option>
                        @include('laracart::categories.select-options', array('categories'=>$categories, 'selected'=>$category->parent_id, 'prefix'=>''))
                    </select>
                </div>
                <div class="form-group">
                    {{ Form::label('title', 'Title:') }}
                    {{ Form::text('category[title]', $category->title, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('slug', 'Slug:') }}
                    {{ Form::text('category[slug]', $category->slug, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('description', 'Description:') }}
                    {{ Form::textarea('category[description]', $category->description, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('status', 'Status:') }}
                    {{ Form::select('category[status]', array('A' => 'Active', 'D' => 'Disabled'), $category->status, array('class' => 'form-control')) }}
                </div>
        	</div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <h2>Timestamps</h2>
            	<div class="form-group">
                    {{ Form::label('', 'Updated@: '.$category->updated_at) }}
            	</div>
            	<div class="form-group">
                    {{ Form::label('', 'Created@: '.$category->created_at) }}
            	</div>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <button class="btn btn-success" type="submit"><i class="fa fa-check-square-o"></i> Save</button>
        <a class="btn btn-danger btn-cancel"><i class="fa fa-square-o"></i> Cancel</a>
        <button class="btn btn-warning pull-right" type="submit" name="saveNew" value="1"><i class="fa fa-plus"></i> Save as new</button>
    </div>
    {{ Form::close() }}
</div>
@stop