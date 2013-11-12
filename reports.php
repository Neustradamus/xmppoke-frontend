<?php

include("common.php");

pg_prepare($dbconn, "sslv3_not_tls1", "SELECT * FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND sslv3 = 't' AND tlsv1 = 'f');");

$res = pg_execute($dbconn, "sslv3_not_tls1", array());

$sslv3_not_tls1 = pg_fetch_all($res);

pg_prepare($dbconn, "dnssec_srv", "SELECT * FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE results.srv_dnssec_good = 't' AND EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND priority IS NOT NULL);");

$res = pg_execute($dbconn, "dnssec_srv", array());

$dnssec_srv = pg_fetch_all($res);



pg_prepare($dbconn, "total", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't');");

$res = pg_execute($dbconn, "total", array());

$total = pg_fetch_assoc($res);

pg_prepare($dbconn, "sslv2", "SELECT * FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND sslv2 = 't');");

$res = pg_execute($dbconn, "sslv2", array());

$sslv2 = pg_fetch_all($res);

pg_prepare($dbconn, "sslv3", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND sslv3 = 't');");

$res = pg_execute($dbconn, "sslv3", array());

$sslv3 = pg_fetch_assoc($res);

pg_prepare($dbconn, "tlsv1", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND tlsv1 = 't');");

$res = pg_execute($dbconn, "tlsv1", array());

$tlsv1 = pg_fetch_assoc($res);

pg_prepare($dbconn, "tlsv1_1", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND tlsv1_1 = 't');");

$res = pg_execute($dbconn, "tlsv1_1", array());

$tlsv1_1 = pg_fetch_assoc($res);

pg_prepare($dbconn, "tlsv1_2", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND tlsv1_2 = 't');");

$res = pg_execute($dbconn, "tlsv1_2", array());

$tlsv1_2 = pg_fetch_assoc($res);



pg_prepare($dbconn, "bitsizes", "SELECT COUNT(*), rsa_bitsize FROM (SELECT DISTINCT ON (results.test_id, rsa_bitsize) rsa_bitsize FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results, srv_results, srv_certificates, certificates WHERE results.test_id = srv_results.test_id AND srv_certificates.srv_result_id = srv_results.srv_result_id AND chain_index = 0 AND certificates.certificate_id = srv_certificates.certificate_id) AS bitsizes GROUP BY rsa_bitsize ORDER BY rsa_bitsize;");

$res = pg_execute($dbconn, "bitsizes", array());

$bitsizes = pg_fetch_all($res);



pg_prepare($dbconn, "c2s_starttls_allowed", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results WHERE type = 'client' ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE requires_starttls = 'f' AND done = 't' AND test_id = results.test_id);");

$res = pg_execute($dbconn, "c2s_starttls_allowed", array());

$c2s_starttls_allowed = pg_fetch_assoc($res);

pg_prepare($dbconn, "c2s_starttls_required", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results WHERE type = 'client' ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE requires_starttls = 't' AND done = 't' AND test_id = results.test_id);");

$res = pg_execute($dbconn, "c2s_starttls_required", array());

$c2s_starttls_required = pg_fetch_assoc($res);

pg_prepare($dbconn, "s2s_starttls_allowed", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results WHERE type = 'server' ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE requires_starttls = 'f' AND done = 't' AND test_id = results.test_id);");

$res = pg_execute($dbconn, "s2s_starttls_allowed", array());

$s2s_starttls_allowed = pg_fetch_assoc($res);

pg_prepare($dbconn, "s2s_starttls_required", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results WHERE type = 'server' ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE requires_starttls = 't' AND done = 't' AND test_id = results.test_id);");

$res = pg_execute($dbconn, "s2s_starttls_required", array());

$s2s_starttls_required = pg_fetch_assoc($res);



pg_prepare($dbconn, "trusted_valid", "SELECT COUNT(*), trusted, valid_identity FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results, srv_results WHERE done = 't' AND results.test_id = srv_results.test_id GROUP BY trusted, valid_identity ORDER BY trusted, valid_identity;");

$res = pg_execute($dbconn, "trusted_valid", array());

$trusted_valid = pg_fetch_all($res);



pg_prepare($dbconn, "score_A", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND total_score >= '80');");

$res = pg_execute($dbconn, "score_A", array());

$score_A = pg_fetch_assoc($res);

pg_prepare($dbconn, "score_B", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND total_score < '80' AND total_score >= '65');");

$res = pg_execute($dbconn, "score_B", array());

$score_B = pg_fetch_assoc($res);

pg_prepare($dbconn, "score_C", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND total_score < '65' AND total_score >= '50');");

$res = pg_execute($dbconn, "score_C", array());

$score_C = pg_fetch_assoc($res);

pg_prepare($dbconn, "score_D", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND total_score < '50' AND total_score >= '35');");

$res = pg_execute($dbconn, "score_D", array());

$score_D = pg_fetch_assoc($res);

pg_prepare($dbconn, "score_E", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND total_score < '35' AND total_score >= '20');");

$res = pg_execute($dbconn, "score_E", array());

$score_E = pg_fetch_assoc($res);

pg_prepare($dbconn, "score_F", "SELECT COUNT(*) FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND total_score < '20');");

$res = pg_execute($dbconn, "score_F", array());

$score_F = pg_fetch_assoc($res);


pg_prepare($dbconn, "reorders_ciphers", "SELECT * FROM (SELECT DISTINCT ON (server_name, type) * FROM test_results ORDER BY server_name, type, test_date DESC) AS results WHERE EXISTS (SELECT * FROM srv_results WHERE test_id = results.test_id AND done = 't' AND reorders_ciphers = 't');");

$res = pg_execute($dbconn, "reorders_ciphers", array());

$reorders_ciphers = pg_fetch_all($res);

common_header();

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
					<li class="active"><a href="reports.php">Stats</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">

		<h1>Various reports of all servers tested</h1>

        <h3 id="tls">TLS versions <small class="text-muted"><?= $total["count"] ?> results</small></h3>

		<table class="table table-bordered table-striped">
			<tr>
                <td>SSL 2</td>
                <td><?= count($sslv2) ?> <span class="text-muted"><?= round(100 * count($sslv2) / $total["count"]) ?>%</span></td>
				<td style="width: 50%;">
					<div class="progress">
						<div class="progress-bar" role="progressbar" aria-valuenow="<?= count($sslv2) ?>" aria-valuemin="0" aria-valuemax="<?= $total["count"] ?>" style="width: <?= 100 * count($sslv2) / $total["count"] ?>%"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td>SSL 3</td>
                <td><?= $sslv3["count"] ?> <span class="text-muted"><?= round(100 * $sslv3["count"] / $total["count"]) ?>%</span></td>
				<td>
					<div class="progress">
						<div class="progress-bar" role="progressbar" aria-valuenow="<?= $sslv3["count"] ?>" aria-valuemin="0" aria-valuemax="<?= $total["count"] ?>" style="width: <?= 100 * $sslv3["count"] / $total["count"] ?>%"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td>TLS 1.0</td>
                <td><?= $tlsv1["count"] ?> <span class="text-muted"><?= round(100 * $tlsv1["count"] / $total["count"]) ?>%</span></td>
				<td>
					<div class="progress">
						<div class="progress-bar" role="progressbar" aria-valuenow="<?= $tlsv1["count"] ?>" aria-valuemin="0" aria-valuemax="<?= $total["count"] ?>" style="width: <?= 100 * $tlsv1["count"] / $total["count"] ?>%"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td>TLS 1.1</td>
                <td><?= $tlsv1_1["count"] ?> <span class="text-muted"><?= round(100 * $tlsv1_1["count"] / $total["count"]) ?>%</span></td>
				<td>
					<div class="progress">
						<div class="progress-bar" role="progressbar" aria-valuenow="<?= $tlsv1_1["count"] ?>" aria-valuemin="0" aria-valuemax="<?= $total["count"] ?>" style="width: <?= 100 * $tlsv1_1["count"] / $total["count"] ?>%"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td>TLS 1.2</td>
                <td><?= $tlsv1_2["count"] ?> <span class="text-muted"><?= round(100 * $tlsv1_2["count"] / $total["count"]) ?>%</span></td>
				<td>
					<div class="progress">
						<div class="progress-bar" role="progressbar" aria-valuenow="<?= $tlsv1_2["count"] ?>" aria-valuemin="0" aria-valuemax="<?= $total["count"] ?>" style="width: <?= 100 * $tlsv1_2["count"] / $total["count"] ?>%"></div>
					</div>
				</td>
			</tr>
		</table>

		<h3 id="grades">Grades <small class="text-muted"><?= $total["count"] ?> results</small></h3>

		<table class="table table-bordered table-striped">
			<tr>
				<th>A</th>
				<td><?= $score_A["count"] ?> <span class="text-muted"><?= round(100 * $score_A["count"] / $total["count"]) ?>%</span></td>
			</tr>
			<tr>
				<th>B</th>
				<td><?= $score_B["count"] ?> <span class="text-muted"><?= round(100 * $score_B["count"] / $total["count"]) ?>%</span></td>
			</tr>
			<tr>
				<th>C</th>
				<td><?= $score_C["count"] ?> <span class="text-muted"><?= round(100 * $score_C["count"] / $total["count"]) ?>%</span></td>
			</tr>
			<tr>
				<th>D</th>
				<td><?= $score_D["count"] ?> <span class="text-muted"><?= round(100 * $score_D["count"] / $total["count"]) ?>%</span></td>
			</tr>
			<tr>
				<th>E</th>
				<td><?= $score_E["count"] ?> <span class="text-muted"><?= round(100 * $score_E["count"] / $total["count"]) ?>%</span></td>
			</tr>
			<tr>
				<th>F</th>
				<td><?= $score_F["count"] ?> <span class="text-muted"><?= round(100 * $score_F["count"] / $total["count"]) ?>%</span></td>
			</tr>
		</table>

		<span class="text-muted">Does not penalize untrusted certificates or SSLv2 support.</span>

		<h3 id="rsa">RSA key sizes for domain certificates</h3>

		<table class="table table-bordered table-striped">
			<tr>
				<th>RSA key size</th>
				<th>Count</th>
			</tr>
<?php
$total = 0;

foreach ($bitsizes as $bitsize) {
        $total += $bitsize["count"];
}

foreach ($bitsizes as $bitsize) {
?>
			<tr>
				<td><?= $bitsize["rsa_bitsize"] ?></td>
                <td><?= $bitsize["count"] ?> <span class="text-muted"><?= round(100 * $bitsize["count"] / $total) ?>%</span></td>
			</tr>
<?php
}
?>
		</table>

		<h3 id="starttls">StartTLS</h3>

		<table class="table table-bordered table-striped">
			<tr>
				<th>Type</th>
				<th>Required</th>
				<th>Allowed</th>
			</tr>
			<tr>
				<td>Client to server</td>
                <td><?= $c2s_starttls_required["count"] ?> <span class="text-muted"><?= round(100 * $c2s_starttls_required["count"] / ($c2s_starttls_required["count"] + $c2s_starttls_allowed["count"])) ?>%</span></td>
                <td><?= $c2s_starttls_allowed["count"] ?> <span class="text-muted"><?= round(100 * $c2s_starttls_allowed["count"] / ($c2s_starttls_required["count"] + $c2s_starttls_allowed["count"])) ?>%</span></td>
			</tr>
			<tr>
				<td>Server to server</td>
                <td><?= $s2s_starttls_required["count"] ?> <span class="text-muted"><?= round(100 * $s2s_starttls_required["count"] / ($s2s_starttls_required["count"] + $s2s_starttls_allowed["count"])) ?>%</span></td>
                <td><?= $s2s_starttls_allowed["count"] ?> <span class="text-muted"><?= round(100 * $s2s_starttls_allowed["count"] / ($s2s_starttls_required["count"] + $s2s_starttls_allowed["count"])) ?>%</span></td>
			</tr>
		</table>

		<h3 id="trust">Trust</h3>

<?php

$total = $trusted_valid[0]["count"] + $trusted_valid[1]["count"] + $trusted_valid[2]["count"] + $trusted_valid[3]["count"];

?>

		<table class="table table-bordered table-striped">
			<tr>
				<th></th>
				<th>Trusted</th>
				<th>Untrusted</th>
			</tr>
			<tr>
				<th>Valid</td>
				<td><?= $trusted_valid[3]["count"] ?> <span class="text-muted"><?= round(100 * $trusted_valid[3]["count"] / $total) ?>%</span></td>
				<td><?= $trusted_valid[1]["count"] ?> <span class="text-muted"><?= round(100 * $trusted_valid[1]["count"] / $total) ?>%</span></td>
			</tr>
			<tr>
				<th>Invalid</td>
				<td><?= $trusted_valid[2]["count"] ?> <span class="text-muted"><?= round(100 * $trusted_valid[2]["count"] / $total) ?>%</span></td>
				<td><?= $trusted_valid[0]["count"] ?> <span class="text-muted"><?= round(100 * $trusted_valid[0]["count"] / $total) ?>%</span></td>
			</tr>
		</table>

		<h3 id="sslv3butnottls1">Servers supporting SSL 3, but not TLS 1.0 <small class="text-muted"><?= count($sslv3_not_tls1) ?> results</small></h3>

		<table class="table table-bordered table-striped">
			<tr>
				<th>Target</th>
				<th>Type</th>
				<th>When</th>
			</tr>
<?php
foreach ($sslv3_not_tls1 as $result) {
?>
			<tr>
				<td><a href="result.php?domain=<?= $result["server_name"] ?>&amp;type=<?= $result["type"] ?>"><?= $result["server_name"] ?></a></td>
				<td><?= $result["type"] ?> to server</td>
				<td><time class="timeago" datetime="<?= date("c", strtotime($result["test_date"])) ?>"><?= date("c", strtotime($result["test_date"])) ?></time></td>
			</tr>
<?php
}
?>
		</table>

		<h3 id="sslv2wallofshame">Servers supporting SSL 2 <small class="text-muted"><?= count($sslv2) ?> results</small></h3>

		<table class="table table-bordered table-striped">
			<tr>
				<th>Target</th>
				<th>Type</th>
				<th>When</th>
			</tr>
<?php
foreach ($sslv2 as $result) {
?>
			<tr>
				<td><a href="result.php?domain=<?= $result["server_name"] ?>&amp;type=<?= $result["type"] ?>"><?= $result["server_name"] ?></a></td>
				<td><?= $result["type"] ?> to server</td>
				<td><time class="timeago" datetime="<?= date("c", strtotime($result["test_date"])) ?>"><?= date("c", strtotime($result["test_date"])) ?></time></td>
			</tr>
<?php
}
?>
		</table>

		<h3 id="dnssecsrv">Servers with DNSSEC signed SRV records <small class="text-muted"><?= count($dnssec_srv) ?> results</small></h3>

		<table class="table table-bordered table-striped">
			<tr>
				<th>Target</th>
				<th>Type</th>
				<th>When</th>
			</tr>
<?php
foreach ($dnssec_srv as $result) {
?>
			<tr>
				<td><a href="result.php?domain=<?= $result["server_name"] ?>&amp;type=<?= $result["type"] ?>"><?= $result["server_name"] ?></a></td>
				<td><?= $result["type"] ?> to server</td>
				<td><time class="timeago" datetime="<?= date("c", strtotime($result["test_date"])) ?>"><?= date("c", strtotime($result["test_date"])) ?></time></td>
			</tr>
<?php
}
?>
		</table>

		<h3 id="reordersciphers">Servers that pick their own cipher order <small class="text-muted"><?= count($reorders_ciphers) ?> results</small></h3>

		<table class="table table-bordered table-striped">
			<tr>
				<th>Target</th>
				<th>Type</th>
				<th>When</th>
			</tr>
<?php
foreach ($reorders_ciphers as $result) {
?>
			<tr>
				<td><a href="result.php?domain=<?= $result["server_name"] ?>&amp;type=<?= $result["type"] ?>"><?= $result["server_name"] ?></a></td>
				<td><?= $result["type"] ?> to server</td>
				<td><time class="timeago" datetime="<?= date("c", strtotime($result["test_date"])) ?>"><?= date("c", strtotime($result["test_date"])) ?></time></td>
			</tr>
<?php
}
?>
		</table>

		
		<div class="footer">
			<p>Some rights reserved.</p>
		</div>
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