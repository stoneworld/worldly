<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $producer = new andZone\Kafka\MessageProducer(new andZone\Kafka\Conf(1), ['server' => '127.0.0.1', 'log_level' =>LOG_DEBUG]);
        dd($producer);
        return view('home');
    }
}
