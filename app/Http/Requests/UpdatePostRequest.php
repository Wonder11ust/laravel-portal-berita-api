<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
        $id = $this->route('id') ?? $this->route('post'); 
        return [
            'title'       => 'sometimes|string|max:255|unique:posts,title,' . $id,
            'content'     => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
             'category_id' => [
             'sometimes',
                Rule::exists('categories', 'id')->whereNull('deleted_at')
                ],
            'cover'       => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            // 'user_id'       => 'sometimes|exists:users,id',
        ];
    }

    public function passedValidation()
    {
        if ($this->has('user_id')) {
            abort(422, 'User ID tidak bisa diubah');
        }
    }
}
