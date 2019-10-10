<?php
header('Content-type: text/plain');

$cache = '../../cache/_stats/stratus-gameplay/index.php.tmp';
if(file_exists($cache) && filesize($cache) && time()-5<filemtime($cache)) {
	include($cache);
} else {
	ob_start();
	
	if(file_exists('../../cache/_stats/stratus-gameplay/output.log') && file_exists('../../cache/_stats/stratus-gameplay/complete_output.log')) {
		/*
		if(sha1_file('../../cache/_stats/stratus-gameplay/output.log')!=sha1_file('../../cache/_stats/stratus-gameplay/complete_output.log')) {
			echo 'NOTE: This page is being updated.'."\n\n\n";
		}
		*/
		$stats = false;
		foreach(explode("\n", file_get_contents('../../cache/_stats/stratus-gameplay/complete_output.log')) as $line) {
			if($stats) {
				echo $line."\n";
			} elseif(substr($line, 0, 3)==';;;') {
				$stats = true;
			}
		}
	} else {
		echo 'The web utility is either rebooting or not in operation.';
	}
	
	$cacheOutput = ob_get_contents();
	ob_end_flush();
	file_put_contents($cache, $cacheOutput);
}
?>