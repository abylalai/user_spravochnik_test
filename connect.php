<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/sql/redbean.php';
    R::setup('mysql:host=localhost;dbname=db',
    'db', 'db');
    if(!R::testConnection()) die('No DB connection!');
?>