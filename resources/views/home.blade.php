@extends('layouts.app')

@section('content')
<div ng-controller="PostCtrl">
        {{--<div class="row">--}}
            {{--<div class="col-md-12 text-center" ng-show="loading || scroll.busy">--}}
                {{--<i class="fa fa-3x fa-circle-o-notch fa-spin"></i>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="alert alert-danger" ng-show="loadingError" ng-cloak>
            <span ng-bind="errorText"></span>
        </div>

        <div class="col-md-12">
            <form role="search">
                <div class="form-group">
                    <input type="text" class="input-lg form-control" placeholder="Search post" ng-model="srcword" ng-change="filterPosts()">
                </div>
            </form>
        </div>

        <div class="col-md-12" ng-cloak infinite-scroll="loadMore()"  infinite-scroll-distance="1" infinite-scroll-disabled='scroll.busy'>
            <ul class="media-list" >
                <li ng-repeat="post in filteredPosts  = ( posts | filter: srcword ) track by $index">
                    <div class="panel">
                        <div class="panel-body">
                             <div class="media">
                                <div class="media-left" ng-show="post.attachments.data[0].media.image.src">
                                    <a href="@{{ post.full_picture  }}" target="_blank"  ng-show="post.picture">
                                        <img src="@{{ post.picture }}" class="media-object" alt="">
                                    </a>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading">@{{ post.caption }}<small><i>@{{ post.created_time }}</i></small></h4>
                                    <p ng-show="post.message">@{{ post.message }}</p>
                                    <p ng-show="post.description">@{{ post.description }}</p>
                                    <a href="@{{ post.link }}" class="link">@{{ post.link }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="row text-center" ng-show="(scroll.busy || loading) && !loadingError">
                <i class="fa fa-spin fa-circle-o-notch"></i> Loading feeds...
            </div>
        </div>

</div>
@endsection
