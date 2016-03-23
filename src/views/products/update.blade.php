@extends('laradmin::layout')

@section('page-title')
{{ !$product->id ? 'Add' : 'Edit' }} Product
@stop

@section('page-subtitle')
{{ $product->title }} {{ $product->id }}
@stop

@section('breadcrumb')
@parent
<li><a href="{{ route('cart.products.list') }}" class="goOnCancel"><i class="fa fa-group"></i> Manage Products</a></li>
<li class="active">{{ !$product->id ? 'Add' : 'Edit' }} Product</li>
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
    {{ Form::open(array('route' => ['cart.products.save', $product->id], 'method' => 'POST', 'role' => 'form', 'enctype'=>'multipart/form-data')) }}
    <div class="box-body">
        <div class="row">
            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                <h2>General</h2>
                <div class="form-group">
                    {{ Form::label('title', 'Title:') }}
                    {{ Form::text('product[title]', $product->title, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seo-title', 'SEO Title:') }}
                    {{ Form::text('seo[title]', ($product->seo) ? $product->seo->title : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('slug', 'Slug:') }}
                    {{ Form::text('product[slug]', $product->slug, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('sku', 'SKU:') }}
                    {{ Form::text('product[sku]', $product->sku, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('description', 'Short Description:') }}
                    {{ Form::textarea('product[short_description]', ($product->descriptions) ? $product->descriptions->short : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('description', 'Full Description:') }}
                    {{ Form::textarea('product[full_description]', ($product->descriptions) ? $product->descriptions->full : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seo-description', 'Meta Description:') }}
                    {{ Form::textarea('seo[description]', ($product->seo) ? $product->seo->description : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('seo-keywords', 'Meta Keywords:') }}
                    {{ Form::textarea('seo[keywords]', ($product->seo) ? $product->seo->keywords : '', array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('tags', 'Tags:') }}
                    {{ Form::text('product[tags]', $product->tagNamesInline(), array('id' => 'tags', 'class' => 'form-control', 'data-role' => 'tagsinput')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('status', 'Status:') }}
                    {{ Form::select('product[status]', array('A' => 'Active', 'D' => 'Disabled'), $product->status, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('affiliate_url', 'If affiliate product, enter here the url:') }}
                    {{ Form::text('product[affiliate_url]', ($product->affiliateUrl) ? $product->affiliateUrl->url : '', array('class' => 'form-control')) }}
                </div>

                <h2>Meta Fields</h2>
                @foreach (Config::get('laracart::product-meta') as $key => $name)
                <div class="form-group">
                    {{ Form::label('meta-'.$key, $name) }}
                    {{ Form::text('meta['.$key.']', $product->meta($key), array('class' => 'form-control')) }}
                </div>
                @endforeach

            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <h2>Basic Data</h2>

                <div class="form-group">
                    {{ Form::label('main_category', 'Parent:') }}
                    <input id="main_category" type="text" name="product[main_category]" value="{{ $product->mainCategory()->id }}"
                           class="form-control categories" data-role="tagsinput"
                           data-selected-id="{{ $product->mainCategory()->id }}"
                           data-selected-name="{{ $product->mainCategory()->path() }}"
                        />
                </div>

                <div class="form-group">
                    {{ Form::label('additional_categories', 'Additional:') }}
                    <input id="additional_categories" type="text" name="product[additional_categories][]" value="{{ implode(',', $product->additionalCategories()->lists('id')) }}"
                           class="form-control categories" data-role="tagsinput"
                        />
                </div>

                <div class="form-group">
                    {{ Form::label('price', 'Price:') }}
                    {{ Form::text('product[price]', ($product->prices) ? $product->prices->price : 0 , array('class' => 'form-control')) }}
                </div>

                <div class="form-group">
                    {{ Form::label('list_price', 'List Price:') }}
                    {{ Form::text('product[list_price]', ($product->prices) ? $product->prices->list_price : 0, array('class' => 'form-control')) }}
                </div>

                <div class="form-group">
                    {{ Form::label('main_image', 'Main Image:') }}
                    @if ($product->mainPhoto())
                    <img src="{{ $product->mainPhoto()->httpPath() }}" class="img-responsive img-rounded" />
                    @endif
                    {{ Form::text('product[main_image]', $product->mainPhoto() ? $product->mainPhoto()->path : '' , array('class' => 'form-control')) }}
                    {{ Form::file('files[main_image]', '', array('class' => 'form-control')) }}
                </div>

                <h2>Timestamps</h2>
                <div class="form-group">
                    {{ Form::label('', 'Updated@: '.$product->updated_at) }}
                </div>
                <div class="form-group">
                    {{ Form::label('', 'Created@: '.$product->created_at) }}
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