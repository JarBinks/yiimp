<?php

$defaultalgo = user()->getState('yaamp-algo');

echo "<div class='main-left-box'>";
echo "<div class='main-left-title'>Pool Status</div>";
echo "<div class='main-left-inner'>";

//echo "<table class='dataGrid2'>";
showTableSorter('maintable1');
echo "<thead>";
echo "<tr>";
echo "<th>Algo</th>";
echo "<th align=right>Port</th>";
echo "<th align=right>Coins</th>";
echo "<th align=right>Miners</th>";
echo "<th align=right>Hashrate</th>";
echo "<th align=right>Fees**</th>";
echo '<th class="estimate" align=right>Current<br>Estimate</th>';
//echo "<th>Norm</th>";
echo '<th class="estimate" align=right>24 Hours<br>Estimated</th>';
echo "<th align=right>24 Hours<br>Actual***</th>";
echo "</tr>";
echo "</thead>";

$best_algo = '';
$best_norm = 0;

$algos = array();
foreach(yaamp_get_algos() as $algo)
{
	$algo_norm = yaamp_get_algo_norm($algo);

	$price = controller()->memcache->get_database_scalar("current_price-$algo",
		"select price from hashrate where algo=:algo order by time desc limit 1", array(':algo'=>$algo));

	$norm = $price*$algo_norm;
	$norm = take_yaamp_fee($norm, $algo);

	$algos[] = array($norm, $algo);

	if($norm > $best_norm)
	{
		$best_norm = $norm;
		$best_algo = $algo;
	}
}

function cmp($a, $b)
{
	return $a[0] < $b[0];
}

usort($algos, 'cmp');

$total_coins = 0;
$total_miners = 0;

$showestimates = false;

echo "<tbody>";
foreach($algos as $item)
{
	$norm = $item[0];
	$algo = $item[1];

	$coinsym = '';
	$coins = getdbocount('db_coins', "enable and visible and auto_ready and algo=:algo", array(':algo'=>$algo));
	if ($coins == 1) {
		// If we only mine one coin, show it...
		$coin = getdbosql('db_coins', "enable and visible and auto_ready and algo=:algo", array(':algo'=>$algo));
		$coinsym = empty($coin->symbol2) ? $coin->symbol : $coin->symbol2;
		$coinsym = '<span title="'.$coin->name.'">'.$coinsym.'</a>';
	}

	if (!$coins) continue;

	$workers = getdbocount('db_workers', "algo=:algo", array(':algo'=>$algo));

	$hashrate = controller()->memcache->get_database_scalar("current_hashrate-$algo",
		"select hashrate from hashrate where algo=:algo order by time desc limit 1", array(':algo'=>$algo));
	$hashrate = $hashrate? Itoa2($hashrate).'h/s': '-';

	$price = controller()->memcache->get_database_scalar("current_price-$algo",
		"select price from hashrate where algo=:algo order by time desc limit 1", array(':algo'=>$algo));

	$price = $price? mbitcoinvaluetoa(take_yaamp_fee($price, $algo)): '-';
	$norm = mbitcoinvaluetoa($norm);

	$t = time() - 24*60*60;

	$avgprice = controller()->memcache->get_database_scalar("current_avgprice-$algo",
		"select avg(price) from hashrate where algo=:algo and time>$t", array(':algo'=>$algo));
	$avgprice = $avgprice? mbitcoinvaluetoa(take_yaamp_fee($avgprice, $algo)): '-';

	$total1 = controller()->memcache->get_database_scalar("current_total-$algo",
		"SELECT SUM(amount*price) AS total FROM blocks WHERE time>$t AND algo=:algo AND NOT category IN ('orphan','stake','generated')",
		array(':algo'=>$algo)
	);

	$hashrate1 = controller()->memcache->get_database_scalar("current_hashrate1-$algo",
		"select avg(hashrate) from hashrate where time>$t and algo=:algo", array(':algo'=>$algo));

	$algo_unit_factor = yaamp_algo_mBTC_factor($algo);
	$btcmhday1 = $hashrate1 != 0? mbitcoinvaluetoa($total1 / $hashrate1 * 1000000 * 1000 * $algo_unit_factor): '';

	$fees = yaamp_fee($algo);
	$port = getAlgoPort($algo);

	if($defaultalgo == $algo)
		echo "<tr style='cursor: pointer; background-color: #e0d3e8;' onclick='javascript:select_algo(\"$algo\")'>";
	else
		echo "<tr style='cursor: pointer' class='ssrow' onclick='javascript:select_algo(\"$algo\")'>";

	echo "<td><b>$algo</b></td>";
	echo "<td align=right style='font-size: .8em;'>$port</td>";
	echo "<td align=right style='font-size: .8em;'>".($coins==1 ? $coinsym : $coins)."</td>";
	echo "<td align=right style='font-size: .8em;'>$workers</td>";
	echo "<td align=right style='font-size: .8em;'>$hashrate</td>";
	echo "<td align=right style='font-size: .8em;'>{$fees}%</td>";

	if($algo == $best_algo)
		echo '<td class="estimate" align="right" style="font-size: .8em;" title="normalized '.$norm.'"><b>'.$price.'*</b></td>';
	else if($norm>0)
		echo '<td class="estimate" align="right" style="font-size: .8em;" title="normalized '.$norm.'">'.$price.'</td>';

	else
		echo '<td class="estimate" align="right" style="font-size: .8em;">'.$price.'</td>';


	echo '<td class="estimate" align="right" style="font-size: .8em;">'.$avgprice.'</td>';

	if($algo == $best_algo)
		echo "<td align=right style='font-size: .8em;'><b>$btcmhday1*</b></td>";
	else
		echo "<td align=right style='font-size: .8em;'>$btcmhday1</td>";

	echo "</tr>";

	$total_coins += $coins;
	$total_miners += $workers;
}

echo "</tbody>";

if($defaultalgo == 'all')
	echo "<tr style='cursor: pointer; background-color: #e0d3e8;' onclick='javascript:select_algo(\"all\")'>";
else
	echo "<tr style='cursor: pointer' class='ssrow' onclick='javascript:select_algo(\"all\")'>";

echo "<td><b>all</b></td>";
echo "<td></td>";
echo "<td align=right style='font-size: .8em;'>$total_coins</td>";
echo "<td align=right style='font-size: .8em;'>$total_miners</td>";
echo "<td></td>";
echo "<td></td>";
echo '<td class="estimate"></td>';
echo '<td class="estimate"></td>';
echo "<td></td>";
echo "</tr>";

echo "</table>";

echo "<p style='font-size: .8em'>
		&nbsp;* best normalized multi algo<br>
		&nbsp;** fees are now fixed manually.<br>
		&nbsp;*** values in mBTC/Mh/day (mBTC/Gh/day for sha256 and blake algos)<br>
		</p>";

echo "</div></div><br>";
?>

<?php if (!$showestimates): ?>

<style type="text/css">
#maintable1 .estimate { display: none; }
</style>

<?php endif; ?>

