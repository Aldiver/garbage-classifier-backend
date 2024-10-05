<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase; // Use this trait to run database migrations before each test

    /**
     * Test the leaderboard API endpoint.
     *
     * @return void
     */
    public function test_leaderboard()
    {
        // Arrange: Create test data
        $student = Student::factory()->create(['rfid' => 'test123']);
        $students = Student::factory()->count(10)->create();

        // Act: Make a request to the leaderboard endpoint
        $response = $this->getJson('/api/leaderboard/' . $student->rfid);

        // Assert: Check the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'leaderboard' => [
                    '*' => ['rank', 'alias', 'current_points'],
                ],
                'student_rank' => [
                    '*' => ['rank', 'alias', 'current_points'],
                ],
            ])
            ->assertJsonFragment(['alias' => $student->alias]);
    }

    /**
     * Test the case where a student is not found.
     *
     * @return void
     */
    public function test_student_not_found()
    {
        // Act: Make a request to the leaderboard endpoint with an invalid RFID
        $response = $this->getJson('/api/leaderboard/invalidrfid');

        // Assert: Check the response
        $response->assertStatus(404)
            ->assertJson(['message' => 'Student not found']);
    }
}
