<?php

return [
    'custom' => [
        'email' => [
            'required' => 'Trường email là bắt buộc.',
            'email' => 'Vui lòng nhập một địa chỉ email hợp lệ.',
        ],
        'password' => [
            'required' => 'Trường mật khẩu là bắt buộc.',
            'min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ],
        'current_password'=> [
            'required' => 'Trường mật này là bắt buộc.',            
        ],
        'new_password' => [
            'required'=> 'Trường mật này là bắt buộc.',
            'min' => 'Mật này phải có nhất :min ký tự.',
            ],
    ],
];
