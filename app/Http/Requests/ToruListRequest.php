<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ToruListRequest extends FormRequest
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
            'price_from' => ['numeric'],
            'price_to' => ['numeric'],
            'date_from' => ['date'],
            'date_to' => ['date'],
            'sort_by' => [Rule::in(['price','starting_date'])],
            'sort_order' => [Rule::in(['asc','desc'])]
        ];
    }
    public function messages() : array 
    {
        return [
            'sort_by' => "The 'sort_by' parameter accepets only 'price' or 'starting_date' values.",
            'sort_order' => "The 'sort_order' parameter accepets only 'asc' or 'desc' values.",
        ];
    }
}
