<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Test as TestModel;
use App\Models\User;

class ModuleReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function module_report_renders_for_module_with_no_results()
    {
        $this->seed();

        // Create a test module for the report
        $category = \App\Models\TestCategory::create(['name' => 'Contoh Kategori']);
        $jenjang = \App\Models\Jenjang::create(['name' => 'Semua']);
        $client = \App\Models\Client::create(['name' => 'Test Client']);

        $test = TestModel::create([
            'client_id' => $client->id,
            'test_category_id' => $category->id,
            'jenjang_id' => $jenjang->id,
            'title' => 'Contoh Modul Laporan',
            'description' => 'Modul berisi beberapa alat tes contoh untuk laporan',
            'duration_minutes' => 30,
        ]);
        $user = User::factory()->create();
        $this->actingAs($user);

        $resp = $this->get(route('tests.module.report', $test->id));
        $resp->assertStatus(200);
        $resp->assertSee('Laporan Modul');
    }
}
