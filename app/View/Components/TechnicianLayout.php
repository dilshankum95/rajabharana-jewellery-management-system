<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class TechnicianLayout extends Component
{
    public function render(): View
    {
        return view('layouts.technician');
    }
}
