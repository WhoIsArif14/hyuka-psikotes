<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Services\ModuleReportService;

class ReportController extends Controller
{
    protected $service;

    public function __construct(ModuleReportService $service)
    {
        $this->service = $service;
    }

    public function showModuleReport(Test $test)
    {
        $report = $this->service->generate($test, auth()->user());
        return view('tests.module_report', compact('report'));
    }
}
