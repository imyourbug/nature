<?php

namespace App\Http\Requests;

use App\Constant\GlobalConstant;
use App\Exceptions\InputInvalidException;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'price' => 'required|numeric|min:0.01',
            'specs' => 'nullable|array',
            'status' => 'nullable|in:' . implode(',', GlobalConstant::getProductStatuses()),
            'vendor_id' => 'nullable|exists:vendors,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc',
            'brand.required' => 'Thương hiệu là bắt buộc',
            'price.required' => 'Giá là bắt buộc',
            'price.numeric' => 'Giá phải là số',
            'price.min' => 'Giá phải lớn hơn 0',
            'status.in' => 'Trạng thái không hợp lệ',
            'vendor_id.exists' => 'Nhà bán không tồn tại',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new InputInvalidException($validator->errors()->toArray());
    }
}
