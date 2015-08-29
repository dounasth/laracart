<div class="row">
    @var $filters = \Bonweb\Laracart\Filter::all()
    @foreach ($filters as $filter)
    <div class="col-lg-12 col-xs-12">
        <div class="box box-primary">
            <h3>{{$filter->title}}</h3>
            <div class="box-body">
                <ul class="list-group">
                    @foreach ($filter->values($product_ids) as $fvid => $fval)
                    <li class="list-group-item">
                        <a href="{{ route('site.cart.category.view', $category->slug) }}?{{ \Bonweb\Laracart\Filter::makeLink($filter->slug, $fval) }}">{{$fval}}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>