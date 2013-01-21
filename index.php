<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>srIT.us</title>
	<!--style>
		* { border: 1px dashed #000; }
		body { background: #ccc; }
		#wrapper {width: 500px; margin: 200px auto 0 auto; }
		label { display: block; width: 100px; }
		input { width: 350px; }
		input[type=submit] { width: 100px; }
	</style-->
</head>
<body>
	<div id="wrapper">
	<form id="form1" method="POST">
		<label for="url">URL:</label>
		<input id="url" name="url" size="45" type="text"><br />
		<input id="submit" name="Submit" type="submit" value="Shorten">
	</form>
	</div>

</body>
</html>
<?php
if ($_SERVER['HTTP_HOST'] == '' || $_SERVER['HTTP_HOST'] == NULL) die ('Could not detect domain name.');

$con = mysql_connect("localhost","shorturl","");  
if (!$con) {
	die('Could not connect: ' . mysql_error());  
}
mysql_select_db( "spacerockit_shorturl", $con ); 

if ( isset( $_SERVER['PATH_INFO'] ) ) {
	#print_r( array("<pre>", $_SERVER) );
	$url = trim( $_SERVER['PATH_INFO'], "/");
	$sql = "SELECT id, url FROM short_urls WHERE shortened = '". $url ."'";
	$result = mysql_query($sql, $con);
	while( $row = mysql_fetch_array( $result ) ) {
		$long_url = $row['url'];
		if ( parse_url($long_url, PHP_URL_SCHEME) == NULL ) {
			$long_url = "http://". $long_url;
		}
		$select = "SELECT * FROM `short_urls` WHERE `shortened` = '". $url ."'";
		#echo $select;
		
		$result = mysql_query($select, $con);
		$row['select'] = mysql_fetch_assoc($result);
		
		$visit_count = $row['select']['count'];
		$visit_count++;
		#echo "<br />visits: ". $visit_count;
		
		$query = "UPDATE `short_urls` SET `count` = '". $visit_count ."' WHERE  `shortened` = '". $url ."'";
		#echo "<br />". $query;
		$result = mysql_query($query, $con);
		
		#print_r( array("<pre>", $_SERVER) );
		$insert = "INSERT INTO `analytics` (`id`, `request_time`, `short_url`, `unique_id`, `http_host`, `http_connection`, `http_accept_encoding`, `http_cf_connecting_ip`, `http_cf_ipcountry`, `http_x_forwarded_for`, `http_x_forwarded_proto`, `http_cf_visitor`, `http_user_agent`, `http_accept`, `http_accept_language`, `http_accept_charset`, `http_cookie`, `http_via`) ";
		$insert .= "VALUES (NULL, '". $_SERVER['REQUEST_TIME'] ."', '". $url ."', '". $_SERVER['UNIQUE_ID'] ."', '". $_SERVER['HTTP_HOST'] ."', '". $_SERVER['HTTP_CONNECTION'] ."', '". $_SERVER['HTTP_ACCEPT_ENCODING'] ."', '". $_SERVER['HTTP_CF_CONNECTING_IP'] ."', '". $_SERVER['HTTP_CF_IPCOUNTRY'] ."', '". $_SERVER['HTTP_X_FORWARDED_FOR'] ."', '". $_SERVER['HTTP_X_FORWARDED_PROTO'] ."', '". $_SERVER['HTTP_CF_VISITOR'] ."', '". $_SERVER['HTTP_USER_AGENT'] ."', '". $_SERVER['HTTP_ACCEPT'] ."', '". $_SERVER['HTTP_ACCEPT_LANGUAGE'] ."', '". $_SERVER['HTTP_ACCEPT_CHARSET'] ."', '". $_SERVER['HTTP_COOKIE'] ."', '". $_SERVER['HTTP_VIA'] ."');";
		//$sql = "INSERT INTO `access` (`id`, `url_id`, `accessed`, `vars` ) VALUES (NULL, ". $row['id'] .", ". time() .", '". $serv ."')";
		
		#echo "<br />". $insert;
		$result = mysql_query($insert, $con);// or die('cannot add access '. $sql);
		
		header( "location: ". $long_url );
	}
} elseif ( isset( $_POST['url'] ) ) {
	$url = mysql_real_escape_string( $_POST['url'] );  
	if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
		$rand = rand( 10000, 99999 );
		$shorturl = base_convert( $rand, 20, 36 );  
		$sql = "INSERT INTO short_urls (id, created, url, shortened) VALUES ('', '". time()."', '". $url ."', '". $shorturl ."')";  
		mysql_query( $sql, $con );  
		echo '<p>Shortened url is <a href="http://'. $_SERVER['HTTP_HOST'] .'/'. $shorturl .'">http://'. $_SERVER['HTTP_HOST'] .'/'. $shorturl .'</a>'; 
	}
	else {
		echo "URL is invalid. Try again.";
	}
}
 
mysql_close($con);  

function install() {
/*
CREATE TABLE IF NOT EXISTS `short_urls` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `created` int(16) NOT NULL,
  `url` varchar(256) COLLATE utf8_bin NOT NULL,
  `shortened` varchar(8) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`shortened`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;
*/

/*
CREATE TABLE IF NOT EXISTS `analytics` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `request_time` varchar(128) NOT NULL,
  `unique_id` text NOT NULL, 
  `http_host` text NOT NULL,
  `http_connection` text NOT NULL,
  `http_accept_encoding` text NOT NULL,
  `http_cf_connecting_ip` text NOT NULL,
  `http_cf_ipcountry` text NOT NULL,
  `http_x_forwarded_for` text NOT NULL,
  `http_x_forwarded_proto` text NOT NULL,
  `http_cf_visitor` text NOT NULL,
  `http_user_agent` text NOT NULL,
  `http_accept` text NOT NULL,
  `http_accept_language` text NOT NULL,
  `http_accept_charset` text NOT NULL,
  `http_cookie` text NOT NULL,
  `http_via` text NOT NULL,
  `short_url` varchar(8) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`short_url`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
*/

}