<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterpretationRule;
use App\Models\Test;
use Illuminate\Http\Request;

class InterpretationRuleController extends Controller
{
    public function index(Test $test)
    {
        $rules = $test->interpretationRules;
        return view('admin.rules.index', compact('test', 'rules'));
    }

    public function create(Test $test)
    {
        return view('admin.rules.create', compact('test'));
    }

    public function store(Request $request, Test $test)
    {
        $request->validate([
            'min_score' => 'required|integer',
            'max_score' => 'required|integer|gte:min_score',
            'interpretation_text' => 'required|string',
        ]);

        $test->interpretationRules()->create($request->all());

        return redirect()->route('admin.tests.rules.index', $test)->with('success', 'Aturan interpretasi berhasil ditambahkan.');
    }

    public function edit(Test $test, InterpretationRule $rule)
    {
        return view('admin.rules.edit', compact('test', 'rule'));
    }

    public function update(Request $request, Test $test, InterpretationRule $rule)
    {
        $request->validate([
            'min_score' => 'required|integer',
            'max_score' => 'required|integer|gte:min_score',
            'interpretation_text' => 'required|string',
        ]);

        $rule->update($request->all());

        return redirect()->route('admin.tests.rules.index', $test)->with('success', 'Aturan interpretasi berhasil diperbarui.');
    }

    public function destroy(Test $test, InterpretationRule $rule)
    {
        $rule->delete();
        return redirect()->route('admin.tests.rules.index', $test)->with('success', 'Aturan interpretasi berhasil dihapus.');
    }
}