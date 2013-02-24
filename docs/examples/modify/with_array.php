<?php

$url = new Url('http://www.example.com');
$url = $url->modify(['scheme' => 'https', 'host' => 'secure.example.com']);

echo $url->get();

//
// OUTPUT:
//
// https://secure.example.com
//
