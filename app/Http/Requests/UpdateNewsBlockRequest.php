<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsBlockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:text,image,text_image_right,text_image_left',
            'text_content' => 'required_unless:type,image|string|nullable',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'integer',
        ];
    }
}
