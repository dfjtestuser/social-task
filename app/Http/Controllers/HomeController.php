<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use GuzzleHttp\Client;
use App\Jobs\UpdateFacebookFeed;
use Carbon\carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $process = new Process('php artisan facebook:fetchdata '.\Auth::user()->id.' > /dev/null 2>&1 &', base_path());
        $process->start();
        return view('home');
    }
    
    
    
    
}
