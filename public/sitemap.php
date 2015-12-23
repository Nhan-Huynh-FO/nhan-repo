<?php
header('Content-Type: application/xml; charset=utf-8');
$contentUrl = 'http://vnexpress.net/sitemap/1002565/sitemap.xml';
$data = file_get_contents($contentUrl);
echo $data;
?>