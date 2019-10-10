<?php
class ConnectionHandler {
	protected static $connection;
	
	public function __construct() {
		$connection = $this->connect();
	}
	
	private function connect() {
		try {
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/_stats/config.ini')) {
				$GLOBALS['config'] = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/../cgi-bin/_stats//config.ini');
			} else {
				throw new Exception('Invalid configuration file!');
			}
		} catch(Exception $e) {
			http_response_code(500);
		}
		mysqli_report(MYSQLI_REPORT_STRICT);
		if(!isset(self::$connection)) {
			try {
				self::$connection = @new mysqli($GLOBALS['config']['dbhost'], $GLOBALS['config']['dbuser'], $GLOBALS['config']['dbpass'], $GLOBALS['config']['dbname']);
			} catch (Exception $e) {
				header('Location: /500');
				die();
			}
		}
		return self::$connection;
	}
	
	private function query($query) {
		try {
			$result = self::$connection->query($query);
			return $result;
		} catch (Exception $e) {
			return 0;
		}
	}
	
	private function select($query) {
		$out = $this->query($query);
		if($out == false) {
			return false;
		}
		$result = array();
		while($row = $out->fetch_assoc()) {
			$result[] = $row;
		}
		return $result;
	}
	
	private function isArray($rs) {
		return is_array($rs)||is_object($rs) ? (count($rs)>0) : false;
	}
	
	private function selectResultSet($query) {
		$rs = $this->select($query);
		return $this->isArray($rs)===false ? [false] : $rs;
	}
	
	private function countRows($query) {
		$out = $this->query($query);
		if($out === false) {
			return false;
		}
		return $out->num_rows;
	}
	
	private function escape($value, $int = 0) {
		return $int ? (int)(self::$connection->real_escape_string($value)) : self::$connection->real_escape_string($value);
	}
	
	public function closeConn() {
		self::$connection->close();
		return;
	}
	
	public function curl($url, $post = false) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_USERAGENT, 'SethNet/1.0');
		$html = curl_exec($ch);
		curl_close($ch);
		return $html;
	}
	
	public function getTopTenPlayers($stat, $largest = true, $minimum_merit = 0, $filter_staff = false) {
		return $this->selectResultSet('SELECT uuid,username,'.$this->escape($stat).',staff FROM players WHERE merit_multiplier>'.$this->escape($minimum_merit).($filter_staff ? ' AND staff=0' : '').' ORDER BY '.$this->escape($stat).' '.($largest ? 'DESC' : 'ASC').' LIMIT 10');
	}
	
	public function getPlayers($limit = 0) {
		return $this->selectResultSet('SELECT uuid,username FROM players'.($limit>0 ? ' LIMIT '.$this->escape($limit, 1) : ''));
	}
	
	public function getPlayer($lookup) {
		return $this->selectResultSet('SELECT * FROM players WHERE uuid="'.$this->escape($lookup).'" OR username="'.$this->escape($lookup).'" LIMIT 1');
	}
	
	public function getRandomPlayer($minimum_merit = 0) {
		return $this->selectResultSet('SELECT * FROM players WHERE merit_multiplier>'.$this->escape($minimum_merit).' ORDER BY RAND() LIMIT 1');
	}
	
	public function getTwoPointCorrelation($stat1, $stat2, $minimum_merit = .1, $filter_staff = false) {
		$rs = $this->selectResultSet('SELECT '.$this->escape($stat1).','.$this->escape($stat2).' FROM players WHERE merit_multiplier>'.$this->escape($minimum_merit).($filter_staff ? ' AND staff=0' : '').' ORDER BY reliability_index DESC LIMIT 25000');
		if($rs===false) {
			return false;
		}
		if(count($rs)==0 || !$rs[0]) {
			return [false];
		}
		$data1 = array_column($rs, $stat1);
		$data2 = array_column($rs, $stat2);
		return [
			'x' => $data1,
			'y' => $data2,
			'x_min' => min($data1),
			'x_max' => max($data1),
			'y_min' => min($data2),
			'y_max' => max($data2)
		];
	}
	
	public function countPlayers($minimum_merit = 0) {
		return $this->selectResultSet('SELECT COUNT(uuid) as count FROM players WHERE merit_multiplier>'.$this->escape($minimum_merit))[0]['count'];
	}
	
	public function getStatTotal($stat, $name) {
		return $this->selectResultSet('SELECT SUM('.$this->escape($stat).') AS `'.$this->escape($name).'` FROM players LIMIT 50000')[0];
	}
	
	public function getTotals() {
		return $this->selectResultSet('SELECT SUM(kills) AS `Kills`, SUM(deaths) AS `Deaths`, SUM(droplets) AS `Droplets`, SUM(droplets*-(staff-1)) AS `Droplets (Non-Staff)`, SUM(monuments) AS `Monuments`, SUM(wools) AS `Wools`, SUM(cores) AS `Cores`, SUM(flags) AS `Flags`, SUM(wools+monuments+cores+flags) AS `All Objectives`, SUM(staff) AS `Staff Members`, SUM(donor) as `Donors`, SUM(tournament_winner) AS `Tournament Winners`, SUM(hours_played) AS `Person-Hours`, SUM(teams_joined) AS `Teams Joined`, SUM(average_kills_per_hour) AS `Average Kills Per Hour`, SUM(average_deaths_per_hour) AS `Average Deaths Per Hour`, SUM(average_wools_per_hour+average_monuments_per_hour+average_cores_per_hour+average_flags_per_hour) AS `Average Objectives Per Hour`, SUM(average_kills_per_game) AS `Average Kills Per Game` FROM players LIMIT 50000')[0];
	}
	
	public function getAverages() {
		return $this->selectResultSet('SELECT AVG(kills) AS `Kills`, AVG(deaths) AS `Deaths`, AVG(friends) AS `Friends`, AVG(droplets) AS `Droplets`, AVG(droplets*-(staff-1)) AS `Droplets (Non-Staff)`, AVG(monuments) AS `Monuments`, AVG(wools) AS `Wools`, AVG(cores) AS `Cores`, AVG(flags) AS `Flags`, AVG(wools+monuments+cores+flags) AS `All Objectives`, AVG(hours_played) AS `Person-Hours`, AVG(teams_joined) AS `Teams Joined`, AVG(average_kills_per_hour) AS `Average Kills Per Hour`, AVG(average_deaths_per_hour) AS `Average Deaths Per Hour`, AVG(average_wools_per_hour+average_monuments_per_hour+average_cores_per_hour+average_flags_per_hour) AS `Average Objectives Per Hour`, AVG(average_kills_per_game) AS `Average Kills Per Game`, AVG(average_experienced_game_length_in_minutes) AS `Game Length (Minutes)`, AVG(percent_time_spent_on_stratus) AS `% of Time on Stratus` FROM players WHERE merit_multiplier>1 LIMIT 50000')[0];
	}
}
?>