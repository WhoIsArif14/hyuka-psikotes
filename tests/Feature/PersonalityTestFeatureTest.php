<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\PersonalityTest;

class PersonalityTestFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_personality_list_and_allows_submission()
    {
        $this->seed(); // seeds including PersonalityTestSeeder

        $test = PersonalityTest::first();
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $resp = $this->get(route('personality.show', $test->id));
        $resp->assertStatus(200);
        $resp->assertSee($test->title);

        $questions = $test->questions;
        $postData = [];
        foreach ($questions as $q) {
            $postData['q_' . $q->id] = 4;
        }

        $submit = $this->post(route('personality.submit', $test->id), $postData);
        $submit->assertRedirect();

        $this->assertDatabaseHas('personality_results', [
            'personality_test_id' => $test->id,
            'score' => count($questions) * 4,
        ]);
    }
}
