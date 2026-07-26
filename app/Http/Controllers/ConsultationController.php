<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ConsultationController extends Controller
{
    public function show(): View
    {
        return view('pages.consultor-ia');
    }
}
