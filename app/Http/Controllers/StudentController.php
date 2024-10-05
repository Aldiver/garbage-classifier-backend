<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function scanRFID($rfid)
    {
        $student = Student::where('rfid', $rfid)->first();

        if ($student) {
            return response()->json(['message' => 'OK', 'student' => $student], 200);
        } else {
            return response()->json(['message' => 'Student not found'], 404);
        }
    }

    // Check student's current points
    public function checkPoints($rfid)
    {
        $student = Student::where('rfid', $rfid)->first();

        if ($student) {
            return response()->json(['points' => $student->current_points], 200);
        } else {
            return response()->json(['message' => 'Student not found'], 404);
        }
    }

    // Get leaderboard
    public function leaderboard($rfid)
    {
        // Find the current student by RFID
        $student = Student::where('rfid', $rfid)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $currentPoints = $student->current_points;

        // Get the top 10 students based on points, along with their rank
        $leaderboard = Student::orderBy('current_points', 'desc')
            ->take(10)
            ->get()
            ->map(function ($student, $index) {
                return [
                    'rank' => $index + 1,
                    'alias' => $student->alias,
                    'current_points' => $student->current_points
                ];
            });

        // Find the current student's rank
        $rank = Student::where('current_points', '>', $currentPoints)->count() + 1;

        // Get 3 students with higher points than the current student
        $higherRankedStudents = Student::where('current_points', '>', $currentPoints)
            ->orderBy('current_points', 'asc')
            ->take(3)
            ->get()
            ->toArray(); // Convert to array

        // Get 3 students with lower points than the current student
        $lowerRankedStudents = Student::where('current_points', '<', $currentPoints)
            ->orderBy('current_points', 'desc')
            ->take(3)
            ->get()
            ->toArray(); // Convert to array

        // Add current student to the list
        $currentStudent = [['alias' => $student->alias, 'current_points' => $currentPoints]];

        // Merge higher ranked students, the current student, and lower ranked students
        $surroundingStudents = array_merge($higherRankedStudents, $currentStudent, $lowerRankedStudents);

        // Convert to a collection to apply rank
        $surroundingStudents = collect($surroundingStudents)
            ->sortByDesc('current_points') // Sort by points in descending order
            ->map(function ($student) use ($currentPoints) {
                // Calculate the rank for each student
                $rank = Student::where('current_points', '>', $student['current_points'])->count() + 1;
                return [
                    'rank' => $rank,
                    'alias' => $student['alias'],
                    'current_points' => $student['current_points']
                ];
            });

        return response()->json([
            'current_student' => $currentStudent,
            'leaderboard' => $leaderboard,
            'student_rank' => $surroundingStudents
        ], 200);
    }


    // Add or subtract points from a student
    public function updatePoints(Request $request, $rfid)
    {
        $student = Student::where('rfid', $rfid)->first();

        if ($student) {
            $points = $request->input('points');
            $student->current_points += $points;
            $student->save();

            return response()->json(['message' => 'Points updated successfully', 'current_points' => $student->current_points], 200);
        } else {
            return response()->json(['message' => 'Student not found'], 404);
        }
    }
}
