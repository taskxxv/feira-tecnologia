<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => 'required|integer|exists:disciplinas,id',
            'title' => 'required|string|max:200',
            'content' => 'required|string|max:20000',
        ];
    }
}
