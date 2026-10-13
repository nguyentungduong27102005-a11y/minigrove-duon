<?php

return [

    'required' => 'Trường :attribute là bắt buộc.',

    'email' => ':attribute phải là địa chỉ email hợp lệ.',

    'string' => ':attribute phải là chuỗi ký tự.',

    'numeric' => ':attribute phải là số.',

    'integer' => ':attribute phải là số nguyên.',

    'boolean' => ':attribute phải có giá trị đúng hoặc sai.',

    'password' => [
        'letters' => ':attribute phải chứa ít nhất một chữ cái.',
        'mixed' => ':attribute phải chứa ít nhất một chữ hoa và một chữ thường.',
        'numbers' => ':attribute phải chứa ít nhất một chữ số.',
        'symbols' => ':attribute phải chứa ít nhất một ký tự đặc biệt.',
        'uncompromised' => ':attribute đã xuất hiện trong một vụ rò rỉ dữ liệu. Vui lòng chọn mật khẩu khác.',
    ],

    'confirmed' => 'Xác nhận :attribute không khớp.',

    'current_password' => 'Mật khẩu hiện tại không chính xác.',

    'unique' => ':attribute đã tồn tại.',

    'exists' => ':attribute đã chọn không hợp lệ.',

    'same' => ':attribute và :other phải giống nhau.',

    'different' => ':attribute và :other phải khác nhau.',

    'regex' => 'Định dạng :attribute không hợp lệ.',

    'in' => ':attribute đã chọn không hợp lệ.',

    'min' => [

        'numeric' => ':attribute phải có giá trị tối thiểu :min.',

        'file' => ':attribute phải có dung lượng tối thiểu :min KB.',

        'string' => ':attribute phải có ít nhất :min ký tự.',

        'array' => ':attribute phải có ít nhất :min phần tử.',

    ],

    'max' => [

        'numeric' => ':attribute không được lớn hơn :max.',

        'file' => ':attribute không được lớn hơn :max KB.',

        'string' => ':attribute không được vượt quá :max ký tự.',

        'array' => ':attribute không được có nhiều hơn :max phần tử.',

    ],

    'between' => [

        'numeric' => ':attribute phải nằm trong khoảng :min đến :max.',

        'file' => ':attribute phải có dung lượng từ :min đến :max KB.',

        'string' => ':attribute phải có từ :min đến :max ký tự.',

        'array' => ':attribute phải có từ :min đến :max phần tử.',

    ],
'custom' => [

    'password_confirmation' => [

        'same' => 'Xác nhận mật khẩu không khớp.',

    ],

],
    'attributes' => [

        'name' => 'Họ và tên',

        'email' => 'Email',

        'phone' => 'Số điện thoại',

        'password' => 'Mật khẩu',

        'password_confirmation' => 'Xác nhận mật khẩu',

        'current_password' => 'Mật khẩu hiện tại',

        'receiver_name' => 'Tên người nhận',

        'receiver_phone' => 'Số điện thoại',

        'province' => 'Tỉnh / Thành phố',

        'district' => 'Quận / Huyện',

        'ward' => 'Phường / Xã',

        'address_detail' => 'Địa chỉ chi tiết',

        'label' => 'Nhãn địa chỉ',

    ],

];