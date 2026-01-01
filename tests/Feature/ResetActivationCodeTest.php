<?php

namespace Tests\Feature;

use App\Models\ActivationCode;
use App\Models\Test as TestModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetActivationCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reset_a_used_activation_code()
    {
        // admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // normal user who "used" the code
        $user = User::factory()->create();

        // create a test/module
        $test = TestModel::create(['title' => 'Module X']);

        // create a single activation code and mark as used
        $code = ActivationCode::create([
            'batch_code' => 'BATCH-TEST',
            'batch_name' => 'Test Batch',
            'test_id' => $test->id,
            'code' => 'ABCD-EFGH',
            'status' => 'Used',
            'user_id' => $user->id,
            'used_at' => now(),
        ]);

        $this->assertDatabaseHas('activation_codes', [
            'id' => $code->id,
            'status' => 'Used',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.codes.reset', $code->id));
        $response->assertRedirect();

        $this->assertDatabaseHas('activation_codes', [
            'id' => $code->id,
            'status' => 'Pending',
            'user_id' => null,
        ]);
    }
}
