<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $userCount = User::where('role', 'user')->count();
        $testCount = Test::count();
        $resultCount = TestResult::count();

        $recentResults = TestResult::with(['user', 'test'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'userCount',
            'testCount',
            'resultCount',
            'recentResults'
        ));
    }
}