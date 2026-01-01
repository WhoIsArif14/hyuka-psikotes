<?php

namespace Tests\Feature;

use App\Models\ActivationCode;
use App\Models\Test as TestModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivationCodesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_batch_and_update_name()
    {
        // buat user admin
        $admin = User::factory()->create(['role' => 'admin']);

        // buat test/module
        $test = TestModel::create(['title' => 'Feature Test Module']);

        // generate batch
        $response = $this->actingAs($admin)->post(route('admin.codes.store'), [
            'test_id' => $test->id,
            'quantity' => 3,
            'batch_name' => 'My Batch',
        ]);

        $response->assertRedirect(route('admin.codes.index'));

        $this->assertDatabaseHas('activation_codes', ['batch_name' => 'My Batch']);

        $code = ActivationCode::first();
        $this->assertNotNull($code->batch_code);

        // update batch name
        $update = $this->actingAs($admin)->patch(route('admin.codes.updateName', $code->id), [
            'batch_name' => 'New Name',
        ]);

        $update->assertRedirect(route('admin.codes.show', $code->id));

        $this->assertDatabaseHas('activation_codes', [
            'batch_code' => $code->batch_code,
            'batch_name' => 'New Name',
        ]);
    }
}
