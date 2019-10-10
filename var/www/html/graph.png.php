<?php
header('Content-Type: image/png');

include("../cgi-bin/_stats/pCharts.class.php");
include("../cgi-bin/_stats/pColor.class.php");
include("../cgi-bin/_stats/pColorGradient.class.php");
include('../cgi-bin/_stats/pData.class.php');
include('../cgi-bin/_stats/pDraw.class.php');
include("../cgi-bin/_stats/pException.class.php");
include("../cgi-bin/_stats/pImageMapInterface.class.php");
include("../cgi-bin/_stats/pImageMapFile.class.php");
include('../cgi-bin/_stats/pScatter.class.php');

require '../cgi-bin/_stats/ConnectionHandler.class.php';
require '../cgi-bin/_stats/CacheHandler.class.php';

$width = 720;
$height = 480;

$graphs = [
	'kills_vs_deaths' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Kills',
			'y' => 'Deaths',
			'title' => 'Kills vs Deaths',
			'x_data' => 'kills',
			'y_data' => 'deaths',
			'minimum_merit' => .1,
			'filter_staff' => false,
			'fit' => true
		]
	],
	'hours_played_vs_kills' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Hours',
			'y' => 'Kills',
			'title' => 'Hours Played vs Kills',
			'x_data' => 'hours_played',
			'y_data' => 'kills',
			'minimum_merit' => .5,
			'filter_staff' => false,
			'fit' => true
		]
	],
	'hours_played_vs_droplets' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Hours',
			'y' => 'Droplets',
			'title' => 'Hours Played vs Droplets',
			'x_data' => 'hours_played',
			'y_data' => 'droplets',
			'minimum_merit' => .5,
			'filter_staff' => true,
			'fit' => true
		]
	],
	'hours_played_vs_rank' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Hours',
			'y' => 'Rank',
			'title' => 'Hours Played vs Rank',
			'x_data' => 'hours_played',
			'y_data' => 'kill_rank',
			'minimum_merit' => .1,
			'filter_staff' => false,
			'fit' => false
		]
	],
	'hours_played_vs_objectives' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Hours',
			'y' => 'Objectives',
			'title' => 'Hours Played vs Objectives',
			'x_data' => 'hours_played',
			'y_data' => '(monuments+wools+cores+flags)',
			'minimum_merit' => .5,
			'filter_staff' => false,
			'fit' => true
		]
	],
	'kd_vs_rank' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'KD',
			'y' => 'Rank',
			'title' => 'KD vs Rank',
			'x_data' => 'kd',
			'y_data' => 'kill_rank',
			'minimum_merit' => 1,
			'filter_staff' => false,
			'fit' => false
		]
	],
	'kd_vs_objectives' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'KD',
			'y' => 'Objectives',
			'title' => 'KD vs Objectives',
			'x_data' => 'kd',
			'y_data' => '(monuments+wools+cores+flags)',
			'minimum_merit' => 1,
			'filter_staff' => false,
			'fit' => false
		]
	],
	'average_experienced_game_length_vs_objectives' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Minutes',
			'y' => 'Objectives',
			'title' => 'Avg. Experienced Game Length vs Objectives',
			'x_data' => 'average_experienced_game_length_in_minutes',
			'y_data' => '(monuments+wools+cores+flags)',
			'minimum_merit' => 1,
			'filter_staff' => false,
			'fit' => false
		]
	],
	'average_experienced_game_length_vs_kd' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Minutes',
			'y' => 'KD',
			'title' => 'Avg. Experienced Game Length vs KD',
			'x_data' => 'average_experienced_game_length_in_minutes',
			'y_data' => 'kd',
			'minimum_merit' => 1,
			'filter_staff' => false,
			'fit' => true
		]
	],
	'percent_time_on_stratus_vs_droplets' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => '%',
			'y' => 'Droplets',
			'title' => 'Percent Time on Stratus vs Droplets',
			'x_data' => 'percent_time_spent_on_stratus',
			'y_data' => 'droplets',
			'minimum_merit' => .5,
			'filter_staff' => true,
			'fit' => true
		]
	],
	'reliability_index_vs_merit_multiplier' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Index',
			'y' => 'Merit',
			'title' => 'Reliability Index vs Merit Multiplier',
			'x_data' => 'reliability_index',
			'y_data' => 'merit_multiplier',
			'minimum_merit' => 0,
			'filter_staff' => false,
			'fit' => true
		]
	],
	'rank_vs_hours_until_one_million_droplets' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Rank',
			'y' => 'Hours',
			'title' => 'Rank vs Hours Until 1M Droplets',
			'x_data' => 'kill_rank',
			'y_data' => 'hours_until_one_million_droplets',
			'minimum_merit' => 1,
			'filter_staff' => true,
			'fit' => true
		]
	],
	'monuments_vs_cores' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Monuments',
			'y' => 'Cores',
			'title' => 'Monuments vs Cores',
			'x_data' => 'monuments',
			'y_data' => 'cores',
			'minimum_merit' => 1,
			'filter_staff' => false,
			'fit' => true
		]
	],
	'wools_vs_flags' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Wools',
			'y' => 'Flags',
			'title' => 'Wools vs Flags',
			'x_data' => 'wools',
			'y_data' => 'flags',
			'minimum_merit' => 1,
			'filter_staff' => false,
			'fit' => true
		]
	],
	'captures_vs_breaks' => [
		'function' => 'scatterCorrelate',
		'payload' => [
			'x' => 'Wools + Flags',
			'y' => 'Monuments + Cores',
			'title' => 'Captures vs Breaks',
			'x_data' => '(wools+flags)',
			'y_data' => '(monuments+cores)',
			'minimum_merit' => 1,
			'filter_staff' => false,
			'fit' => true
		]
	]
];

if(isset($_GET['g']) && !empty($_GET['g']) && array_key_exists($_GET['g'], $graphs)) {
	$ch = new ConnectionHandler();
	$cache = new CacheHandler();
	$cacheFile = 'graphs/'.strtolower($_GET['g']).'.tmp';
	if($cache->checkCacheParams($cacheFile, 604800)) {
		include($cache->getCacheDir().'/'.$cacheFile);
	} else {
		ob_start();
		call_user_func($graphs[$_GET['g']]['function'], $graphs[$_GET['g']]['payload']);
		$output = ob_get_contents();
		ob_end_flush();
		if(!$cache->writeCache($cacheFile, $output)) {
			error('error');
			echo 'Error writing cache!';
		}
	}
} else {
	error('default');
}

function error($type) {
	echo file_get_contents('images/graph_'.$type.'.png');
}

function scatterCorrelate($payload) {
	global $ch, $width, $height;
	$result = $ch->getTwoPointCorrelation($payload['x_data'], $payload['y_data'], $payload['minimum_merit'], $payload['filter_staff']);
	if($result===false) {
		error('error');
	} elseif(isset($result[0]) && !$result[0]) {
		error('empty');
	} else {
		$data = new pChart\pData();

		$data->addPoints($result['x'], 'x');
		$data->addPoints($result['y'], 'y');

		$data->setSerieOnAxis('y', 1);
		$data->setAxisName(0, $payload['x']);
		$data->setAxisName(1, $payload['y']);

		$data->setAxisXY(0, AXIS_X);
		$data->setAxisXY(1, AXIS_Y);
		$data->setAxisPosition(0, AXIS_POSITION_BOTTOM);
		$data->setAxisPosition(1, AXIS_POSITION_LEFT);

		$data->setScatterSerie('x', 'y', 0);
		$data->setScatterSerieColor(0, new pChart\pColor(51,122,183));

		$output = new pChart\pDraw($width, $height);
		$output->myData = $data;
		$output->Antialias = false;
		$output->setFontProperties([
			'FontName' => $_SERVER['DOCUMENT_ROOT'].'/fonts/NotoSans-Regular.ttf',
			'FontSize' => 12
		]);
		$output->setGraphArea(54+7*strlen((string)$result['y_max']), 50, $width-50, $height-54-7*strlen((string)$result['x_max']));
		$output->drawText($width/2, 35, $payload['title'], [
			'FontSize' => 20,
			'Align' => TEXT_ALIGN_BOTTOMMIDDLE
		]);

		$points = new pChart\pScatter($output, $data);
		$points->drawScatterScale([
			'XMargin' => 20,
			'YMargin' => 20,
			'Floating' => false,
			'GridR' => 176,
			'GridG' => 176,
			'GridB' => 176,
			'DrawSubTicks' => false,
			'CycleBackground' => true,
			'Mode' => SCALE_MODE_MANUAL,
			'ManualScale' => [
				0 => [
					'Min' => $result['x_min'],
					'Max' => $result['x_max']
				],
				1 => [
					'Min' => $result['y_min'],
					'Max' => $result['y_max']
				]
			]
		]);
		$output->Antialias = true;
		$points->drawScatterPlotChart();
		
		if($payload['fit']) {
			$regression = findLineByLeastSquares($result['x'], $result['y']);
			$output->drawText($width-80, 50, 'y='.$regression['m'].'x'.($regression['b']==0 ? '' : ($regression['b']>0 ? '+' : '').$regression['b']), [
				'FontSize' => 10,
				'Align' => TEXT_ALIGN_TOPRIGHT
			]);
			$correlation = findCorrelationCoefficient($result['x'], $result['y']);
			$output->drawText($width-80, 65, 'R^2='.sigFig($correlation*$correlation, 5), [
				'FontSize' => 8,
				'Align' => TEXT_ALIGN_TOPRIGHT
			]);
			$points->drawScatterBestFit();
		}

		$output->stroke();
	}
}

function findLineByLeastSquares($x_values, $y_values) {
	$sum_x = 0;
	$sum_y = 0;
	$sum_xy = 0;
	$sum_xx = 0;
	$count = 0;
	$x = 0;
	$y = 0;
	$values = count($x_values);

	if($values===0) {
		return [0,0];
	}

	for($i=0; $i<$values; $i++) {
		$x = $x_values[$i];
		$y = $y_values[$i];
		$sum_x += $x;
		$sum_y += $y;
		$sum_xx += $x*$x;
		$sum_xy += $x*$y;
		$count++;
	}

	$m = ($count*$sum_xy - $sum_x*$sum_y) / ($count*$sum_xx - $sum_x*$sum_x);
	$b = ($sum_y/$count) - ($m*$sum_x)/$count;
	
	return ['m' => sigFig($m, 3), 'b' => sigFig($b, 3)];
}

function findCorrelationCoefficient($x_values, $y_values) {
    $n = count($x_values);
    $keys = array_keys(array_intersect_key($x_values, $y_values));

    $sum_x = 0;
    $sum_y = 0;
    $x_sum_sq = 0;
    $y_sum_sq = 0;
    $prod_sum = 0;
    foreach($keys as $k) {
        $sum_x += $x_values[$k];
        $sum_y += $y_values[$k];
        $x_sum_sq += pow($x_values[$k], 2);
        $y_sum_sq += pow($y_values[$k], 2);
        $prod_sum += $x_values[$k] * $y_values[$k];
    }

    $numerator = $prod_sum - ($sum_x * $sum_y / $n);
    $denominator = sqrt( ($x_sum_sq - pow($sum_x, 2) / $n) * ($y_sum_sq - pow($sum_y, 2) / $n) );

    return $denominator==0 ? 0 : sigFig($numerator/$denominator, 5);
}

function sigFig($value, $digits) {
	if($value==0) {
		$decimalPlaces = $digits - 1;
	} elseif($value<0) {
		$decimalPlaces = $digits - floor(log10($value * -1)) - 1;
	} else {
		$decimalPlaces = $digits - floor(log10($value)) - 1;
	}

	return round($value, $decimalPlaces);
}
?>