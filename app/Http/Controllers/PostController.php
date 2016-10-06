<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Post;
use Auth;

class PostController extends Controller
{

    protected $take = 30;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($offset = 0, $srcword=null)
    {
        if($srcword) {
            return Post::whereUserId(Auth::id())
                ->where(function($q) use($srcword) {
                    $q->orWhere('caption','LIKE',"%{$srcword}%");
                    $q->orWhere('name','LIKE',"%{$srcword}%");
                    $q->orWhere('message','LIKE',"%{$srcword}%");
                    $q->orWhere('description','LIKE',"%{$srcword}%");
                })->take($this->take)->offset($offset)->get();
        }
        return Post::whereUserId(Auth::id())->orderBy('created_time','desc')->take($this->take)->offset($offset)->get();
    }

}