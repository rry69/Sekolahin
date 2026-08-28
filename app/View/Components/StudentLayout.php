<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StudentLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): \Illuminate\View\View
    {
        return view('layouts.student');
    }
}
