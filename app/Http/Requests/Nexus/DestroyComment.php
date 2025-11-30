<?php

namespace App\Http\Requests\Nexus;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class DestroyComment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // get comment from parameter via route model binding
        $comment = $this->route('comment');
        return $this->user()->can('delete', $comment);
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
