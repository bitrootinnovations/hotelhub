<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use App\Models\ClientMaster;

class PrinterStatusController extends Controller
{
    public function index()
    {
        $printers = Printer::with('client')
            ->orderByRaw("FIELD(status, 'online', 'offline')")
            ->orderBy('last_seen_at', 'desc')
            ->get();

        return view('printers.index', compact('printers'));
    }
}
