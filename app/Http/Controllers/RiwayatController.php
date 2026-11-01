<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Services\DeadlineService;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request, DeadlineService $deadlines)
    {
        $user = $request->user();

        $reports = DailyReport::where('class_id', $user->class_id)
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->paginate(15);

        return view('riwayat.index', [
            'reports' => $reports,
            'todayEditable' => $deadlines->todaysEditableDate(),
        ]);
    }
}
