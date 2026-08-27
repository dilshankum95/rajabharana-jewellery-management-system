<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Permission;
use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterReportRequest;
use App\Models\ReportExport;
use App\Services\ReportEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportEngine $reportEngine
    ) {}

    public function index(Request $request): View
    {
        $role = $request->user()->role;
        $reportTypes = ReportType::forRole($role);

        $recentExports = ReportExport::with('generator')
            ->when(! $request->user()->isAdmin(), fn ($q) => $q->where('generated_by', $request->user()->id))
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact('reportTypes', 'recentExports'));
    }

    public function show(ReportType $type, FilterReportRequest $request): View
    {
        $this->authorizeReportType($type, $request);

        $range = $type->usesDateRange() ? $request->dateRange() : ['from' => null, 'to' => null];
        $report = $this->reportEngine->generate($type, $range['from'], $range['to']);

        return view('admin.reports.show', [
            'report' => $report,
            'type' => $type,
            'filters' => $request->only(['date_from', 'date_to']),
        ]);
    }

    public function print(ReportType $type, FilterReportRequest $request): View
    {
        $this->authorizeReportType($type, $request);

        $range = $type->usesDateRange() ? $request->dateRange() : ['from' => null, 'to' => null];
        $report = $this->reportEngine->generate($type, $range['from'], $range['to']);

        return view('admin.reports.print', [
            'report' => $report,
            'type' => $type,
            'generatedBy' => $request->user()->name,
        ]);
    }

    public function exportCsv(ReportType $type, FilterReportRequest $request): StreamedResponse
    {
        abort_unless(
            $request->user()->hasPermission(Permission::ReportsExport->value) || $request->user()->isAdmin(),
            403
        );

        $this->authorizeReportType($type, $request);

        $range = $type->usesDateRange() ? $request->dateRange() : ['from' => null, 'to' => null];
        $report = $this->reportEngine->generate($type, $range['from'], $range['to']);

        $filename = sprintf('%s_%s.csv', $type->value, now()->format('Ymd_His'));

        Storage::disk('exports')->makeDirectory('');

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $report['columns']);
        foreach ($report['rows'] as $row) {
            fputcsv($handle, $row);
        }

        foreach ($report['sections'] ?? [] as $section) {
            fputcsv($handle, []);
            fputcsv($handle, [$section['title']]);
            fputcsv($handle, $section['columns']);
            foreach ($section['rows'] as $row) {
                fputcsv($handle, $row);
            }
        }
        rewind($handle);
        Storage::disk('exports')->put($filename, stream_get_contents($handle));
        fclose($handle);

        ReportExport::create([
            'report_type' => $type,
            'date_from' => $range['from'],
            'date_to' => $range['to'],
            'generated_by' => $request->user()->id,
            'file_path' => $filename,
            'format' => 'csv',
            'parameters' => $request->only(['date_from', 'date_to']),
        ]);

        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $report['columns']);
            foreach ($report['rows'] as $row) {
                fputcsv($out, $row);
            }

            foreach ($report['sections'] ?? [] as $section) {
                fputcsv($out, []);
                fputcsv($out, [$section['title']]);
                fputcsv($out, $section['columns']);
                foreach ($section['rows'] as $row) {
                    fputcsv($out, $row);
                }
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function authorizeReportType(ReportType $type, Request $request): void
    {
        $user = $request->user();

        if (! $user || ! $user->role instanceof \App\Enums\UserRole) {
            abort(403);
        }

        if (! $type->isAllowedFor($user->role)) {
            abort(403, 'You do not have access to this report.');
        }
    }
}
