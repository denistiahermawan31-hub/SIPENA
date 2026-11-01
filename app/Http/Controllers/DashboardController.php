<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Services\DeadlineService;

class DashboardController extends Controller
{
    public function index(DeadlineService $deadlines)
    {
        $user = auth()->user();
        $today = $deadlines->todaysEditableDate();
        $deadline = $deadlines->deadline();

        $weekDays = collect($deadlines->weekDays());

        $reports = DailyReport::where('class_id', $user->class_id)
            ->whereIn('report_date', $weekDays->pluck('toDateString'))
            ->get()
            ->keyBy(fn (DailyReport $report) => $report->report_date->toDateString());

        $todayReport = $today ? $reports->get($today->toDateString()) : null;

        return view('dashboard', [
            'weekDays' => $weekDays,
            'reportsByDate' => $reports,
            'today' => $today,
            'todayReport' => $todayReport,
            'deadline' => $deadline,
            'weekLocked' => $deadlines->isWeekLocked(),
            'deadlineInMinutes' => (int) abs(now()->diffInMinutes($deadline)),
        ]);
    }
}
