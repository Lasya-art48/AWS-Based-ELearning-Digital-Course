<?php

namespace Mindigo\StudentExam\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Mindigo\ExamManagement\Models\ExamProctorEvent;
use Mindigo\ExamManagement\Models\ExamSessionAttempt;

class SessionSecurityEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $attempt = $this->route('attempt');

        return $attempt instanceof ExamSessionAttempt && (int) $attempt->user_id === (int) $this->user()?->getAuthIdentifier();
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:'.implode(',', ExamProctorEvent::CLIENT_TYPES)],
            'occurred_at' => ['nullable', 'date'],
            'session_key' => ['sometimes', 'nullable', 'string', 'max:64'],
            'device_key' => ['sometimes', 'nullable', 'string', 'max:255'],
            'metadata' => ['sometimes', 'array'],
        ];
    }
}
