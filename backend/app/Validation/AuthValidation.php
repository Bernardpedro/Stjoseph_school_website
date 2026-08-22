<?php

namespace App\Validation;

class AuthValidation
{
    public static function register(): array
    {
        return [
            'firstName' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'First name is required.',
                    'max_length' => 'First name cannot exceed 100 characters.',
                ],
            ],

            'lastName' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required'   => 'Last name is required.',
                    'max_length' => 'Last name cannot exceed 100 characters.',
                ],
            ],

            'email' => [
                'rules' => 'required|valid_email|max_length[255]',
                'errors' => [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Please provide a valid email address.',
                    'max_length'  => 'Email cannot exceed 255 characters.',
                ],
            ],

            'phone' => [
                'rules' => 'required|max_length[20]',
                'errors' => [
                    'required'   => 'Phone number is required.',
                    'max_length' => 'Phone number cannot exceed 20 characters.',
                ],
            ],

            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required'   => 'Password is required.',
                    'min_length' => 'Password must be at least 8 characters.',
                ],
            ],

            'passwordConfirmation' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Password confirmation is required.',
                    'matches'  => 'Password confirmation does not match.',
                ],
            ],
        ];
    }

    public static function login(): array
    {
        return [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Please provide a valid email address.',
                ],
            ],

            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password is required.',
                ],
            ],
        ];
    }

    public static function verifyEmail(): array
    {
        return [
            'token' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Verification token is required.',
                ],
            ],
        ];
    }

    public static function forgotPassword(): array
    {
        return [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Please provide a valid email address.',
                ],
            ],
        ];
    }

    public static function resetPassword(): array
    {
        return [
            'token' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Reset token is required.',
                ],
            ],

            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required'  => 'Password is required.',
                    'min_length' => 'Password must be at least 8 characters.',
                ],
            ],

            'passwordConfirmation' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Password confirmation is required.',
                    'matches'  => 'Password confirmation does not match.',
                ],
            ],
        ];
    }

}