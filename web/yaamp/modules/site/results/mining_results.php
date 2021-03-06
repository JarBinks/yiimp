<?php

function WriteBoxHeader($title)
{
	echo "<div class='main-left-box'>";
	echo "<div class='main-left-title'>$title</div>";
	echo "<div class='main-left-inner'>";
}

$showrental = (bool) YAAMP_RENTAL;

$algo = user()->getState('yaamp-algo');

$total_rate = yaamp_pool_rate();
$total_rate_d = $total_rate? Itoa2($total_rate).'h/s': '';

if($algo == 'all')
	$list = getdbolist('db_coins', "enable and visible order by index_avg desc");
else
	$list = getdbolist('db_coins', "enable and visible and algo=:algo order by index_avg desc", array(':algo'=>$algo));

$count = count($list);

if($algo == 'all')
	$worker = getdbocount('db_workers');
else
	$worker = getdbocount('db_workers', "algo=:algo", array(':algo'=>$algo));

if ($showrental)
	$services = getdbolist('db_services', "algo=:algo ORDER BY price DESC", array(':algo'=>$algo));
else
	$services = array();

////////////////////////////////////////////////////////////////////////////////////

WriteBoxHeader("Mining $count coins at $total_rate_d * with $worker miners ($algo)");

//echo "<table  class='dataGrid2'>";
showTableSorter('maintable3');
echo "<thead>";
echo "<tr>";
echo "<th></th>";
echo "<th>Name</th>";
echo "<th align=right>Amount</th>";
echo "<th align=right>Diff</th>";
echo "<th align=right>Block</th>";
echo "<th align=right>TTF*</th>";
echo "<th align=right>Hash**</th>";
echo "<th align=right>Profit***</th>";
echo "</tr>";
echo "</thead>";

if($algo != 'all' && $showrental)
{
	$hashrate_jobs = yaamp_rented_rate($algo);
	$hashrate_jobs = $hashrate_jobs? Itoa2($hashrate_jobs).'h/s': '';

	$price_rent = dboscalar("select rent from hashrate where algo=:algo order by time desc", array(':algo'=>$algo));
	$price_rent = mbitcoinvaluetoa($price_rent);

	$amount_rent = dboscalar("select sum(amount) from jobsubmits where status=1 and algo=:algo", array(':algo'=>$algo));
	$amount_rent = bitcoinvaluetoa($amount_rent);
}

foreach($list as $coin)
{
	$name = substr($coin->name, 0, 12);
	$difficulty = Itoa2($coin->difficulty, 3);
	$price = bitcoinvaluetoa($coin->price);
	$height = number_format($coin->block_height, 0, '.', ' ');
//	$pool_ttf = $coin->pool_ttf? sectoa2($coin->pool_ttf): '';
	$pool_ttf = $total_rate? $coin->difficulty * 0x100000000 / $total_rate: 0;
	$reward = round($coin->reward, 3);

	$btcmhd = yaamp_profitability($coin);
	$pool_hash = yaamp_coin_rate($coin->id);
	$real_ttf = $pool_hash? $coin->difficulty * 0x100000000 / $pool_hash: 0;

	$pool_hash = $pool_hash? Itoa2($pool_hash).'h/s': '';
	$real_ttf = $real_ttf? sectoa2($real_ttf): '';
	$pool_ttf = $pool_ttf? sectoa2($pool_ttf): '';

	$pool_hash_pow = yaamp_pool_rate_pow($coin->algo);
	$pool_hash_pow = $pool_hash_pow? Itoa2($pool_hash_pow).'h/s': '';

	$min_ttf = $coin->network_ttf>0? min($coin->actual_ttf, $coin->network_ttf): $coin->actual_ttf;
	$network_hash = $coin->difficulty * 0x100000000 / ($min_ttf? $min_ttf: 60);
	$network_hash = $network_hash? 'network hash '.Itoa2($network_hash).'h/s': '';

	if(controller()->admin && $services)
	{
		foreach($services as $i=>$service)
		{
			if($service->price*1000 < $btcmhd) continue;
			$service_btcmhd = mbitcoinvaluetoa($service->price*1000);

			echo "<tr class='ssrow'>";
			echo "<td width=18><img width=16 src='/images/btc.png'></td>";
			echo "<td><b>$service->name</b></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td align=right style='font-size: .8em;'><b>$service_btcmhd</b></td>";
			echo "</tr>";

			unset($services[$i]);
		}
	}

	if(isset($price_rent) && $price_rent > $btcmhd)
	{
		echo "<tr class='ssrow'>";
		echo "<td width=18><img width=16 src='/images/btc.png'></td>";
		echo "<td><b>Rental</b></td>";
		echo "<td align=right style='font-size: .8em;'><b>$amount_rent BTC</b></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td align=right style='font-size: .8em;'>$hashrate_jobs</td>";
		echo "<td align=right style='font-size: .8em;'><b>$price_rent</b></td>";
		echo "</tr>";

		unset($price_rent);
	}

	if(!$coin->auto_ready)
		echo "<tr style='opacity: 0.4;'>";
	else
		echo "<tr class='ssrow'>";

	echo "<td width=18><img width=16 src='$coin->image'></td>";

	$owed = dboscalar("select sum(balance) from accounts where coinid=$coin->id");
	if($coin->balance+$coin->mint < $owed*0.9)
		echo "<td><b><a href='/site/block?id=$coin->id' title='We are short of this currency. Please select another one for payments until we find more blocks.'
			style='color: #c55'>$name</a></b><span style='font-size: .8em;'> ($coin->algo)</span></td>";

	else
		echo "<td><b><a href='/site/block?id=$coin->id'>$name</a></b><span style='font-size: .8em'> ($coin->algo)</span></td>";

	echo "<td align=right style='font-size: .8em;'><b>$reward $coin->symbol_show</a></td>";

	$title = "POW $coin->difficulty";
	if($coin->rpcencoding == 'POS')
		$title .= "\nPOS $coin->difficulty_pos";

	echo "<td align=right style='font-size: .8em;' title='$title'>$difficulty</td>";

	if(!empty($coin->errors))
		echo "<td align=right style='font-size: .8em; color: red;' title='$coin->errors'>$height</td>";
	else
		echo "<td align=right style='font-size: .8em;'>$height</td>";

	if(!empty($real_ttf))
		echo "<td align=right style='font-size: .8em;' title='$real_ttf at $pool_hash'>$pool_ttf</td>";
	else
		echo "<td align=right style='font-size: .8em;'>$pool_ttf</td>";

	if($coin->auxpow && $coin->auto_ready)
		echo "<td align=right style='font-size: .8em; opacity: 0.6;' title='merge mined\n$network_hash'>$pool_hash_pow</td>";
	else
		echo "<td align=right style='font-size: .8em;' title='$network_hash'>$pool_hash</td>";

	$btcmhd = mbitcoinvaluetoa($btcmhd);
	echo "<td align=right style='font-size: .8em;'><b>$btcmhd</b></td>";
	echo "</tr>";
}

if(controller()->admin && $services)
{
	foreach($services as $i=>$service)
	{
		$service_btcmhd = mbitcoinvaluetoa($service->price*1000);

		echo "<tr class='ssrow'>";
		echo "<td width=18><img width=16 src='/images/btc.png'></td>";
		echo "<td><b>$service->name</b></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td align=right style='font-size: .8em;'><b>$service_btcmhd</b></td>";
		echo "</tr>";
	}
}

if(isset($price_rent) && $showrental)
{
	echo "<tr class='ssrow'>";
	echo "<td width=18><img width=16 src='/images/btc.png'></td>";
	echo "<td><b>Rental</b></td>";
	echo "<td align=right style='font-size: .8em;'><b>$amount_rent BTC</b></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td></td>";
	echo "<td align=right style='font-size: .8em;'>$hashrate_jobs</td>";
	echo "<td align=right style='font-size: .8em;'><b>$price_rent</b></td>";
	echo "</tr>";

	unset($price_rent);
}


echo "</table>";

echo "<p style='font-size: .8em'>
		&nbsp;* estimated average time to find a block at full pool speed<br>
		&nbsp;** approximate from the last 5 minutes submitted shares<br>
		&nbsp;*** mBTC/Mh/day (mBTC/Gh/day for sha256 and blake algos)<br>
		</p>";

echo "</div></div><br>";


