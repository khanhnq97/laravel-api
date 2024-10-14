<?php

return [
    'custom' => [
        'email' => [
            'required' => 'The email field is required aa.',
            'email' => 'Please enter a valid email address.',
        ],
        'password' => [
            'required' => 'The password field is required.',
            'min' => 'The password must be at least :min characters.',
        ],
        'current_password' => [
            'required' => 'The current password field is required.',
        ],
        'new_password'=> [ 
            'required'=> 'The new password field is required.',
            'min' => 'The new password must be at least :min characters.',
        ],
    ],
];
