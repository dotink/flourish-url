<?php

$url = new Url('http://www.example.com/deep/path/test');
$url = $url->modify('../newpath?param=value');

echo $url->get();

//
// OUTPUT:
//
// http://www.example.com/deep/newpath?param=value
//
