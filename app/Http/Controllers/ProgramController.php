<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/** Add / edit / delete one-off programmes shown on the Timetable page. */
class ProgramController extends Controller
{
    public function store(Request $request)
    {
        $program = $request->user()->programs()->create($this->validated($request));

        return response()->json(['success' => true, 'program' => $program->toRaw()]);
    }

    public function update(Request $request, Program $program)
    {
        abort_unless($program->user_id === $request->user()->id, 403);
        $program->update($this->validated($request));

        return response()->json(['success' => true, 'program' => $program->fresh()->toRaw()]);
    }

    public function destroy(Request $request, Program $program)
    {
        abort_unless($program->user_id === $request->user()->id, 403);
        $program->delete();

        return response()->json(['success' => true]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:120',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'all_day' => 'required|boolean',
            'start_time' => 'nullable|required_if:all_day,false,0|date_format:H:i',
            'end_time' => 'nullable|required_if:all_day,false,0|date_format:H:i|after:start_time',
            'place' => 'nullable|string|max:120',
            'color' => 'nullable|in:' . implode(',', Program::COLORS),
        ], [
            'end_date.after_or_equal' => __('The end date cannot be before the start date.'),
            'end_time.after' => __('End time must be after the start time.'),
        ]);

        $days = Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date'])) + 1;
        if ($days > Program::MAX_DAYS) {
            throw ValidationException::withMessages(['end_date' => __('A programme can be at most :days days long.', ['days' => Program::MAX_DAYS])]);
        }

        if ($data['all_day']) {
            $data['start_time'] = $data['end_time'] = null;
        }
        $data['color'] = $data['color'] ?? 'amber';
        $data['place'] = isset($data['place']) && trim($data['place']) !== '' ? trim($data['place']) : null;

        return $data;
    }
}
