<?php

namespace App\Http\Controllers;

use App\Services\Inicis\INISServices;
use Illuminate\Http\Request;

class InicisController extends Controller
{
    private $INISServices;

    public function __construct(Request $request)
    {
        $this->INISServices = (new INISServices());
    }

    public function INISInit(Request $request)
    {
        return $this->INISServices->INISInitService($request);
    }

    public function INISResult(Request $request)
    {
        return $this->INISServices->INISResultService($request);
    }

    public function INISClose(Request $request)
    {
        return view('common.inicis.close', ['resultMap' => (object)$request->all()]);
    }
}
