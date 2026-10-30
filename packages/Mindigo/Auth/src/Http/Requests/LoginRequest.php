<?php

namespace Mindigo\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $learnSphereId = $this->input('learn_sphere_id');

        if (is_string($learnSphereId)) {
            $this->merge(['learn_sphere_id' => strtoupper(trim($learnSphereId))]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'learn_sphere_id' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'learn_sphere_id.required' => __('Mindigo-auth::app.validation.learn_sphere_id.required'),
            'password.required' => __('Mindigo-auth::app.validation.password.required'),
            'password.min' => __('Mindigo-auth::app.validation.password.min'),
        ];
    }
}
