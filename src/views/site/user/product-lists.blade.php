@extends('laradmin::site.site-layout')

@section('page-title')
    Οι Λίστες μου
@stop

@section('page-subtitle')
@stop

@section('breadcrumb')
    @parent
    <li class="active"><a href="{{ route('site.user.account') }}"> Ο Λογαριασμός μου </a></li>
    <li class="active">Οι Λίστες μου</li>
@stop

@section('page-menu')
@stop

@section('meta')
@stop

@section('styles')
@stop

@section('scripts')
@stop

@section('content')


    <div class="container main-container headerOffset">

        <div class="row">
            <div class="breadcrumbDiv col-lg-12">
                <ul class="breadcrumb">
                    @include('laradmin::site.breadcrumb')
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h1 class="section-title-inner"><span><i class="glyphicon glyphicon-heart"></i> Λίστες με αγαπημένα προϊόντα </span></h1>
                <div class="row userInfo">
                    <div class="col-xs-12 col-sm-12">
                        <h2 class="block-title-2"><span>Welcome to your account. Here you can manage all of your personal information and orders.</span></h2>
                        <ul class="myAccountList row">
                            @foreach ( $lists as $list )
                            <li class="col-lg-2 col-md-2 col-sm-3 col-xs-4  text-center ">
                                <div class="thumbnail equalheight" style="height: 104px;"><a title="{{ $list->name }}" href="{{ route('site.user.lists', [$list->id]) }}"><i class="fa fa-heart"></i> {{ $list->name }} </a></div>
                            </li>
                            @endforeach
                            <li class="col-lg-2 col-md-2 col-sm-3 col-xs-4  text-center ">
                                <div class="thumbnail equalheight" style="height: 104px;"><a title="My wishlists" href="wishlist.html"><i class="fa fa-plus"></i> Νέα Λίστα </a></div>
                            </li>
                        </ul>
                        <div class="clear clearfix"><hr/></div>
                    </div>
                </div>

            </div>
        </div>

        @if ($selected)
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h1 class="section-title-inner"><span><i class="glyphicon glyphicon-heart"></i> {{ $selected->name }} </span></h1>
                <div class="row userInfo">
                    <div class="col-lg-12">
                        <h2 class="block-title-2"> Update your wishlist if it has changed. </h2>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                        @foreach ($selected->products as $it)
                            <div class="item col-sm-3 col-lg-3 col-md-3 col-xs-6">
                                @include('laracart::site.product-mini', ['product'=>$it->product])
                                <a class="btn btn-danger btn-block">remove</a>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-lg-12 clearfix">
                        <ul class="pager">
                            <li class="next pull-left"><a href="{{ route('site.user.account') }}"> ← Back to My Account</a></li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="col-lg-3 col-md-3 col-sm-5"></div>
        </div>
        @endif

    </div>


@stop