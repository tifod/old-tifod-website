<?php
namespace App;

class User {
    public static function isValid ($username = ''){
        return (strtolower($username) == 'jean');
    }
}