<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Các rule cập nhật hồ sơ Customer.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'regex:/^0\d{9}$/',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:1024',
            ],
        ];
    }

    /**
     * Thông báo riêng cho hồ sơ.
     */
    public function messages(): array
    {
        return [
            'phone.regex' =>
                'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',

            'avatar.image' =>
                'Ảnh đại diện phải là tệp hình ảnh.',

            'avatar.mimes' =>
                'Ảnh đại diện chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',

            'avatar.max' =>
                'Ảnh đại diện không được vượt quá 1 MB.',
        ];
    }

    /**
     * Tên hiển thị của các trường.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'avatar' => 'Ảnh đại diện',
        ];
    }
}