<?php

namespace App\Validation;

use App\Services\I18n;

class AuthValidation
{
    public static function register(): array
    {
        return [
            'firstName' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required'   => I18n::line('Validation.firstNameRequired'),
                    'max_length' => I18n::line('Validation.firstNameMax'),
                ],
            ],
            'lastName' => [
                'rules' => 'required|max_length[100]',
                'errors' => [
                    'required'   => I18n::line('Validation.lastNameRequired'),
                    'max_length' => I18n::line('Validation.lastNameMax'),
                ],
            ],
            'email' => [
                'rules' => 'required|valid_email|max_length[255]',
                'errors' => [
                    'required'    => I18n::line('Validation.emailRequired'),
                    'valid_email' => I18n::line('Validation.emailInvalid'),
                    'max_length'  => I18n::line('Validation.emailMax'),
                ],
            ],
            'phone' => [
                'rules' => 'required|max_length[20]',
                'errors' => [
                    'required'   => I18n::line('Validation.phoneRequired'),
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
            'passwordConfirmation' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => I18n::line('Validation.passwordConfirmRequired'),
                    'matches'  => I18n::line('Validation.passwordMismatch'),
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
                    'required'    => I18n::line('Validation.emailRequired'),
                    'valid_email' => I18n::line('Validation.emailInvalid'),
                ],
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => I18n::line('Validation.passwordRequired'),
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
                    'required' => I18n::line('Validation.tokenRequired'),
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
                    'required'    => I18n::line('Validation.emailRequired'),
                    'valid_email' => I18n::line('Validation.emailInvalid'),
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
                    'required' => I18n::line('Validation.resetTokenRequired'),
                ],
            ],
            'password' => [
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required'   => I18n::line('Validation.passwordRequired'),
                    'min_length' => I18n::line('Validation.passwordMin'),
                ],
            ],
            'passwordConfirmation' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => I18n::line('Validation.passwordConfirmRequired'),
                    'matches'  => I18n::line('Validation.passwordMismatch'),
                ],
            ],
        ];
    }
}
