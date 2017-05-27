<?php

date_default_timezone_set('Asia/Taipei');

// ==================================================== Functions ====================================================//

function ap_fun_array():array {
    $args = func_get_args();
    $args_len = count($args);
    if($args_len == 1 && is_int($args[0]))
        $args = array_fill(0, $args[0], null);
    return $args;
}

function ap_fun_boolean(bool $expression):bool {
    if($expression == 0) return false;
    if(is_nan($expression)) return false;
    if($expression == "") return false;
    if(is_null($expression)) return false;
    if(is_array($expression) && count($expression) == 0) return false;
    return true;
}

function ap_fun_int(int $number):int {
    return $number;
}

function ap_fun_isFinite(float $number):bool {
    return $number == INF || $number == -INF ? false : true;
}

function ap_fun_isNaN($mixed):bool {
    return is_nan($mixed) ? true : false;
}

function ap_fun_isNull($mixed):bool {
    return is_null($mixed) ? true : false;
}

function ap_fun_isArray($mixed):bool {
    return is_array($mixed) ? true : false;
}

function ap_fun_number($number) {
    if(is_null($number)) return 0;
    if(is_bool($number) && $number == true) return 1;
    if(is_bool($number) && $number == false) return 0;
    if(is_nan($number)) return NAN;
    if($number == "") return 0;
    if(is_string($number)) return NAN;
    return $number;
}

function ap_fun_parseFloat($mixed):float {
    return floatval($mixed);
}

function ap_fun_parseInt($mixed):int {
    return intval($mixed);
}

function ap_fun_string($mixed):string {
    if (is_null($mixed)) return 'null';
    if (is_bool($mixed) && $mixed == true) return 'true';
    if (is_bool($mixed) && $mixed == false) return 'false';
    if (is_nan($mixed)) return 'NaN';
    if($mixed instanceof AP_Object) return $mixed->toString();
    return $mixed;
}

function ap_fun_trace() {
    if(!function_exists('r_trace')) {
        function r_trace(array $args, int $tab_count) {
            if($tab_count == 0) echo 'Array<br/>(<br/>';
            $space_repeat = 5;
            for ($i = 0, $space_b = ''; $i < ($tab_count+1)*$space_repeat; $i++) $space_b .= '&nbsp;';
            for ($i = 0, $space_e = ''; $i < ($tab_count)*$space_repeat; $i++) $space_e .= '&nbsp;';
            foreach ($args as $key => $value) {
                if (!is_array($value)) {
                    if(is_bool($value) && $value == true) $value = 'true';
                    if(is_bool($value) && $value == false) $value = 'false';
                    if(is_null($value)) $value = 'null';
                    if($value instanceof AP_Object) $value = $value->toString();
                    echo "{$space_b}[{$key}] => {$value}<br/>";
                } else {
                    echo "{$space_b}[{$key}] => Array<br/>{$space_b}(<br/>";
                    r_trace($value, $tab_count+1);
                }
            }
            echo "{$space_e})<br/>";
        }
    }
    $args = func_get_args();
    $args_len = count($args);
    echo '<div style="font-size: 10px;background: rgba(0,0,0,0.8);color: white; left:0; top:0; right:0; padding:10px; border-bottom: 1px dashed gray;">';
    if($args_len == 1)
        if(is_array($args[0])) {
            r_trace($args[0],0);
        } else {
            $value = $args[0];
            if(is_bool($value) && $value == true) $value = 'true';
            if(is_bool($value) && $value == false) $value = 'false';
            if(is_null($value)) $value = 'null';
            if($value instanceof AP_Object) $value = $value->toString();
            echo $value;
        }
    else
        r_trace($args,0);
    echo '</div>';
}

// ==================================================== Classes ====================================================//

class AP_Object extends stdClass {

    public function toString():string {
        return static::class;
    }

}

class AP_String extends  AP_Object {

    private function __construct() {}
    public static function length($str){return strlen($str);} //取得文字長度
    public static function charAt($str, $index){return $str{$index};} //取得字串索引字元
    public static function charCodeAt($str, $index){return ord($str{$index});} //取得字串索引字元ASCII編號
    public static function fromCharCode($code){return chr($code);} //取得ASCII編號字元
    public static function indexOf($str, $match){ //從字串開頭搜尋相符字串索引
        $pos = strpos($str, $match);
        return is_bool($pos) && $pos == false ? -1:$pos;
    }
    public static function lastIndexOf($str, $match){ //從字串結尾搜尋相符字串索引
        $pos = strrpos($str, $match);
        return is_bool($pos) && $pos == false ? -1:$pos;
    }
    public static function repeat($str, $match){return substr_count($str, $match);} //相符字串出現次數
    public static function split($str, $delimiter){return explode($delimiter, $str);} //取得打散字串陣列
    public static function lower($str){return strtolower($str);} //字串字母皆小寫
    public static function upper($str){return strtoupper($str);} //字串字母皆大寫
    public static function trim($str){return trim($str);} //去掉字串左右側空白符
    public static function substr($str, $start, $len = 0) //取得子字串，若無設定長度則取得開始點到最尾端字串
    {return substr($str, $start, $len == 0 ? AP_String::length($str)-$start : $len);}
    public static function replace($str, $pattern, $replace) //取代指定字串
    {return preg_replace($pattern, $replace, $str);}

}

class AP_Array extends AP_Object {

    private function __construct() {}
    public static function length(&$arr){return count($arr);} //取得陣列長度
    public static function join(&$arr, $delimiter){return implode($delimiter, $arr);} //取得合併陣列成字串
    public static function pop(&$arr){return array_pop($arr);} //剔除最後元素，並回傳該元素
    public static function push(&$arr, $elem){array_push($arr, $elem);} //新增元素在陣列最後方
    public static function shift(&$arr){return array_shift($arr);} //剔除最前元素，並回傳該元素
    public static function unshift(&$arr, $elem){array_unshift($arr, $elem);} //新增元素在陣列最前方
    public static function reverse(&$arr){return array_reverse($arr);} //回傳反向陣列
    public static function sort(&$arr, $type = SORT_NATURAL){sort($arr, $type);} //陣列排序
    public static function inArray(&$arr, $match){return in_array($match, $arr, true);} //檢查子元素是否存在
    public static function slice(&$arr, $start, $len = 0) //取得子字串，若無設定長度則取得開始點到最尾端字串
    {return array_slice($arr, $start, $len == 0 ? count($arr)-$start : $len);}
    public static function splice(&$arr, $start, $len = 0, $add = null) //陣列中刪增元素
    {return is_array($add) ? array_splice($arr, $start, $len, $add) : array_splice($arr, $start, $len);}
    public static function concat(/*...*/) //合併一個或多個陣列
    {
        $args = func_get_args();
        $merge = array();
        foreach ($args as $value) {
            $merge = array_merge($merge, $value);
        }
        return $merge;
    }
}

class AP_Math extends AP_Object {

    private function __construct() {}
    public static function abs($num){return abs($num);}
    public static function acos($num){return acos($num);}
    public static function asin($num){return asin($num);}
    public static function atan($num){return atan($num);}
    public static function atan2($y, $x){return atan2($y, $x);}
    public static function ceil($num){return ceil($num);}
    public static function cos($num){return cos($num);}
    public static function exp($num){return exp($num);}
    public static function floor($num){return floor($num);}
    public static function log($num){return log($num);}
    public static function max(/*...*/){return max(func_get_args());}
    public static function min(/*...*/){return min(func_get_args());}
    public static function pow($base, $exp){return pow($base, $exp);}
    public static function random($min, $max){return mt_rand($min, $max);} //正整數間的亂數
    public static function round($num){return round($num);}
    public static function sin($num){return sin($num);}
    public static function sqrt($num){return sqrt($num);}
    public static function tan($num){return tan($num);}
    
}


class AP_Date extends AP_Object {

    private function __construct() {}
    public static function getWeek(){return date("N")."-".date("l");} //取得星期中第幾天 1~7 monday~sunday
    public static function getYear(){return date("Y");} //取得年份如2015
    public static function getMonth(){return date("m");} //取得月份1~12
    public static function getDate(){return date("d");} //取得日1~31
    public static function getHour(){return date("H");} //取得時
    public static function getMinute(){return date("i");} //取得分
    public static function getSecond(){return date("s");} //取得秒
    public static function getMillisecond(){return AP_Math::round(AP_String::split(microtime()," ")[0]*1000);} //取得毫秒
    public static function getFullDate(){return date("Y-m-d H:i:s");} //取得資料庫時間格式
    public static function getTime(){return time();} //取得 January 1 1970 00:00:00 GMT 至今秒數
    public static function getTimeToFullDate($time){return date("Y-m-d H:i:s",$time);} // 至今秒數轉換資料庫時間格式

}


class AP_Validator extends AP_Object {

    private function __construct() {}
    public static function test_email($str){return !filter_var($str, FILTER_VALIDATE_EMAIL)?false:true;}
    public static function test_url($str){return !filter_var($str, FILTER_VALIDATE_URL)?false:true;}
    public static function test_ipv4($str){return !filter_var($str, FILTER_VALIDATE_IP)?false:true;}
    public static function test_ipv6($str){return !filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)?false:true;}
    public static function test_int($num){return !filter_var($num, FILTER_VALIDATE_INT)?false:true;}
    public static function test_uint($num){return !filter_var($num, FILTER_VALIDATE_INT)?false:$num<0?false:true;}
    public static function test_float($num){return !filter_var($num, FILTER_VALIDATE_FLOAT)?false:true;}
    public static function test_ufloat($num){return !filter_var($num, FILTER_VALIDATE_FLOAT)?false:$num<0?false:true;}
    public static function test_between($num, $min, $max){return !filter_var($num, FILTER_VALIDATE_FLOAT, array("options" => array("min_range"=>$min, "max_range"=>$max)))?false:true;}
    public static function test_length($str, $min, $max){return !filter_var(AP_String::length($str), FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min, "max_range"=>$max)))?false:true;}
    public static function test_chineseName($str){return !filter_var($str, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^([\x7f-\xff]+)$/")))?false:true;}
    public static function test_englishName($str){return !filter_var($str, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^([ A-Za-z]+)$/")))?false:true;}
    public static function test_password($str){return !filter_var($str, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[_a-zA-Z0-9!@#$%^&*]+$/")))?false:true;}
    public static function test_variableName($str){return !filter_var($str, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[_A-Za-z]{1}[_0-9A-Za-z]+$/")))?false:true;}
    public static function test_color($hex){return !filter_var($hex, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^#([0-9A-Za-z]{3}|[0-9A-Za-z]{6}|[0-9A-Za-z]{8})$/")))?false:true;}
    public static function test_image($filename){return !filter_var($filename, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[_0-9A-Za-z\-\x7f-\xff]+\.(gif|jpg|jpeg|png)$/")))?false:true;}
    public static function test_fileType($filename, &$types_arr){return !filter_var($filename, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[_0-9A-Za-z\-\x7f-\xff]+\.(".AP_Array::join($types_arr, "|").")$/")))?false:true;}
    public static function test_preg($str, $pattern){return !filter_var($str, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>$pattern)))?false:true;}
    public static function test_fileExists($file){return file_exists($file)?true:false;}
    public static function test_date($str){
        $d = AP_String::split($str,"-");
        if(!AP_Validator::test_between($d[1],1,12))return false;
        if(!AP_Validator::test_between($d[2],1,31))return false;
        return !filter_var($str, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/")))?false:true;
    }

}

class AP_Coder extends AP_Object {

    private function __construct() {}
    public static function encode_url($url){return urlencode($url);} //取得編碼後網址
    public static function decode_url($encoded_url){return urldecode($encoded_url);} //取得解碼後網址
    public static function encode_base64($str){return base64_encode($str);} //取得Base64編碼後字串
    public static function decode_base64($encoded_str){return base64_decode($encoded_str);} //取得Base64解碼後字串
    public static function encode_json($arr){return json_encode($arr);} //取得Json編碼後字串
    public static function decode_json($json_str){return json_decode($json_str, true);} //取得Json解碼後陣列

}