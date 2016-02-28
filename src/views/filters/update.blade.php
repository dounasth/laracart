@extends('laradmin::layout')

@section('page-title')
{{ !$filter->id ? 'Add' : 'Edit' }} Filter
@stop

@section('page-subtitle')
{{ $filter->title }} {{ $filter->id }}
@stop

@section('breadcrumb')
@parent
<li><a href="{{ route('cart.filters.list') }}" class="goOnCancel"><i class="fa fa-group"></i> Manage Filters</a></li>
<li class="active">{{ !$filter->id ? 'Add' : 'Edit' }} Filter</li>
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
<script type="text/javascript" charset="utf-8">
    /*$("#mainCategory").select2({
        placeholder: "Select a main category",
        allowClear: false
    });
    $("#additionalCategories").select2({
        placeholder: "Select additional categories",
        allowClear: true
    });*/
</script>
<script>
    var tags = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: '/json/tags',
            cache: false,
            filter: function(list) {
                return $.map(list, function(cityname) {
                    return { name: cityname }; });
            }
        }
    });
    tags.initialize();

    $('#tags').tagsinput({
        typeaheadjs: {
            name: 'tags',
            displayKey: 'name',
            valueKey: 'name',
            source: tags.ttAdapter()
        }
    });

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

    $('#additional_categories').tagsinput({
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
    $('#additional_categories').each(function(i, elm){
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
    {{ Form::open(array('route' => ['cart.filters.save', $filter->id], 'method' => 'POST', 'role' => 'form', 'enctype'=>'multipart/form-data')) }}
    <div class="box-body">
        <div class="row">
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                <h2>General</h2>
                <div class="form-group">
                    {{ Form::label('title', 'Title:') }}
                    {{ Form::text('filter[title]', $filter->title, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('meta_id', 'Meta Field:') }}
                    {{ Form::select('filter[meta_id]', array_replace(array(''=>'Select One'), \Bonweb\Laradmin\Meta::lists('name', 'id')), $filter->meta_id, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seo-title', 'SEO Title:') }}
                    {{ Form::text('seo[title]', ($filter->seo) ? $filter->seo->title : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('slug', 'Slug:') }}
                    {{ Form::text('filter[slug]', $filter->slug, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('show_on_canonical', 'Show on Canonical Url:') }}
                    {{ Form::select('filter[show_on_canonical]', array('1' => 'Yes', '0' => 'No'), $filter->show_on_canonical, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('indexing', 'REL Index:') }}
                    {{ Form::select('filter[indexing]', array('1' => 'Yes', '0' => 'No'), $filter->indexing, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('follow', 'REL Follow:') }}
                    {{ Form::select('filter[follow]', array('1' => 'Yes', '0' => 'No'), $filter->follow, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seo-description', 'Meta Description:') }}
                    {{ Form::textarea('seo[description]', ($filter->seo) ? $filter->seo->description : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seo-keywords', 'Meta Keywords:') }}
                    {{ Form::textarea('seo[keywords]', ($filter->seo) ? $filter->seo->keywords : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('status', 'Status:') }}
                    {{ Form::select('filter[status]', array('A' => 'Active', 'D' => 'Disabled'), $filter->status, array('class' => 'form-control')) }}
                </div>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <h2>Timestamps</h2>
                <div class="form-group">
                    {{ Form::label('', 'Updated@: '.$filter->updated_at) }}
                </div>
                <div class="form-group">
                    {{ Form::label('', 'Created@: '.$filter->created_at) }}
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