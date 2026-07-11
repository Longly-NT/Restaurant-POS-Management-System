<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;

class TableController extends Controller
{
    public function index()
    {
        $tables = DiningTable::with(['activeOrder'])->orderBy('name')->get();

        return view('staff.tables.index', compact('tables'));
    }
}
