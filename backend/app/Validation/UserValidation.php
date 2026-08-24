<?php

namespace App\Validation;

use App\Services\I18n;

class UserValidation
{
    public static function create(): array
    {
        return [
            'email' => [
                'rules' => 'required|valid_email|max_length[255]',
                'errors' => [
                    'required'    => I18n::line('Validation.emailRequired'),
                    'valid_email' => I18n::line('Validation.emailInvalid'),
                    'max_length'  => I18n::line('Validation.emailMax'),
                ],
            ],
            'phone' => [
                'rules' => 'permit_empty|max_length[20]',
                'errors' => [
                    'max_length' => I18n::line('Validation.phoneMax'),
                ],
            ],
            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required'   => I18n::line('Validation.passwordRequired'),
                    'min_length' => I18n::line('Validation.passwordMin'),
                ],
            ],
            'role' => [
                'rules' => 'permit_empty|in_list[admin,user]',
                'errors' => [
                    'in_list' => I18n::line('Validation.roleInvalid'),
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
                    'max_length' => I18n::line('Validation.firstNameMax'),
                ],
            ],
            'lastName' => [
                'rules' => 'permit_empty|max_length[150]',
                'errors' => [
                    'max_length' => I18n::line('Validation.lastNameMax'),
                ],
            ],
            'phone' => [
                'rules' => 'permit_empty|max_length[20]',
                'errors' => [
                    'max_length' => I18n::line('Validation.phoneMax'),
                ],
            ],
            'email' => [
                'rules' => 'permit_empty|valid_email|max_length[255]',
                'errors' => [
                    'valid_email' => I18n::line('Validation.emailInvalid'),
                    'max_length'  => I18n::line('Validation.emailMax'),
                ],
            ],
            'password' => [
                'rules' => 'permit_empty|min_length[8]',
                'errors' => [
                    'min_length' => I18n::line('Validation.passwordMin'),
                ],
            ],
            'role' => [
                'rules' => 'permit_empty|in_list[admin,user]',
                'errors' => [
                    'in_list' => I18n::line('Validation.roleInvalid'),
                ],
            ],
            'status' => [
                'rules' => 'permit_empty|in_list[active,inactive]',
                'errors' => [
                    'in_list' => I18n::line('Validation.statusInvalid'),
                ],
            ],
        ];
    }
}
