<?php

$url = new Url('http://www.github.com');
$url = $url->modify(['scheme' => 'https']);

echo $url->get();

//
// OUTPUT:
//
// https://www.github.com
//
