<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsBlockRequest extends FormRequest
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
            'news_id' => 'required|exists:news,id',
            'content.type' => 'required|in:text,image,text_image_right,text_image_left',
            'content.text' => 'required_unless:content.type,image|string',
            'content.image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'integer',
        ];
    }
}
