<?php

namespace App\Supplier\app\Http\Requests;

class LoginRequest {

    public static function validateLogin($data) {
        $errors = [];

        if (empty($data['login'])) {
            $errors['login'] = 'L’identifiant est requis';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Le mot de passe est requis';
        }

        return $errors;
    }

}