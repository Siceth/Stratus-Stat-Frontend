<?php
$foundMap = false;
if(isset($_GET['request'])) {
	header('Content-type: text/plain');
	$cacheDir = '../../cache/_stats/stratus-maps/';
	if(!isset($_GET['force-renew']) && file_exists($cacheDir.$_GET['request'].'.png') && !is_dir($cacheDir.$_GET['request'].'.png') && (time()-(file_exists($cacheDir.$_GET['request'].'.png') ? filemtime($cacheDir.$_GET['request'].'.png') : 0)) < 2592000) {
		echo file_get_contents($cacheDir.$_GET['request'].'.png');
	} else {
		$mapImage = false;
		$stratusMethods = [
			'https://stratus.nyc3.digitaloceanspaces.com/maps/tdm/standard/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/ctw/standard/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/ctf/standard/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/dtc/standard/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/dtcm/standard/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/dtm/standard/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/arcade/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/payload/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/koth/standard/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/blitz/classic/standard/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/blitz/rage/standard/',
			'https://stratus.nyc3.digitaloceanspaces.com/maps/blitz/ffa/standard/'
		];
		$occMethods = [
			'https://raw.githubusercontent.com/OvercastCommunity/overcastcommunity.github.io/master/assets/img/maps/'
		];
		$resourcePileMethods = [
			'https://raw.githubusercontent.com/MCResourcePile/pgm-maps/master/maps/',
			'https://raw.githubusercontent.com/MCResourcePile/stratus-maps/master/maps/',
			'https://raw.githubusercontent.com/MCResourcePile/avicus-maps/master/maps/',
			'https://raw.githubusercontent.com/MCResourcePile/overcast-maps-a-to-f/master/maps/',
			'https://raw.githubusercontent.com/MCResourcePile/overcast-maps-g-to-n/master/maps/',
			'https://raw.githubusercontent.com/MCResourcePile/overcast-maps-o-to-z/master/maps/',
			'https://raw.githubusercontent.com/MCResourcePile/rfw-maps/master/maps/'
		];
		$mapImage = $mapImage ?: tryMethodsWithName($occMethods, normalizeOCCMapName($_GET['request']));
		$mapImage = $mapImage ?: tryMethodsWithName($resourcePileMethods, normalizeResourcePileMapName($_GET['request']));
		$mapImage = $mapImage ?: tryMethodsWithName($resourcePileMethods, normalizeResourcePileMapNameAgain($_GET['request']));
		if($foundMap) {
			ob_start();
			echo $mapImage;
			$cacheOutput = ob_get_contents();
			ob_end_flush();
			file_put_contents($cacheDir.$_GET['request'].'.png', $cacheOutput);
		} else {
			//http_response_code(404);
			echo file_get_contents('404.png');
		}
	}
} else {
	header('Content-type: text/html');
	?>
Stratus Network Map Cache<br>
=========================<br>
<br>
Usage: /&lt;request&gt;[?force-renew]<br>
<br>
<form method="GET" action="">
	<input type="text" name="request" placeholder="Formal map name" required />
	<input type="submit">
</form>
	<?php
	
}

function tryMethodsWithName($methods, $mapName) {
	global $foundMap;
	foreach($methods as $method) {
		if($foundMap) {
			break;
		}
		$map = getMapImage($method.$mapName.'/map.png');
		if($map['success']) {
			$foundMap = true;
			return $map['data'];
		}
	}
}

function getMapImage($url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 2);
	$output = curl_exec($ch);
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if($status < 300 && $status != 0) {
		return [
			'success' => true,
			'data' => $output
		];
	}
	return [
		'success' => false
	];
}

function normalizeOCCMapName($name) {
	return preg_replace('/[^A-Za-z0-9 _.\']/', '', str_replace(':', '', $name));
}

function normalizeResourcePileMapName($name) {
	return preg_replace('/[^a-z0-9_.]/', '_', str_replace(':', '', strtolower($name)));
}

function normalizeResourcePileMapNameAgain($name) {
	return preg_replace('/[^a-z0-9_.]/', '_', preg_replace('/[:\']/', '', strtolower($name)));
}
?>
