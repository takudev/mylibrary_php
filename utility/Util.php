<?php

class Util{

    /**
     * 前方一致
     * $haystackが$needleから始まるか否かを判定します。
     * @param string $haystack
     * @param string $needle
     * @return TRUE = needleで始まる / FALSE = needleで始まらない
     */
    public static function startsWith($haystack, $needle){
    	return strpos($haystack, $needle) === 0;
    }

    /**
     * 後方一致
     * $haystackが$needleで終わるか判定します。
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    public static function endsWith($haystack, $needle){
        $length = (strlen($haystack) - strlen($needle));
        // 文字列長が足りていない場合はFALSEを返します。
        if($length < 0) return false;
        return strpos($haystack, $needle, $length) !== false;
    }


    /**
     * 部分一致
     * $haystackの中に$needleが含まれているか判定します。
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    public static function matchesIn($haystack, $needle){
        return strpos($haystack, $needle) !== false;
    }

    /**
     * 文字列中からランダムに文字を取得する。
     *
     * @param string $character ソース文字列
     * @param int length 取得する文字数。省略すると8文字
     *
     * @return string
     */
    public static function getRandomCharacters($character, $length = 8)
    {
        if(!is_numeric($length) || strlen($character) == 0){
            throw new InvalidArgumentException("length parameter is not numeric");
        }

        $return_str = "";

        for($i=0; $i<$length; $i++){
            $return_str .= substr($character, mt_rand(0, strlen($character) - 1), 1);
        }

        return $return_str;
    }


    /**
     * ランダムな文字列を生成する。
     *
     * @param int $length 必要な文字列長。省略すると 8 文字
     * @return String ランダムな文字列
     */
    public static function getRandomString($length = 8){
        $sCharList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_@";
        mt_srand();
        $sRes = "";
        for($i = 0; $i < $length; $i++){
            $sRes .= $sCharList[mt_rand(0, strlen($sCharList) - 1)];
        }
        return $sRes;
    }


    /**
     * ランダムな文字列を生成する。
     * 文字列は英数字と記号文字「!@#$%&*()-+=<>;:」から構成されます。
     * 記号は必ず2文字入ります。
     *
     * @param int $length 必要な文字列長。省略すると 8 文字
     * @return string ランダムな文字列
     */
    public static function getRandomStringStrong8($length = 8)
    {
        if(!is_numeric($length) || $length <= 2){
            throw new InvalidArgumentException("length parameter is not expected. more than 2 numeric character require. [length:$length]");
        }

        $numeric = '0123456789';
        $alphabet_lower = 'abcdefghijklmnopqrstuvwxyz';
        $alphabet_upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $other_character = '!@#$%&*()-+=<>;:';

        $character = $numeric . $alphabet_lower . $alphabet_upper . $other_character;
        $random_string = self::getRandomCharacters($character, $length);

        // 記号を２文字以上含ませる
        $pattern = '/[' . $other_character . '].*[' . $other_character . ']/';
        while (preg_match($pattern, $random_string) !== 1) {
            $random_string{mt_rand(0, strlen($random_string) - 1)} = self::getRandomCharacters($other_character, 1);
        }
        return $random_string;
    }
    
    
    /**
     * パスで指定されたCSVファイルを配列に変換して返します
     * CSVファイルをメモリに展開し、文字コード変換後、デリミタ（デフォルト：カンマ）で分割し、配列に格納し返却します。
     * 
     * ※fgetcsv 5C問題対策に作成したfunctionです。
     * 
     * @param string $file_path ファイルパス
     * @param array  $options   オプション配列
     *
     * @return array $values CSVデータ配列
     *
     */
    public static function convertCsvStringToArray($file_path, $options = array())
    {

        $to_encoding   = isset($options['to_encoding']) ? $options['to_encoding'] : 'utf-8';
        $from_encoding = isset($options['from_encoding']) ? $options['from_encoding'] : 'sjis-win';
        $delimiter     = isset($options['delimiter']) ? $options['delimiter'] : ',';
        $locale        = isset($options['local']) ? $options['local'] : 'ja_JP.UTF-8';

        $csv_string = file_get_contents($file_path);
        $csv_string = mb_convert_encoding($csv_string, $to_encoding, $from_encoding);

        $fp = fopen('php://memory', 'r+');
        fwrite($fp, $csv_string);
        rewind($fp);
        $current_locale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, $locale);
        $values = array();
        while ($value = fgetcsv($fp, 10240, $delimiter)) {
            $values[] = $value;
        }
        setlocale(LC_ALL, $current_locale);
        fclose($fp);
        return $values;
    }
}
?>
