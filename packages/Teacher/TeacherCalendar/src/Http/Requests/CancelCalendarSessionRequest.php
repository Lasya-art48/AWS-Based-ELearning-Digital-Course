<?php

namespace Mindigo\TeacherCalendar\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mindigo\TeacherClassroom\Models\ClassroomSchedule;

class CancelCalendarSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $schedule = $this->route('schedule');

        return $this->user()?->role === 'teacher'
            && $schedule?->classroom?->teacher_id === (int) $this->user()->getAuthIdentifier()
            && ! in_array($schedule?->status, [ClassroomSchedule::STATUS_CANCELLED, ClassroomSchedule::STATUS_RESCHEDULED, ClassroomSchedule::STATUS_COMPLETED], true);
    }

    public function rules(): array
    {
        return ['cancel_reason' => ['required', 'string', 'min:10', 'max:1000']];
    }
}
