<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class KanbanController extends Controller
{
    /**
     * Exibe o quadro Kanban de cobranças.
     */
    public function index(): View
    {
        return view('kanban.index');
    }
}