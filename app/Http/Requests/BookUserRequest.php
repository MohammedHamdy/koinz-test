<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookUserRequest extends ApiRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request) {
        $rules = [
            'book_id' => 'required|exists:books,id',
            'user_id' => 'required|exists:users,id',
            'start_page' => 'required|integer|min:1|before:end_page',
            'end_page' => 'required|integer|min:1||after:start_page',
        ];
        return $rules;
    }
}
