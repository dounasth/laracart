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
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
    <link href="{{ Config::get('laradmin::general.asset_path') }}/css/bootstrap-tags/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
@stop

@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="{{ Config::get('laradmin::general.asset_path') }}/js/bootstrap-tags/bootstrap-tagsinput.js" type="text/javascript"></script>
    <script src="{{ Config::get('laradmin::general.asset_path') }}/js/typeahead.bundle.js" type="text/javascript"></script>
    <script>
        /* Categories */
        var categories = new Bloodhound({
            name: 'categories',
            datumTokenizer: function(d) {
                return Bloodhound.tokenizers.whitespace(d.name);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: {
                url: '/json/categories',
                cache: false
            }
        });
        categories.initialize();

        $('#main_category').tagsinput({
            maxTags: 1,
            freeInput: false,
            itemValue: 'id',
            itemText: 'name',
            typeaheadjs: {
                highlight: true,
                limit: 20,
                name: 'categories',
                displayKey: 'name',
                source: categories.ttAdapter()
            }
        });
        $('#main_category').each(function(i, elm){
            var id = $(elm).attr('data-selected-id');
            if (id > 0) {
                var name = $(elm).attr('data-selected-name');
                $(elm).tagsinput('add', { "id": id , "name": name });
            }
        });
    </script>
@stop

@section('content')
<div class="box box-primary">
    {{ Form::open(array('route' => ['cart.categories.save', $category->id], 'method' => 'POST', 'role' => 'form', 'files' => true)) }}
    <div class="box-body">
        <div class="row">
        	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
                <h2>General</h2>
                <div class="form-group">
                    {{ Form::label('parent_id', 'Parent:') }}
                    <input id="main_category" type="text" name="category[parent_id]" value="{{ $category->parent_id }}"
                           class="form-control categories" data-role="tagsinput"
                           data-selected-id="{{ $category->parent_id }}"
                           data-selected-name="{{ $category->parent ? $category->parent->path() : 0 }}"
                    />
                </div>
                <div class="form-group">
                    {{ Form::label('title', 'Title:') }}
                    {{ Form::text('category[title]', $category->title, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seo-title', 'SEO Title:') }}
                    {{ Form::text('seo[title]', ($category->seo) ? $category->seo->title : '', array('class' => 'form-control')) }}
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
                    {{ Form::label('seo-description', 'Meta Description:') }}
                    {{ Form::textarea('seo[description]', ($category->seo) ? $category->seo->description : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seo-keywords', 'Meta Keywords:') }}
                    {{ Form::textarea('seo[keywords]', ($category->seo) ? $category->seo->keywords : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('status', 'Status:') }}
                    {{ Form::select('category[status]', array('A' => 'Active', 'D' => 'Disabled'), $category->status, array('class' => 'form-control')) }}
                </div>
        	</div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
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