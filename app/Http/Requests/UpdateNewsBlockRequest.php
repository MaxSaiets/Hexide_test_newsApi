<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\NewsBlockType;

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
            'type' => ['required', Rule::in(NewsBlockType::values())],
            'text_content' => 'required_unless:type,image|string|nullable',
            'image_path' => [Rule::requiredIf(fn () => in_array($this->input('type'), [
                NewsBlockType::Image->value,
                NewsBlockType::TextImageLeft->value,
                NewsBlockType::TextImageRight->value,
            ])),'nullable','image','mimes:jpeg,png,jpg,gif','max:2048'],
            'position' => 'integer',
        ];
    }
}
