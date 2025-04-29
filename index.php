<?php
include 'config.php';

$html = file_get_contents($config['template']);
$keywords = explode(PHP_EOL, file_get_contents($config['keywords']));

if(!file_exists($config['sitemap'])) {
    $doc = new DOMDocument('1.0', 'UTF-8');
    $doc->formatOutput = true;

    $urlset = $doc->createElement('urlset');
    $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

    $doc->appendChild($urlset);

    foreach ($keywords as $keyword) {
        $url = $config['url'] . '?' . $config['parameter'] . '=' . urlencode(trim($keyword)); 
        
        $urlElement = $doc->createElement('url');
    
        $locElement = $doc->createElement('loc', htmlspecialchars($url));
        $urlElement->appendChild($locElement);
    
        $lastmodElement = $doc->createElement('lastmod', date("Y-m-d"));
        $urlElement->appendChild($lastmodElement);

        $changefreqElement = $doc->createElement('changefreq', 'daily');
        $urlElement->appendChild($changefreqElement);

        $urlset->appendChild($urlElement);
    }

    $doc->save($config['sitemap']);
}

if(isset($_GET[$config['parameter']]) && !empty($_GET[$config['parameter']])) {
    foreach($keywords as $keyword) {
        if(strtolower(trim($_GET[$config['parameter']])) == strtolower(trim($keyword))) {
            $brand = trim($keyword);
            $url = $config['url'] . '?' . $config['parameter'] . '=' . urlencode($brand); 

            $html = str_replace('{{ BRAND }}', $brand, $html);
            $html = str_replace('{{ URL }}', $url, $html);
            echo $html;
            exit;
        }
    }
}
header("HTTP/1.1 404 Not Found");
echo "<h1>NEWS</h1>";
exit;