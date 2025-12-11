<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchMovieRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            'title' => 'required|string|max:255',
            'type' => 'sometimes|string|in:movie,series,episode',
            'year' => 'sometimes|integer|min:1800|max:' . date('Y'),
            'page' => 'sometimes|integer|min:1',
        ];
    }
    
    public function messages(): array {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'type.string' => 'The type must be a string.',
            'type.in' => 'The type must be one of the following: movie, series, episode.',
            'year.integer' => 'The year must be an integer.',
            'year.min' => 'The year must be at least 1800.',
            'year.max' => 'The year may not be greater than the current year.',
            'page.integer' => 'The page must be an integer.',
            'page.min' => 'The page must be at least 1.',
        ];
    }

}