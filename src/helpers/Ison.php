<?php
namespace ant\helpers;

use yii\base\Component;

 /**
  * Inspiren Object Notation
  *
  * @author Ch'ng Hui Yang <chy1988@gmail.com>, Mlax Wong <mlax@inspiren.my>
  * @since 1.0
  */
class Ison extends Component
{
    public static function encode($data)
    {
		if (YII_DEBUG && YII_LOCALHOST) throw new \Exception('DEPRECATED'); // 2019-11-05
        $ison = null;

        if (is_array($data)) {
            $json = json_encode($data);
        } else if (self::isJson($data)) {
            $json = $data;
        } else {
            $json = null;
        }

        if($json)
        {
            $ison = str_replace([
                '"',
                '{',
                '}',
            ], [
                '',
                '[',
                ']',
            ], $json);
        }

        return $ison;
    }

    public static function decode($ison, $assoc = false)
    {
		if (YII_DEBUG && YII_LOCALHOST) throw new \Exception('DEPRECATED'); // 2019-11-05
        return json_decode(static::toJson($ison), $assoc);
    }

    public static function toJson($ison)
    {
		if (YII_DEBUG && YII_LOCALHOST) throw new \Exception('DEPRECATED'); // 2019-11-05
        $pattern = '/\[([^\[^\]]+)\]/i';

        do {
            $ison = preg_replace_callback($pattern, function($input) {
                list($fullInput, $input) = $input;

                if (strpos($input, ':')) {

                    $parts = explode(',', $input);

                    foreach ($parts as $i => $part)
                    {
                        $keyOrValue =  explode(':', $part);

                        $isKey = true;

                        foreach ($keyOrValue as $j => $item)
                        {
                            if (!(strpos($item, '{') || strpos($item, '}') || strpos($item, '<') || strpos($item, '>')))
                            {
                                if (!is_numeric($item) || $isKey)
                                {
                                    $keyOrValue[$j] = '"' . $item . '"';
                                }

                                $isKey = false;
                            }
                        }

                        $parts[$i] = implode('|', $keyOrValue);
                    }

                    $output = '{' . implode(',', $parts) . '}';

                    //echo $output;die;
                } else {

                    $values = explode(',', $input);

                    foreach ($values as $j => $value)
                    {
                        if (!is_numeric($value) && !strpos($value, '{') && !strpos($value, '}') && !strpos($value, '<') && !strpos($value, '>'))
                        {
                            $values[$j] = '"' . $value . '"';
                        }
                    }

                    $input = implode(',', $values);

                    $output = '<' . $input . '>';
                }

                return str_replace(',', '&', $output);

            }, $ison);

        } while (preg_match($pattern, $ison));

        return str_replace(['<', '>', '|', '&'], ['[', ']', ':', ','], $ison);
    }

    protected static function isJson($string)
    {
		if (YII_DEBUG && YII_LOCALHOST) throw new \Exception('DEPRECATED'); // 2019-11-05
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}
?>
