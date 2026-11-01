<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Student;
use App\Services\DeadlineService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request, DeadlineService $deadlines)
    {
        $user = $request->user();

        $monday = $this->resolveWeek($request->input('minggu'), $deadlines);
        $weekDays = collect($deadlines->weekDays($monday));

        $reports = DailyReport::where('class_id', $user->class_id)
            ->whereDate('report_date', '>=', $weekDays->first()->toDateString())
            ->whereDate('report_date', '<=', $weekDays->last()->toDateString())
            ->with('assessments')
            ->get()
            ->keyBy(fn (DailyReport $report) => $report->report_date->toDateString());

        $students = Student::where('class_id', $user->class_id)
            ->orderBy('sort_order')
            ->get();

        $grid = [];
        foreach ($reports as $report) {
            foreach ($report->assessments as $assessment) {
                $grid[$assessment->student_id][$report->report_date->toDateString()] = $assessment;
            }
        }

        return view('rekap.index', [
            'weekDays' => $weekDays,
            'reports' => $reports,
            'students' => $students,
            'grid' => $grid,
            'monday' => $monday,
            'canGoNext' => $monday->startOfWeek()->lt($deadlines->now()->startOfWeek()),
        ]);
    }

    private function resolveWeek(?string $week, DeadlineService $deadlines): Carbon
    {
        if (! $week) {
            return $deadlines->now();
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $week, config('app.timezone'));
        } catch (\Throwable) {
            abort(404);
        }

        if (! $parsed || $parsed->format('Y-m-d') !== $week) {
            abort(404);
        }

        return $parsed->startOfDay();
    }
}
