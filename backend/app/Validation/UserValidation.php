<?php

namespace App\Validation;

class UserValidation
{
    public static function create(): array
    {
        return [
            'email' => [
                'rules' => 'required|valid_email|max_length[255]',
                'errors' => [
                    'required'    => 'Email is required.',
                    'valid_email' => 'Please provide a valid email address.',
                    'max_length'  => 'Email cannot exceed 255 characters.',
                ],
            ],

            'phone' => [
                'rules' => 'permit_empty|max_length[20]',
                'errors' => [
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

            'role' => [
                'rules' => 'permit_empty|in_list[admin,user]',
                'errors' => [
                    'in_list' => 'Role must be admin or user.',
                ],
            ],
        ];
    }

    public static function update(): array
    {
        return [
            'firstName' => [
                'rules' => 'permit_empty|max_length[150]',
                'errors' => [
                    'max_length' => 'First name cannot exceed 150 characters.',
                ],
            ],

            'lastName' => [
                'rules' => 'permit_empty|max_length[150]',
                'errors' => [
                    'max_length' => 'Last name cannot exceed 150 characters.',
                ],
            ],

            'phone' => [
                'rules' => 'permit_empty|max_length[20]',
                'errors' => [
                    'max_length' => 'Phone number cannot exceed 20 characters.',
                ],
            ],

            'email' => [
                'rules' => 'permit_empty|valid_email|max_length[255]',
                'errors' => [
                    'valid_email' => 'Please provide a valid email address.',
                    'max_length'  => 'Email cannot exceed 255 characters.',
                ],
            ],

            'password' => [
                'rules' => 'permit_empty|min_length[8]',
                'errors' => [
                    'min_length' => 'Password must be at least 8 characters.',
                ],
            ],

            'role' => [
                'rules' => 'permit_empty|in_list[admin,user]',
                'errors' => [
                    'in_list' => 'Role must be admin or user.',
                ],
            ],

            'status' => [
                'rules' => 'permit_empty|in_list[active,inactive]',
                'errors' => [
                    'in_list' => 'Status must be active or inactive.',
                ],
            ],
        ];
    }
}
