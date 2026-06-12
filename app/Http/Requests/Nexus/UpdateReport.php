<?php

namespace App\Http\Requests\Nexus;

use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReport extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(Report::STATUSES))],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
