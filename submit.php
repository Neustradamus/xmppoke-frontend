<?php
// Don't accept anything but a POST, to prevent crawlers from starting tests.
// See https://github.com/xmpp-observatory/xmppoke-frontend/issues/5
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    return;
}

include("common.php");

if (isset($_REQUEST["domain"]) && isset($_REQUEST["type"])) {

	$domain = idn_to_utf8(strtolower(idn_to_ascii($_REQUEST["domain"])));
	$type = $_REQUEST["type"];

	$error = NULL;

	if(strpos($domain, ".") !== FALSE && preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", idn_to_ascii($domain)) && ($type === "c2s" || $type === "s2s" || $type === "client" || $type === "server")) {

		if ($type === "c2s") {
			$type = "client";
		}
		if ($type === "s2s") {
			$type = "server";
		}

		pg_prepare($dbconn, "find_result", "SELECT * FROM test_results WHERE server_name = $1 AND type = $2 ORDER BY test_date DESC LIMIT 1");

		$res = pg_execute($dbconn, "find_result", array($domain, $type));

		$result = pg_fetch_object($res);

		if ($result && (time() - strtotime($result->test_date)) < 60 * 60) {
			$error = '"' . htmlspecialchars($domain) . '" was tested too recently. Try again in an hour or <a href="result.php?domain=' . urlencode($domain) . '&type=' . $type . '">check the latest report</a>.';
		} else {
			# 20160228 edwinm was here
			# exec("LD_LIBRARY_PATH=/home/thijs/openssl/lib LUA_PATH='/home/thijs/xmppoke/?.lua;/home/thijs/share/lua/5.1/?.lua;/usr/share/lua/5.1/?.lua' LUA_CPATH='/home/thijs/xmppoke/?.so;/home/thijs/lib/lua/5.1/?.so;/usr/lib/lua/5.1/?.so;/usr/lib/x86_64-linux-gnu/lua/5.1/?.so' lua5.1 /home/thijs/xmppoke/xmppoke.lua --cafile=/etc/ssl/certs/ca-certificates.crt --db_password='" . escapeshellarg($dbpass) . "' --mode=$type -d=15 -v '" . escapeshellarg($domain) . "' --version_jid='" . $version_jid . "' --version_password='" . $version_password . "' > /dev/null &");

			$data = http_build_query(array('domain' => $domain, 'mode' => $type));
			$options = array(
				'http' => array(
					'method' => 'POST',
					'content' => $data,
					'header' => "Content-type: application/x-www-form-urlencoded\r\n"
				),
			);
			$context = stream_context_create($options);
			$result = file_get_contents($queue_url, false, $context);
			if ($result === FALSE) {
				$error = 'Failed to enqueue a test for "' . htmlspecialchars($domain) . '".';
			} else {
				$result = json_decode($result, true);
				if ($result['success'] === TRUE) {
					header("Refresh: 1;result.php?domain=" . urlencode($domain) . "&type=$type");
				} else {
					$error = $result['error'];
				}
			}
		}

	} else {
		$error = '"' . htmlspecialchars($domain) . '" is not a valid domain name.';
	}
} else {
	$error = "Something went wrong.";
}

common_header('<meta http-equiv="refresh" content="2; url=result.php?domain=' . urlencode($domain) . '&type=' . $type . '">');

?>

	<body>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php">IM Observatory</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a href="list.php">Test results</a></li>
					<li><a href="directory.php">Public server directory</a></li>
					<li><a href="about.php">About</a></li>
					<li><a href="reports.php">Stats</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">

<?php
if ($error) {
?>
		<h1>Error</h1>

		<div class="alert alert-block alert-danger">
			<?= $error ?>
		</div>

<?php
} else {
?>
		<h1>Queueing test...</h1>
<?php
}
?>
	</div> <!-- /container -->

	<!-- Le javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="./js/jquery.js"></script>
	<script src="./js/jquery.timeago.js"></script>
	<script src="./js/bootstrap.js"></script>

	<script src="./js/main.js"></script>

	</body>
</html>
