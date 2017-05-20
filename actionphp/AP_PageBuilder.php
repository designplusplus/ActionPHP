<?php

class AP_PageBuilder extends AP_Object {

    private static $meta_arr = array();
    private static $load_js_arr = array();
    private static $load_css_arr = array();
    private static $head_js = '';
    private static $head_css = '';
    private static $page_title = 'ActionPHP';
    private static $page_body = '';

    private function __construct() {}

    public static function head_set_title(string $title) {
        self::$page_title = $title;
    }

    public static function head_add_meta(array $key_val_arr) {
        $b = '<meta';
        foreach ($key_val_arr as $key=>$value) $b.= " {$key}='{$value}'";
        $b .= ' />';
        AP_Array::push(self::$load_js_arr, $b);
    }

    public static function head_load_js(string $url) {
        AP_Array::push(self::$load_js_arr, "<script src='{$url}'></script>");
    }

    public static function head_load_css(string $url) {
        AP_Array::push(self::$load_css_arr, "<link rel='stylesheet' href='{$url}' />");
    }

    public static function body_set_html(string $url, array $key_val_arr=null) {
        $body = AP_String::trim(file_get_contents($url));
        $slice_start_record = array();
        $slice_end_record = array();
        $seg = '<!--css-->';
        $findIndex = AP_String::indexOf($body, $seg);
        if($findIndex != -1) {
            AP_Array::push($slice_end_record, $findIndex);
            $slice_start_record['css'] = $findIndex + AP_String::length($seg) + 1;
        }
        $seg = '<!--js-->';
        $findIndex = AP_String::indexOf($body, $seg);
        if($findIndex != -1) {
            AP_Array::push($slice_end_record, $findIndex);
            $slice_start_record['js'] = $findIndex + AP_String::length($seg) + 1;
        }
        $seg = '<!--html-->';
        $findIndex = AP_String::indexOf($body, $seg);
        if($findIndex != -1) {
            AP_Array::push($slice_end_record, $findIndex);
            $slice_start_record['html'] = $findIndex + AP_String::length($seg) + 1;
        }
        asort($slice_end_record);
        asort($slice_start_record);
        AP_Array::shift($slice_end_record);
        AP_Array::push($slice_end_record, AP_String::lastIndexOf($body,'>') + 1);

        $types = array_keys($slice_start_record);
        $slice_start = array_values($slice_start_record);
        $slice_end = $slice_end_record;
        $code = array();
        foreach ($types as $k=>$v)
            $code[$v] = AP_String::substr($body, $slice_start[$k], $slice_end[$k] - $slice_start[$k]);

        // html compress
        if(isset($code['html'])) {
            $search = array(
                '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
                '/[^\S ]+\</s',     // strip whitespaces before tags, except space
                '/(\s)+/s',         // shorten multiple whitespace sequences
                '/<!--(.|\s)*?-->/' // Remove HTML comments
            );
            $replace = array(
                '>',
                '<',
                '\\1',
                ''
            );
            if(ap_fun_isNull($key_val_arr) == false)
                foreach ($key_val_arr as $key=>$value) {
                    AP_Array::push($search, "/\{\{$key\}\}/");
                    AP_Array::push($replace, $value);
                }
            self::$page_body = AP_String::replace($code['html'], $search, $replace);
        }

        // css compress
        if(isset($code['css'])) {
            $code['css'] = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code['css']);
            $code['css'] = str_replace(': ', ':', $code['css']);
            $code['css'] = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $code['css']);
            self::$head_css = $code['css'];
        }

        // js compress
        if(isset($code['js'])) {
            $code['js'] = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $code['js']); // remove comments
            $code['js'] = str_replace(array("\r\n", "\r", "\t", "\n", '  ', '    ', '     '), '', $code['js']); // remove tabs, spaces, newlines, etc.
            $code['js'] = preg_replace(array('(( )+\))', '(\)( )+)'), ')', $code['js']); // remove other spaces before/after )
            self::$head_js = $code['js'];
        }
    }

    public static function build() {
        $b = '<!DOCTYPE html>';
        $b .= '<html>';
        $b .= '<head>';
        $b .= '<title>'.self::$page_title.'</title>';
        foreach(self::$meta_arr as $meta) $b .= $meta;
        foreach(self::$load_css_arr as $css) $b .= $css;
        foreach(self::$load_js_arr as $js) $b .= $js;
        $b .= self::$head_css;
        $b .= self::$head_js;
        $b .= '</head>';
        $b .= '<body>';
        $b .= self::$page_body;
        $b .= '</body>';
        $b .= '</html>';
        echo $b;
    }

}