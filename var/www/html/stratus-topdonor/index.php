<?php
header('Content-Type: text/plain');
try {
	$dom = new DOMDocument;
	libxml_use_internal_errors(true);
	$dom->loadHTML(file_get_contents('https://stratusnetwork.buycraft.net'));
	libxml_clear_errors();
	$finder = new DomXPath($dom);
	$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' ign ')]");
	echo $nodes[0]->nodeValue;
} catch(Exception $e) {
	echo 'BuyCraft Error!';
}
?>