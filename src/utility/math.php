<?php

namespace App\Utility;

class Math {
    private static $base62 = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    public static function random_str ($length, $keyspace = ''){
        $str = '';
        $keyspace = empty($keyspace) ? self::$base62 : $keyspace;
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }
    
    public static function bcrypt($pass, $salt, $rounds = 10){
        return crypt($pass, sprintf('$2a$%02d$', $rounds) . $salt);
    }
    
    public static function from10to64 ($num) {
        if (empty($num)) return null;
        $num = ($num >= 0 and is_numeric($num)) ? $num : 0 ;
        $base = self::$base62 . '-_';
        $b = strlen($base);
        $r = $num  % $b ;
        $res = $base[$r];
        $q = floor($num/$b);
        while ($q) {
            $r = $q % $b;
            $q = floor($q/$b);
            $res = $base[$r].$res;
        }
        return $res;
    }

    public static function from64to10 ($num) {
        if (empty($num)) return null;
        $num = strval($num);
        $base = self::$base62 . '-_';
        $b = strlen($base);
        $limit = strlen($num);
        $res = strpos($base,$num[0]);
        for($i=1;$i<$limit;$i++) {
            $res = $b * $res + strpos($base,$num[$i]);
        }            
        return $res;
    }
}