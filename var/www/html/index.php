<?php
session_name('stats');
session_start();

require '../cgi-bin/_stats/ConnectionHandler.class.php';
require '../cgi-bin/_stats/CacheHandler.class.php';
$loadMessages = ['Rendering', 'Loading', 'Calculating', 'Optimizing', 'Evaluating', 'Quantifying', 'Analyzing', 'Ranking', 'Indexing'];

// See, I was gonna make it like 10 lines and now it's somehow +1200 :l

$request = explode('/', isset($_GET['request']) ? $_GET['request'] : '');
switch($request[0]) {
	case 'ajax':
		array_shift($request);
		ajax();
		break;
	case '401':
		error(401);
		break;
	case '403':
		error(403);
		break;
	case '404':
		error(404);
		break;
	case '500':
		error(500);
		break;
	case '501':
		error(501);
		break;
	case '':
		header('Location: /about');
		break;
	case 'random':
		switch(isset($request[1]) ? $request[1] : (mt_rand(0,1) ? 'player' : 'match')) {
			case 'player':
			case 'players':
				if(!isset($ch)) {
					$ch = new ConnectionHandler();
				}
				header('Location: /'.$ch->getRandomPlayer(.8)[0]['username']);
				break;
			case 'match':
			case 'matches':
				header('Location: /501');
				break;
		}
		break;
	case 'autocomplete':
		if(isset($request[1]) && $_SERVER['REQUEST_METHOD']==='POST') {
			if(!isset($ch)) {
				$ch = new ConnectionHandler();
			}
			header('Content-Type: application/json');
			echo $ch->curl('https://stratus.network/autocomplete/'.$request[1], true);
			break;
		}
	default:
		home();
		break;
}
exit();

function head($title = '', $desc = 'Stratus Network - Unofficial PvP Statistics') {
	$isMobile = isset($_SERVER['HTTP_USER_AGENT']) ? (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$_SERVER['HTTP_USER_AGENT'])||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|username)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4))) : false;
	$active = function($links) {
		global $request;
		if(isset($request[0]) && in_array($request[0], $links)) {
			return 'active ';
		}
		return '';
	};
	?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<base href="<?php echo isset($_SERVER["HTTPS"]) ? 'https' : 'http' ?>://<?php echo $_SERVER['HTTP_HOST']; ?>/">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
		<meta content="Stratus Network - Minecraft PvP" property="og:title">
		<meta content="website" property="og:type">
		<meta content="https://stratus.network" property="og:url">
		<meta content="https://stratus.network/images/stratus.png" property="og:image">
		<meta name="description" content="<?php echo $desc; ?>">
		<meta name="keywords" content="Stratus,Network,Statistics,Minecraft,PVP,Siceth">
		<meta name="author" content="Seth Phillips">
		<title>Stratus Statistics<?php echo empty($title) ? '' : ' &raquo; '.str_replace('/', '&raquo;', $title); ?></title>
		<link rel="stylesheet" media="screen" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" />
		<link rel="stylesheet" media="screen" href="/css/application.css" />
		<link rel="stylesheet" media="screen" href="//fonts.googleapis.com/css?family=Noto+Sans:700,400,300" />
		<link rel="stylesheet" media="screen" href="//use.fontawesome.com/releases/v5.1.0/css/all.css" />
		<link rel="stylesheet" media="screen" id="styleswap" <?php echo isset($_COOKIE['theme']) && $_COOKIE['theme']=='dark' ? ' href="/css/dark-theme.css"' : 'href="/css/default-theme.css" disabled="disabled"'; ?>/>
		<link rel="apple-touch-icon" sizes="57x57" href="/images/icons/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/images/icons/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/images/icons/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/images/icons/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/images/icons/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/images/icons/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/images/icons/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/images/icons/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/images/icons/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="/images/icons/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/images/icons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="/images/icons/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/images/icons/favicon-16x16.png">
		<link rel="manifest" href="/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/images/icons/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.0/bootstrap3-typeahead.min.js" type="text/javascript"></script>
		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
		<link href="/css/zoom.css" rel="stylesheet">
		<script src="/js/zoom.js"></script>
		<script src="/js/transition.js"></script>
		<style type="text/css">
		#loading-wrapper {
			height: <?php echo $isMobile ? '128' : '640'?>px;
			width: <?php echo $isMobile ? '128' : '640'?>px;
			background: url(/images/loader<?php echo $isMobile ? '-mobile' : ''?>.png) 0 0;
		}
		@keyframes load-cycle {
			0% { background-position: 0px 0px; }
			100% { background-position: -<?php echo $isMobile ? '640' : '3200'?>px -0px; }
		}
		@keyframes load-end {
			0% { background-position: 0px 0px; }
			100% { background-position: -<?php echo $isMobile ? '4480' : '22400'?>px -0px; }
		}
		#loading-wrapper > span {
			top: <?php echo $isMobile ? '64' : '320'?>px;
			font-size: <?php echo $isMobile ? '1' : '3'?>em;
		}
		</style>
	</head>
	<body>
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<button class="navbar-toggle collapsed" data-target="#top-navbar-collapse" data-toggle="collapse" id="top-navbar-collapse-button">
						<span class="fa fa-bars"></span>
					</button>
					<a class="navbar-brand" href="/">
						<img class="pull-left" height="34" src="/images/stratus.png"> Stratus Statistics
					</a>
				</div>
				<div class="navbar-collapse collapse" id="top-navbar-collapse">
					<ul class="nav navbar-nav" style="margin-right: 0;">
						<li class="<?php echo $active(['about']); ?>dropdown">
							<a href="/about">
								<i class="fa fa-info-circle"></i> About
							</a>
						</li>
						<li class="<?php echo $active(['leaderboards','graphs']); ?>dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="fa fa-globe"></i> Global Stats
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li>
									<a href="/leaderboards">
										<i class="fa fa-list-ol"></i> Leaderboards
									</a>
								</li>
								<li>
									<a href="/graphs">
										<i class="fa fa-chart-bar"></i> Graphs
									</a>
								</li>
								<li>
									<a href="/combined">
										<i class="fa fa-universal-access"></i> Combined
									</a>
								</li>
							</ul>
						</li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="fa fa-users"></i> Players
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li>
									<a href="//<?php echo $_SERVER['HTTP_HOST']; ?>/random/player">
										<i class="fa fa-dice"></i> Random Player
									</a>
								</li>
								<form class="menu-search" method="POST">
									<input class="input-sm form-control typeahead" id="player-search" name="search" placeholder="Enter a username" type="text" autocomplete="off">
								</form>
							</ul>
						</li>
						<!--
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="fa fa-gamepad"></i> Matches
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li>
									<a href="//<?php echo $_SERVER['HTTP_HOST']; ?>/random/match">
										<i class="fa fa-dice"></i> Random Match
									</a>
								</li>
								<form class="menu-search">
									<input class="input-sm form-control typeahead" id="match-search" name="search" placeholder="Enter a match UUID" type="text" autocomplete="off">
								</form>
							</ul>
						</li>
						-->
						<li class="<?php echo $active(['predictor']); ?>dropdown">
							<a href="/predictor">
								<i class="fa fa-percent"></i> Win Predictor
							</a>
						</li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="fa fa-link"></i> Links
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li>
									<a href="https://stratus.network/" target="_blank">
										<i class="fa fa-cloud"></i> Stratus Network
									</a>
								</li>
								<li>
									<a href="https://mcresourcepile.github.io/" target="_blank">
										<i class="fa fa-map"></i> ResourcePile
									</a>
								</li>
								<li>
									<a href="https://graph.unixfox.eu/" target="_blank">
										<i class="fa fa-desktop"></i> Grafana Monitor
									</a>
								</li>
								<li>
									<a href="https://github.com/Siceth/Stratus-Stat-Utilities" target="_blank">
										<i class="fa fa-terminal"></i> Stat Utils
									</a>
								</li>
							</ul>
						</li>
					</ul>
					<hr class="visible-xs visible-sm" style="margin: 4px; border-color: #e7e7e7;">
					<ul class="nav navbar-nav pull-right">
						<li>
							<a data-toggle="dropdown" href="#" onclick="toggleTheme()">
								<i class="fa fa-adjust"></i>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container">
			<div class="flash" style="margin-top: 20px;"></div>
			<div id="content-wrapper">
				<section id="content-area">
	<?php
}

function foot($include_loader = true) {
	global $loadMessages, $request;
	?>
				</section>
			</div>
			<div id="content-loader">
				<div class="load" id="loading-wrapper">
					<span><?php echo $loadMessages[array_rand($loadMessages)]; ?>...</span>
				</div>
			</div>
			<hr>
			<footer>
				<div class="row">
					<div class="center">
						<p>
							<span style="font-weight:bold">Made with <i class="fa fa-heart"></i> by <a target="_blank" href="https://seth-phillips.com/">Seth Phillips</a></span>
							<br>
							<small>This service is not affiliated with or endorsed by Stratus Network, LLC.</small>
						</p>
					</div>
				</div>
			</footer>
		</div>
		<span id="top-link-block" class="hidden">
			<a href="#top" class="well well-sm" onclick="$('html,body').animate({scrollTop:0},'slow');return false;">
				<i class="fa fa-angle-up"></i>
			</a>
		</span>
		<script type="text/javascript">
		$(document).ready(function() {
	<?php
	if($include_loader && !empty($request)) {
		echo "\t\t".'loadPage("/'.implode('/', $request).'");';
	}
	?>
			$('.typeahead').typeahead({
				source: function(query, process) {
					$.ajax({
						type: "POST",
						headers: {          
							Accept: "*/*",
							"Content-Type": "text/plain; charset=utf-8",
							"Content-Type": "application/x-www-form-urlencoded"
						},
						url: "/autocomplete/" + JSON.stringify(query).replace(/[^a-zA-Z0-9-_]+/g, ''),
						contentType: "application/json; charset=utf-8",
						dataType: "json",
						success: function(response) {
							var users = [];
							if(response) {
								$(response).each(function(index, val) {
									users.push(val);
								});
								process(users);
							}
						}
					});
				}
			});
			var name;
			$("#player-search").on("change", function(event) {
				name = event.target.value;
				setTimeout(function redirect() {
					window.location = "/" + name
				}, 200);
			});
			$("[rel=tooltip]").tooltip();
		});
		
		function refixTopButton() {
			if(($(window).height() + 100) < $(document).height()) {
				$('#top-link-block').removeClass('hidden').affix({
					offset: {top:100}
				});
			}
		};
		
		function toggleTheme() {
			if($("#styleswap").attr("disabled")=="disabled") {
				$("#styleswap").removeAttr("disabled");
				$("#styleswap").attr("href", "/css/dark-theme.css");
				document.cookie = "theme=dark; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
			} else {
				$("#styleswap").attr("disabled", "disabled");
				$("#styleswap").attr("href", "/css/default-theme.css");
				document.cookie = "theme=default; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
			}
		}
		
		$(".dropdown-menu a").click(function() {
			$(this).closest(".dropdown-menu").prev().dropdown("toggle");
		});
		
		$("body").on("click", "a:not([href*=#],[href^=http],[href^='//'])", function(event) {
			event.preventDefault();
			var that = $(this);
			var href = that.attr("href")=="/" ? "/about" : that.attr("href");
			var quickLoads = ["/about"];
			var proxyLoads = ["/play", "/teams"];
			if(new RegExp(proxyLoads.join("|")).test(href)) {
				window.location = "http://stratus.network" + href;
			} else {
				if(href!=window.location.pathname || href.substring(1,7)=="random") {
					if(history.pushState) {
						history.pushState(null, null, href);
					}
					$("li.active.dropdown").removeClass("active");
					that.closest("li.dropdown").addClass("active");
					$("#content-area").fadeOut('slow', function() {
						if($.inArray(href, quickLoads)===-1) {
							$("#content-loader").fadeIn('slow', function() {
								$('#loading-wrapper > span').fadeIn('fast');
								loadPage(href, true);
							});
						} else {
							if($("#content-loader").is(":visible")) {
								$("#content-loader").fadeOut('fast');
							}
							loadPage(href, false);
						}
					});
				}
			}
			return false;
		});
		
		$.fn.imagesLoaded = function() {
			if(typeof ignoreImages !== 'undefined' && ignoreImages) {
				ignoreImages = false;
				return $.Deferred().resolve().promise();
			}
			var $imgs = this.find("img[src!='']");
			if(!$imgs.length) {
				return $.Deferred().resolve().promise();
			}
			var dfds = [];  
			$imgs.each(function() {
				var dfd = $.Deferred();
				dfds.push(dfd);
				var img = new Image();
				img.onload = function(){dfd.resolve();}
				img.onerror = function(){dfd.resolve();}
				img.src = this.src;
			});
			return $.when.apply($,dfds);
		}
		
		function loadPage(request, fade = false) {
			$.ajax({
				url: "/ajax" + request,
				dataType: "html",
				success: function(data) {
					$('#content-area').html(data).imagesLoaded().then(function() {
						if(fade) {
							$('#loading-wrapper').attr("class", "load-complete");
							$('#loading-wrapper > span').fadeOut(1200, function() {
								loadMessages = JSON.parse("<?php echo str_replace('"', '\"', json_encode($loadMessages)); ?>");
								$(this).text(loadMessages[~~(loadMessages.length * Math.random())] + "...");
							});
							setTimeout(function() {
								$("#content-loader").fadeOut('slow', function() {
									$('#content-area').fadeIn('slow');
									$('#loading-wrapper').attr("class", "load");
								});
							}, 1500);
						} else if(!$("#content-area").is(":visible")) {
							$("#content-area").fadeIn('slow');
						}
						refixTopButton();
						return true;
					}).fail(function() {
						// TODO: Images failed to load
					});
				}
			}).fail(function() {
				// TODO: Page failed to load
			});
			return false;
		}
		</script>
	</body>
</html>
	<?php
}

function home() {
	global $ch, $request;
	if(!isset($ch)) {
		$ch = new ConnectionHandler();
	}
	if(isset($request[0])) {
		$player = $ch->getPlayer($request[0]);
		$exists = $player!==false && $player!==[false];
	}
	head();
	?>
				<div class="fa fa-2x fa-spinner fa-spin"></div> <i style="font-size: 2em;"> &nbsp; <?php echo $exists ? 'Downloading profile from Stratus' : 'Initializing'; ?>...</i>
				<script type="text/javascript">var ignoreImages = true;</script>
	<?php
	foot();
}

function error($status, $include_extremes = true) {
	$errors = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		103 => 'Early Hints',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Switch Prozy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		412 => 'Length Required',
		413 => 'Precondition Failed',
		414 => 'Payload Too Large',
		415 => 'URI Too Long',
		416 => 'Unsupported Media Type',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		421 => 'Misdirected Request',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		451 => 'Unavailable For Legal Reasons',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		510 => 'Not Extended',
		511 => 'Network Authentication Required'
	];
	if($include_extremes) {
		http_response_code($status);
		head();
	}
	?>
				<div class="jumbotron error">
					<div>
						<div><?php echo $status; ?></div>
						<div><?php echo isset($errors[$status]) ? $errors[$status] : 'Unknown Error'; ?></div>
					</div>
				</div>
	<?php
	if($include_extremes) {
		foot(false);
	}
}

function ajax() {
	header('Content-Type: text/plain');
	global $request;
	switch(isset($request[0]) ? $request[0] : '') {
		case '':
		case 'about':
			about();
			break;
		case 'leaderboards':
			$cache = new CacheHandler();
			$cacheFile = 'leaderboards.tmp';
			if($cache->checkCacheParams($cacheFile, 1800)) {
				include($cache->getCacheDir().'/'.$cacheFile);
			} else {
				ob_start();
				leaderboards();
				$output = ob_get_contents();
				ob_end_flush();
				if(!$cache->writeCache($cacheFile, $output)) {
					error(500, false);
				}
			}
			break;
		case 'combined':
			$cache = new CacheHandler();
			$cacheFile = 'combined.tmp';
			if($cache->checkCacheParams($cacheFile, 120)) {
				include($cache->getCacheDir().'/'.$cacheFile);
			} else {
				ob_start();
				combined();
				$output = ob_get_contents();
				ob_end_flush();
				if(!$cache->writeCache($cacheFile, $output)) {
					error(500, false);
				}
			}
			break;
		case 'graphs':
			graphs();
			break;
		case 'predictor':
			predictor();
			break;
		default:
			if(!isset($ch)) {
				$ch = new ConnectionHandler();
			}
			$result = $ch->getPlayer($request[0]);
			if($result===false) {
				error(500, false);
			} elseif(isset($result[0]) && !$result[0]) {
				error(404, false);
			} else {
				$result = $result[0];
				try {
					$dom = new DOMDocument();
					@$dom->loadHTML($ch->curl('https://'.$_SERVER['SERVER_NAME'].'/stratus/'.$result['username'].'?lazy-renew'));
					$scrubTags = function(&$dom, $tagName) {
						$meta = $dom->getElementsByTagName($tagName);
						while($meta->length > 0) {
							$p = $meta->item(0);
							$p->parentNode->removeChild($p);
						}
					};
					$scrubTags($dom, 'meta');
					$scrubTags($dom, 'style');
					$scrubTags($dom, 'script');
					
					$userTabs = $dom->getElementById('user-tabs');
					if(is_null($userTabs)) {
						throw new Exception('Unable to get page data from Stratus!');
					}
						$newTab = $userTabs->appendChild($dom->createElement('li'));
						$newTab->setAttribute('class', '');
							$newTabAnchor = $newTab->appendChild($dom->createElement('a'));
							$newTabAnchor->setAttribute('data-toggle', 'tab');
							$newTabAnchor->setAttribute('href', '#statistics');
							$newTabAnchor->setAttribute('aria-expanded', 'false');
							$newTabAnchor->setAttribute('style', 'color: #e11f26 !important;');
							$newTabAnchor->appendChild($dom->createTextNode('Extra Statistics'));
					
					$finder = new DomXPath($dom);
					$tabContent = $finder->query('//div[contains(@class, "tab-content")]')->item(0);
						$newTabContent = $tabContent->appendChild($dom->createElement('div'));
						$newTabContent->setAttribute('class', 'tab-pane');
						$newTabContent->setAttribute('id', 'statistics');
							$newTabContentRow = $newTabContent->appendChild($dom->createElement('div'));
							$newTabContentRow->setAttribute('class', 'row');
								$newTabContentRowPre = $newTabContentRow->appendChild($dom->createElement('pre'));
								$newTabContentRowPre->appendChild($dom->createTextNode('Hey! This is still a huge work-in-progress. Below is what I track, but I\'ll make it pretty later.'));
								//$newTabContentRowPre->appendChild($dom->createTextNode(json_encode($result)));
								
								foreach($result as $statistic=>$value) {
									$newTabContentRowCol = $newTabContentRow->appendChild($dom->createElement('div'));
									$newTabContentRowCol->setAttribute('class', 'col-md-3 col-sm-6');
									$newTabContentRowColH4 = $newTabContentRowCol->appendChild($dom->createElement('h4'));
									$newTabContentRowColH4->setAttribute('class', 'strong');
									$newTabContentRowColH4->setAttribute('data-placement', 'top');
									$newTabContentRowColH4->setAttribute('rel', 'tooltip');
									$newTabContentRowColH4->appendChild($dom->createTextNode($value));
									$newTabContentRowColH4small = $newTabContentRowColH4->appendChild($dom->createElement('small'));
									$newTabContentRowColH4small->setAttribute('class', 'strong');
									$newTabContentRowColH4small->appendChild($dom->createTextNode(' '.str_replace('_', ' ', $statistic)));
								}
					
					foreach($dom->getElementsByTagName('section') as $section) {
						echo $dom->saveXML($section);
					}
					?>
					<script type="text/javascript">
						$("img[data-cfsrc]").each(function() {
							$(this).attr("src", $(this).attr("data-cfsrc"));
							$(this).attr("style", "");
							$(this).removeAttr("data-cfsrc");
						});
					</script>
					<?php
				} catch(Exception $e) {
					echo 'There was a problem fetching data!';
					print_r($e);
				}
			}
			break;
	}
}

function about() {
	$backgrounds = array_diff(scandir('./images/backgrounds'), array('.', '..'));
	?>
				<div class="page-header">
					<h2>About</h2>
				</div>
				<br>
				<div class='posts'>
					<div class='forum-post'>
						<div class='converted post-content'>
							<div class="breathing-wrapper">
								<img class="breathing" src="/images/backgrounds/<?php echo $backgrounds[mt_rand(2, count($backgrounds)+1)]; ?>" />
							</div>
							<hr>
							<h3>
								Greetings!
							</h3>
							<p>
								For those of you that are unfamiliar with the <a href="https://stratus.network/" target="_blank">Stratus Network</a>, it's a Minecraft server for casual &amp; competitive PvP gameplay.  Part of its unique gameplay includes player rankings through a powerful set of public statistics.  Frankly, it's the last decent server out there with a thriving and diverse community &mdash; that's something absolutely rare that I wish to see continue for decades.
								<br><br>
								I built this service to calculate and store some advanced statistics and experimental analytics (<i>because let's face it, predicting the future is fun</i>).  It all started when I had the idea to scrape a player's profile page to get their stats automatically.  Now, there's win predictors, in-game bot integration, and finally bulk data stores.  <b>Voilà!</b>
								<br><br>
								For those of you that <i>are</i> familiar with the network, this design should look pretty similar to you.  I did my best to mirror the functionality of the original website, but on an entirely different backend and framework.
								<br><br>
								Happy exploring!
								<br><br>
								~ <a href="/Siceth"><img width="20" height="20" src="https://api.ashcon.app/mojang/v1/avatar/1e4cbacc-0fb5-445a-a99d-7c76f9d0b564/20" alt="Siceth" title="Siceth" class="avatar"></a> <a href="/Siceth" style="color:#08c;">Siceth</a>
							</p>
						</div>
					</div>
				</div>
	<?php
}

function leaderboards() {
	if(!isset($ch)) {
		$ch = new ConnectionHandler();
	}
	$boards = [
		'&#x2694;&#xFE0F; Classic Stats &#x2694;&#xFE0F;' => [
			'Top Kills' => [
				'stat' => 'kills',
				'col_title' => 'Kills',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => ''
			],
			'Top Deaths' => [
				'stat' => 'deaths',
				'col_title' => 'Deaths',
				'largest' => true,
				'minimum_merit' => .1,
				'filter_staff' => false,
				'note' => ''
			],
			'Top Playing Time' => [
				'stat' => 'hours_played',
				'col_title' => 'Hours',
				'largest' => true,
				'minimum_merit' => .1,
				'filter_staff' => false,
				'note' => ''
			],
			'Top KD' => [
				'stat' => 'kd',
				'col_title' => 'KD',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			]
		],
		'&#x1F3F4;&#xFE0F; Objective Stats &#x1F3F4;&#xFE0F;' => [
			'Most Wools Captured' => [
				'stat' => 'wools',
				'col_title' => 'Wools',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => ''
			],
			'Most Flags Captured' => [
				'stat' => 'flags',
				'col_title' => 'Flags',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => ''
			],
			'Most Monuments Destroyed' => [
				'stat' => 'monuments',
				'col_title' => 'Monuments',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => ''
			],
			'Most Cores Leaked' => [
				'stat' => 'cores',
				'col_title' => 'Cores',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => ''
			],
			'Most Objectives' => [
				'stat' => '(monuments+wools+cores+flags)',
				'col_title' => 'Objectives',
				'largest' => true,
				'minimum_merit' => .1,
				'filter_staff' => false,
				'note' => ''
			],
			'Most Team Joins' => [
				'stat' => 'teams_joined',
				'col_title' => 'Teams',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => ''
			],
		],
		'&#x1F3C6;&#xFE0F; Cosmetic Stats &#x1F3C6;&#xFE0F;' => [
			'Most Friends' => [
				'stat' => 'friends',
				'col_title' => 'Friends',
				'largest' => true,
				'minimum_merit' => .1,
				'filter_staff' => false,
				'note' => ''
			],
			'Most Trophies' => [
				'stat' => 'trophies',
				'col_title' => 'Trophies',
				'largest' => true,
				'minimum_merit' => .1,
				'filter_staff' => false,
				'note' => ''
			],
			'Most Droplets' => [
				'stat' => 'droplets',
				'col_title' => 'Droplets',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => ''
			],
			'Most Droplets (No Staff)' => [
				'stat' => 'droplets',
				'col_title' => 'Droplets',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => true,
				'note' => 'filtered'
			],
			'Most Ranks' => [
				'stat' => 'ranks',
				'col_title' => 'Ranks',
				'largest' => true,
				'minimum_merit' => .1,
				'filter_staff' => false,
				'note' => ''
			],
		],
		'&#x26A1;&#xFE0F; Speed Stats &#x26A1;&#xFE0F;' => [
			'Kills Per Hour' => [
				'stat' => 'average_kills_per_hour',
				'col_title' => 'Kills',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Deaths Per Hour' => [
				'stat' => 'average_deaths_per_hour',
				'col_title' => 'Deaths',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Wools Per Hour' => [
				'stat' => 'average_wools_per_hour',
				'col_title' => 'Wools',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Flags Per Hour' => [
				'stat' => 'average_flags_per_hour',
				'col_title' => 'Flags',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Monuments Per Hour' => [
				'stat' => 'average_monuments_per_hour',
				'col_title' => 'Monuments',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Cores Per Hour' => [
				'stat' => 'average_cores_per_hour',
				'col_title' => 'cores',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'All Objectives Per Hour' => [
				'stat' => '(average_wools_per_hour+average_flags_per_hour+average_monuments_per_hour+average_cores_per_hour)',
				'col_title' => 'Objectives',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Droplets Per Hour' => [
				'stat' => 'average_droplets_per_hour',
				'col_title' => 'Droplets',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Droplets Per Hour (No Staff)' => [
				'stat' => 'average_droplets_per_hour',
				'col_title' => 'Droplets',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => true,
				'note' => 'filtered'
			],
			'New Friends Per Hour' => [
				'stat' => 'average_new_friends_per_hour',
				'col_title' => 'Friends',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Average Kills Per Game' => [
				'stat' => 'average_kills_per_game',
				'col_title' => 'Kills',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Shortest Average Game Length' => [
				'stat' => 'average_experienced_game_length_in_minutes',
				'col_title' => 'Minutes',
				'largest' => false,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Longest Average Game Length' => [
				'stat' => 'average_experienced_game_length_in_minutes',
				'col_title' => 'Minutes',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			],
			'Percent Time On Stratus' => [
				'stat' => 'percent_time_spent_on_stratus',
				'col_title' => '%',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => 'filtered'
			]
		],
		'&#x1F4C8;&#xFE0F; Assorted Stats &#x1F4C9;&#xFE0F;' => [
			'Newest Players' => [
				'stat' => 'first_joined',
				'col_title' => 'Join Date',
				'largest' => true,
				'minimum_merit' => 0,
				'filter_staff' => false,
				'note' => ''
			],
			'Hours Until 1M Droplets' => [
				'stat' => 'hours_until_one_million_droplets',
				'col_title' => 'Hours',
				'largest' => false,
				'minimum_merit' => 1,
				'filter_staff' => true,
				'note' => 'filtered'
			],
			'Kill-Based Merit' => [
				'stat' => 'kill_based_merit',
				'col_title' => 'Merit Index',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => ''
			],
			'Time-Based Merit' => [
				'stat' => 'time_based_merit',
				'col_title' => 'Merit Index',
				'largest' => true,
				'minimum_merit' => 1,
				'filter_staff' => false,
				'note' => ''
			],
			'Overall Merit' => [
				'stat' => 'merit_multiplier',
				'col_title' => 'Merit Index',
				'largest' => true,
				'minimum_merit' => 1.19,
				'filter_staff' => false,
				'note' => ''
			],
			'Kills-Hours Per Deaths-Games' => [
				'stat' => 'khpdg',
				'col_title' => 'KH/DG',
				'largest' => true,
				'minimum_merit' => 1.19,
				'filter_staff' => false,
				'note' => 'experimental'
			]
		]
	];
	?>
				<div class="page-header playerstats">
					<h2>Leaderboards</h2>
				</div>
				<br><br>
	<?php
	foreach($boards as $title=>$boardSet) {
		?>
				<h2><?php echo $title; ?></h2>
				<br>
				<div class="row">
		<?php
		foreach($boardSet as $name=>$info) {
			?>
					<div class="col col-sm-12 col-lg-6">
						<h3><?php echo $name.(empty($info['note']) ? '' : ' <small><i>'.$info['note'].'</i></small>'); ?></h3>
						<div class="table-container">
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th scope="col"></th>
										<th scope="col">Name</th>
										<th scope="col"><?php echo $info['col_title']; ?></th>
									</tr>
								</thead>
								<tbody>
			<?php
			$result = $ch->getTopTenPlayers($info['stat'], $info['largest'], $info['minimum_merit'], $info['filter_staff']);
			if($result===false) {
				?>
									<tr>
										<td colspan="3">
											<center><span class="fa fa-exclamation-triangle"></span> <i>Error getting data!</i></center>
										</td>
									</tr>
				<?php
			} elseif(isset($result[0]) && !$result[0]) {
				?>
									<tr>
										<td colspan="3">
											<center><span class="fa fa-sticky-note"></span> Empty data set</center>
										</td>
									</tr>
				<?php
			} else {
				$rank = 0;
				foreach($result as $player) {
					?>
									<tr>
										<th scope="row"><?php echo ++$rank; ?></th>
										<td>
											<img width="16" height="16" class="avatar small" src="https://api.ashcon.app/mojang/v1/avatar/<?php echo $player['uuid']; ?>/16" alt="<?php echo $player['username']; ?>" title="<?php echo $player['username']; ?>">
											<a style="color:#<?php echo $player['staff'] ? 'f00' : '08c'; ?>" href="/<?php echo $player['username']; ?>"><?php echo $player['username']; ?></a>
										</td>
										<td><?php echo $player[$info['stat']]; ?></td>
									</tr>
					<?php
				}
			}
			?>
								</tbody>
							</table>
						</div>
						<br>
					</div>
			<?php
		}
		?>
				</div>
		<?php
		if($boardSet !== end($boards)) {
			?>
				<br><br>
				<hr>
				<br><br>
			<?php
		}
	}
}

function graphs() {
	$graphs = [
		'kills_vs_deaths' => '<br>It definitely looks like there are been more deaths than kills, and the slope proves that. There\'s a strong assocation between the two, where even extreme outliers follow the general trend.',
		'hours_played_vs_kills' => 'This association also makes a lot of sense, despite the strange y-intercept. The average player gets <code>30 kills/hour</code>, but it takes a brand new player an average of <code>60 kills-worth</code> (or about two hours) before they follow the trend.',
		'hours_played_vs_droplets' => '<br>The data shows a promising average of <code>100 droplets per hour played</code>, but the correlation <i>is</i> rather moderate. Just like Hours Played vs Kills, it takes about two hours before the trend is met.',
		'hours_played_vs_rank' => 'For the first non-linear shape, it\'s known that hours correlate to kills, and the ranking system is dependent upon rank. This data asymptotically runs near <code>0</code> (unitless) along the rank line, resulting in the evident inverse relationship.',
		'hours_played_vs_objectives' => '<br>This data has nearly the same coefficient of determination as Hours Played vs Droplets. Without surprise, there\'s another positive, moderate, and linear correlation between the two variables.',
		'kd_vs_rank' => 'If the high-KD outliers were removed, a sharp down-trend would be seen, stretched decently further than what the bulk of data currently shows. This is more so an interesting shape than an informative piece (although it was the first one to break my line of best fit algorithm).',
		'kd_vs_objectives' => 'Using my sesquipedalian prose and years of background in mathematics, I would deem this fit a <code>poof</code>. It\'s just so poofy.<br><br>There might be a bell curve in there somewhere.',
		'average_experienced_game_length_vs_objectives' => 'Most who like to defend probably play longer than average games. Adversely, those who like to rush potentially play shorter than average games. One might expect a negative trendline to show at least more of a proportional relationship &mdash; perhaps those are the outliers along the bottom.',
		'average_experienced_game_length_vs_kd' => 'This graph should show the same effect as the previous one. The problem with the "average experienced game length" as a metric is that it continuously tends toward the average since heavy objective games aren\'t usually reliant on one defender or one attacker the majority of the time.',
		'percent_time_on_stratus_vs_droplets' => 'I bet you missed these simple, linear relationships. While the correlation is relatively weak, that can be explained away by the fact that droplets vary in weight and can be spent (so those outliers below the trendline most likely spent their droplets on gizmos).',
		'reliability_index_vs_merit_multiplier' => 'My favorite shape has got to be this one. Not only do you miss the trendline at first, but the data appears to be so algorithmically generated for those with weak merit/reliability (Hmm... I wonder why!). All players get sucked into an impossible singularity at <code>(1, 1.2)</code> due to the merit formula.',
		'rank_vs_hours_until_one_million_droplets' => '<br>This just goes to show you, kids, that investing your time in "good stats" can mean a good KD or lots of droplets, but the latter is more resistant with kills. Most people won\'t achieve 1M droplets in the first few years of gameplay.',
		'monuments_vs_cores' => '<br>Aside from looking like the end of a cotton swab, this goes to show <b>people get more monuments over cores</b> (about five times as much).',
		'wools_vs_flags' => 'Less of a cotton swab, but the same principle. <b>People get more wools than flags</b> (about five times as much).<br><br>This data may be skewed due to flags not being tracked as long as wools.',
		'captures_vs_breaks' => '<br>Last but not least, despite the data appearing mostly above the trendline, <b>people capture more than break</b>. Not a surprise when looking at the ratio between CTWs and DTC/Ms on the rotations, though.'
	];
	?>
				<div class="page-header">
					<h2>Graphs &amp; Correlations</h2>
				</div>
				<br>
				<p>Statistics aren't limited to boring old tables. We have <i>graphy bois</i>, too.<br><br>These are some of my favorite shapes when playing around with the data &mdash; I just can't think of a way to organize these whatsoever.  The picture generation takes a hot second to be created, so there's also <b>a week-long server cache</b> to help your browser re-load the minimally changed data.<br><br>As always, let me know if there's a new metric you want to see! I'm not limited to scatter plots but I am incredibly lazy in picking data.</p>
				<br><br>
				<h2>Assorted</h2>
				<br>
				<div class="row">
	<?php
	$maxDesc = max(array_map('strlen', $graphs));
	foreach($graphs as $name => $desc) {
		?>
					<div class="col col-sm-12 col-lg-6">
						<div class="card">
							<img src="/graph.png.php?g=<?php echo $name; ?>" class="card-img-top" data-action="zoom">
							<div class="card-body">
								<p class="card-text"><?php echo $desc; ?></p>
								<p style="opacity: 0; user-select: none"><?php echo str_repeat('&nbsp;&nbsp;', $maxDesc); ?><br></p>
							</div>
						</div>
						<br><br><br>
					</div>
		<?php
	}
	?>
				</div>
	<?php

	
}

function combined() {
	if(!isset($ch)) {
		$ch = new ConnectionHandler();
	}
	$statsTotals = $ch->getTotals();
	$statsAverages = $ch->getAverages();
	$statsKeys = [];
	if($statsTotals!==false & $statsTotals!==[false] && $statsAverages!==false && $statsAverages!==[false]) {
		$statsKeys = array_keys($statsTotals + $statsAverages);
	}
	?>
				<div class="page-header">
					<h2>Combined</h2>
				</div>
				<br>
				<p>While competition is fun, I'd like to think macro-collaboration is funner. These are some statistics that <i>everyone</i> adds to in some way.</p>
				<br><br>
				<div class="table-container">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th style="text-align:center" scope="col">Statistic</th>
								<th style="text-align:center" scope="col">Total Value</th>
								<th style="text-align:center" scope="col">Average Value</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th scope="row">Sample Size</th>
								<td style="text-align:center">Everyone (<?php echo number_format($ch->countPlayers()); ?>)</td>
								<td style="text-align:center">Merit &ge; 1.00 (<?php echo number_format($ch->countPlayers(.1)); ?>)</td>
							</tr>
	<?php
	if(count($statsKeys)>0) {
		foreach($statsKeys as $stat) {
			?>
							<tr>
								<th scope="row"><?php echo $stat; ?></th>
								<td style="text-align:right"><?php echo isset($statsTotals[$stat]) ? number_format($statsTotals[$stat]) : '&mdash;'; ?></td>
								<td style="text-align:right"><?php echo isset($statsAverages[$stat]) ? number_format($statsAverages[$stat], 3) : '&mdash;'; ?></td>
							</tr>
			<?php
		}
	} else {
		?>
							<tr>
								<td colspan="3">
									<center><span class="fa fa-exclamation-triangle"></span> <i>Error getting data!</i></center>
								</td>
							</tr>
		<?php
	}
	?>
						</tbody>
					</table>
				</div>
	<?php
}

function predictor() {
	?>
				<div class="page-header">
					<h2>Win Predictor</h2>
				</div>
				<div class="row">
				<br>
				<p>Below is the direct console dump of the players and statistics from the current match on Mixed.</p>
				<div class="col-md-12">
					<div class="form-inline pull-right">
						<div class="form-group">
							<a class="btn btn-sm btn-default" href="/predictor" onclick="window.location.href = '/predictor';">Refresh</a>
						</div>
					</div>
				</div>
				<br>
				<br>
				<pre><?php echo @file_get_contents('https://'.$_SERVER['SERVER_NAME'].'/stratus-gameplay'); ?></pre>
	<?php
}
?>