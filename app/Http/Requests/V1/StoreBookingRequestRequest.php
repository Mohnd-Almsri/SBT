<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public endpoint
    }

    public function rules(): array
    {
        return [
            'course_run_id' => ['required', 'integer', 'min:1', 'exists:course_runs,id'],

            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],

            'phone' => ['nullable', 'string', 'max:30'],

            'note' => ['nullable', 'string', 'max:2000'],

            // meta: نخليها Array (وتنخزن JSON) — إذا بدك string JSON خبرني
            'meta' => ['nullable', 'array'],
            // مثال لو بدك قيود جوّا meta:
            // 'meta.source' => ['nullable', 'string', 'max:50'],
            // 'meta.utm'    => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // تنظيف بسيط للمدخلات الشائعة
        $this->merge([
            'course_run_id' => $this->course_run_id !== null ? (int) $this->course_run_id : null,
            'first_name' => is_string($this->first_name) ? trim($this->first_name) : $this->first_name,
            'last_name'  => is_string($this->last_name) ? trim($this->last_name) : $this->last_name,
            'phone'      => is_string($this->phone) ? trim($this->phone) : $this->phone,
        ]);
    }
}
