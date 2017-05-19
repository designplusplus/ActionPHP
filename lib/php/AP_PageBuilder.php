<?php
include_once 'toplevel.php';

class AP_PageBuilder extends AP_Object {

    private $meta_arr = array();
    private $load_js_arr = array();
    private $load_css_arr = array();
    private $page_title = 'ActionPHP';

    public function set_page_title(string $title) {
        $this->page_title = $title;
    }

    public function add_meta(array $key_val_arr) {
        $b = '<meta';
        foreach ($key_val_arr as $key=>$value) $b.= " {$key}='{$value}'";
        $b .= ' />';
    }

    public function load_js(string $url) {
        AP_Array::push($this->load_js_arr, "<script src='{$url}'></script>");
    }

    public function load_css(string $url) {
        AP_Array::push($this->load_css_arr, "<link rel='stylesheet' href='{$url}' />");
    }

    public function build() {
        $b = '<!DOCTYPE html>';
        $b .= '<html>';
        $b .= '<head>';
        $b .= "<title>{$this->page_title}</title>";
        $b .= '</head>';
        //<meta name="viewport" content="width=device-width, initial-scale=1">
        //<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    }

}