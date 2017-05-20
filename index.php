<?php
include_once 'actionphp/toplevel.php';
include_once 'actionphp/AP_PageBuilder.php';

AP_PageBuilder::head_set_title("ActionPHP-Edwin");
AP_PageBuilder::head_add_meta(array('charset' => 'UTF-8'));
AP_PageBuilder::head_add_meta(array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1'));
AP_PageBuilder::head_load_css('https://www.w3schools.com/w3css/4/w3.css');
AP_PageBuilder::head_load_js('https://code.jquery.com/jquery-3.2.1.min.js');
AP_PageBuilder::body_set_html("pages/{$_GET['page']}.html", array('cool' => 'cool'));
AP_PageBuilder::build();