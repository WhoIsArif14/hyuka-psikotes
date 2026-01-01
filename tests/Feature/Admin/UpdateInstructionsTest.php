<?php

namespace Tests\Feature\Admin;

use App\Models\AlatTes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateInstructionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_update_instructions_for_alat_tes()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $alat = AlatTes::factory()->create();

        $response = $this->actingAs($admin)
            ->put(route('admin.alat-tes.questions.update_instructions', $alat->id), [
                'instructions' => 'Instruksi baru',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('alat_tes', [
            'id' => $alat->id,
            'instructions' => 'Instruksi baru',
        ]);
    }
}
