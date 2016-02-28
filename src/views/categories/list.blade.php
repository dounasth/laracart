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
<link rel="stylesheet" href="{{ Config::get('laradmin::general.asset_path') }}/css/jquery.nestable.css" type="text/css"/>
@stop

@section('scripts')
<script type="text/javascript" src="{{ Config::get('laradmin::general.asset_path') }}/js/jquery.nestable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {

        $('.nestable').each(function () {
            $(this).nestable({
                group: 1,
                onDragFinished: function(item, parent) {
                    var itemId = jQuery(item).data('id');
                    var parentId = jQuery(parent).data('id');
                    var prevId = jQuery(item).prev().data('id');
                    var nextId = jQuery(item).next().data('id');
                    var position = false;
                    var siblingId = false;

                    if (prevId && nextId) { position = 'after'; siblingId = prevId; }
                    else if (prevId && !nextId) { position = 'after'; siblingId = prevId; }
                    else if (!prevId && nextId) { position = 'before'; siblingId = nextId;}
                    else if (!prevId && !nextId) { position = false; siblingId = false; }

                    if (position != false && siblingId != false) {
                        $.post('{{route("cart.categories.save-pos-category")}}', {
                            item: itemId,
                            parent: parentId,
                            sibling: siblingId,
                            pos: position
                        }, function (response) {
                            console.info(response);
                        });
                    }
                }
            });//.on('change', updateFromNestable);
        });

//        function updateFromNestable(e, ui) {
//            var list = e.length ? e : $(e.target);
//            var action_url = $(e.currentTarget).data('action');
//            $.post(action_url, {
//                menu_order: list.nestable('serialize')
//            }, function (response) {
////                notifyJs(response);
//            });
//        }

        $('.nestable-menu').on('click', function(e)
        {
            var target = $(e.target),
                action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });
        jQuery('[data-action="collapse-all"]:first').click()
    });
</script>
<script type="text/javascript">

    function titlecase(str)
    {
        return str.replace(/(.)\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function camelcase(str) {
        return str
            .replace(/\s(.)/g, function ($1) {
                return $1.toUpperCase();
            })
            .replace(/\s/g, ' ')
            .replace(/^(.)/, function ($1) {
                return $1.toLowerCase();
            });
    }

    function gtranslate(sourceText) {

        sourceText = capitalizeFirstLetter(sourceText.toLowerCase());

        var sourceLang = 'en';

        var targetLang = 'el';

        var url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl="
            + sourceLang + "&tl=" + targetLang + "&dt=t&q=" + encodeURI(sourceText);
        var result;

        jQuery.ajax({
            url: url,
            async: false,
            success: function (response) {
                result = eval(response);
                result = result[0][0][0];
            },
            dataType: 'text'
        });

        var json = {
            'source': sourceText,
            'translation': capitalizeFirstLetter(camelcase(result))
        };

        return json;
    }

    jQuery(document).ready(function(){
        jQuery('.translatable').after('<a href="#" title="click to get translation suggestion" class="hidden-sm hidden-xs inline pad translator"><i class="fa fa-globe"></i></a>');
        jQuery(document).on('click', '.translator', function(e){
            e.preventDefault();
            var source = '';
            if (jQuery(this).prev().is('a')) {
                source = jQuery(this).prev().text();
            }
            else if (jQuery(this).prev().is('input')) {
                source = jQuery(this).prev().val();
            }
            var data = gtranslate(source);
            console.info(data);
            if (jQuery(this).prev().is('a')) {
                jQuery(this).next().remove();
                jQuery(this).after(
                    '<div class="inline" style="position: relative; top: -3px;">' +
                        '<input type="text" style="width: 300px;" class="form-control input-sm inline source" value="'+data.source+'"/>' +
                        '<a href="#" title="click to translate again" class="hidden-sm hidden-xs inline pad translator"><i class="fa fa-globe"></i></a>' +
                        '<input type="text" style="width: 300px;" class="form-control input-sm inline translation" value="'+data.translation+'"/>' +
                        '<button class="btn btn-default btn-sm do-translate">DO</button>' +
                        '<div>'
                );
            }
            else if (jQuery(this).prev().is('input')) {
                jQuery(this).next().val(data.translation);
            }
            //jQuery(this).text( titlecase(data.translation) );
            return false;
        });
        jQuery(document).on('click', '.do-translate', function(e){
            var translation = jQuery(this).prev().val();
            var id = jQuery(this).closest('.dd-item').data('id');
            var url = '{{route("cart.categories.save", ["XXX"])}}';
            url = url.replace("XXX", id);
            var data = {
                isAjax: true,
                category: {
                    title: translation,
                    slug: ''
                },
                seo: {
                    title: translation
                }
            };
            jQuery.post(url, data, function(response) {
                console.info(response);
            }, 'text');
        });
    });

</script>
@stop

@section('content')
<div class="box box-primary">
    <div class="box-body table-responsive">

        <div class="btn-group nestable-menu">
            <a type="button" class="btn btn-default" data-action="expand-all">Expand All</a>
            <a type="button" class="btn btn-default" data-action="collapse-all">Collapse All</a>
        </div>

        <div class=" dd nestable">
            <ol class="dd-list ">
                @foreach($categories as $category)
                @include('laracart::categories.partials.row', ['category'=>$category])
                @endforeach
            </ol>
        </div>

        <div class="clearfix"></div>

        <div class="btn-group nestable-menu">
            <a type="button" class="btn btn-default" data-action="expand-all">Expand All</a>
            <a type="button" class="btn btn-default" data-action="collapse-all">Collapse All</a>
        </div>

    </div>
</div>
@stop