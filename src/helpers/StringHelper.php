<?php
namespace ant\helpers;

class StringHelper
{
	public static function default($src, $default) {
		return isset($src) ? $src : $default;
	}
	
	public static function generateTitle($name) {
		return \yii\helpers\Inflector::camel2Words($name);
	}
	
	public static function forEach($array, $callback) {
		$string = '';
		foreach ($array as $value) {
			$string .= call_user_func_array($callback, [$value]);
		}
		return $string;
	}
	
	public static function censor($string, $startPos, $toPos) {
		return substr($string, 0, $startPos).str_repeat('*', $toPos - $startPos).substr($string, $toPos);
	}
	
	public static function isEmpty($string) {
		return !strlen(trim($string));
	}
	
	public static function plainText($string, $allowTags = []) {
		return strip_tags($string, $allowTags);
	}
	
	public static function generateRandomNumber($length = 10) {
		return self::generateRandomString($length, '0123456789');
	}
	
	public static function generateRandomString($length = 10, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
    public static function alphaID($in, $to_num = false, $pad_up = false, $caseSensitive = false, $passKey = null)
    {
        $index = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        if($caseSensitive)
        {
            $index = $index .= 'abcdefghijklmnopqrstuvwxyz';
        }

        if ($passKey !== null)
        {
            // Although this function's purpose is to just make the
            // ID short - and not so much secure,
            // with this patch by Simon Franz (http://blog.snaky.org/)
            // you can optionally supply a password to make it harder
            // to calculate the corresponding numeric ID
            for ($n = 0; $n<strlen($index); $n++)
            {
                $i[] = substr( $index,$n ,1);
            }

            $passhash = hash('sha256',$passKey);
            $passhash = (strlen($passhash) < strlen($index)) ? hash('sha512',$passKey) : $passhash;

            for ($n=0; $n < strlen($index); $n++)
            {
                $p[] =  substr($passhash, $n ,1);
            }

            array_multisort($p,  SORT_DESC, $i);

            $index = implode($i);
        }

        $base  = strlen($index);
        if ($to_num) {

            // Digital number  <<--  alphabet letter code
            $in  = strrev($in);
            $out = 0;
            $len = strlen($in) - 1;

            for ($t = 0; $t <= $len; $t++)
            {
                $bcpow = bcpow($base, $len - $t);
                $out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
            }

            if (is_numeric($pad_up))
            {
                $pad_up--;

                if ($pad_up > 0)
                {
                    $out -= pow($base, $pad_up);
                }
            }

            $out = sprintf('%F', $out);
            $out = substr($out, 0, strpos($out, '.'));

        } else {

            // Digital number  -->>  alphabet letter code
            if (is_numeric($pad_up))
            {
                $pad_up--;
                if ($pad_up > 0)
                {
                    $in += pow($base, $pad_up);
                }
            }

            $out = "";
            for ($t = floor(log($in, $base)); $t >= 0; $t--)
            {
                $bcp = bcpow($base, $t);
                $a   = floor($in / $bcp) % $base;
                $out = $out . substr($index, $a, 1);
                $in  = $in - ($a * $bcp);
            }

            $out = strrev($out); // reverse
        }

        return $out;
    }

    public static function zeroIfEmpty($value) {
        if (empty($value)) return 0;

        return $value;
    }
}
