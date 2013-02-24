<?php

$url = new Url('http://www.example.com/path');
$url = $url->modify('/newpath');

echo $url->get();

//
// OUTPUT:
//
// http://www.example.com/newpath
//
