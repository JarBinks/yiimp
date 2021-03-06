<?php

// default values if local server config keys are not set (also used to add defines in git)
// do not change them here... set them in your serverconfig.php

if (!defined('YAAMP_PRODUCTION')) define('YAAMP_PRODUCTION', false);
if (!defined('YAAMP_USE_NGINX')) define('YAAMP_USE_NGINX', false);

if (!defined('YAAMP_DBHOST')) define('YAAMP_DBHOST', 'localhost');
if (!defined('YAAMP_DBNAME')) define('YAAMP_DBNAME', 'yaamp');
if (!defined('YAAMP_DBUSER')) define('YAAMP_DBUSER', 'root');
if (!defined('YAAMP_DBPASSWORD')) define('YAAMP_DBPASSWORD', '');

if (!defined('YIIMP_PUBLIC_EXPLORER')) define('YIIMP_PUBLIC_EXPLORER', true);

if (!defined('YAAMP_FEES_MINING')) define('YAAMP_FEES_MINING', 0.5);
if (!defined('YAAMP_FEES_EXCHANGE')) define('YAAMP_FEES_EXCHANGE', 2);
if (!defined('YAAMP_FEES_RENTING')) define('YAAMP_FEES_RENTING', 2);
if (!defined('YAAMP_PAYMENTS_FREQ')) define('YAAMP_PAYMENTS_FREQ', 24*60*60);
if (!defined('YAAMP_PAYMENTS_MINI')) define('YAAMP_PAYMENTS_MINI', 0.001);

if (!defined('YAAMP_ALLOW_EXCHANGE')) define('YAAMP_ALLOW_EXCHANGE', false);
if (!defined('EXCH_AUTO_WITHDRAW')) define('EXCH_AUTO_WITHDRAW', 9999.9999);
if (!defined('EXCH_CRYPTSY_KEY')) define('EXCH_CRYPTSY_KEY', '');
if (!defined('EXCH_BITTREX_KEY')) define('EXCH_BITTREX_KEY', '');
if (!defined('EXCH_BLEUTRADE_KEY')) define('EXCH_BLEUTRADE_KEY', '');
if (!defined('EXCH_BTER_KEY')) define('EXCH_BTER_KEY', '');
if (!defined('EXCH_CCEX_KEY')) define('EXCH_CCEX_KEY', '');
if (!defined('EXCH_CRYPTOPIA_KEY')) define('EXCH_CRYPTOPIA_KEY', '');
if (!defined('EXCH_POLONIEX_KEY')) define('EXCH_POLONIEX_KEY', '');
if (!defined('EXCH_SAFECEX_KEY')) define('EXCH_SAFECEX_KEY', '');
if (!defined('EXCH_YOBIT_KEY')) define('EXCH_YOBIT_KEY', '');
if (!defined('EXCH_BANX_USERNAME')) define('EXCH_BANX_USERNAME', '');
if (!defined('EXCH_KRAKEN_KEY')) define('EXCH_KRAKEN_KEY', '');

if (!defined('YAAMP_BTCADDRESS')) define('YAAMP_BTCADDRESS', '');
if (!defined('YAAMP_SITE_URL')) define('YAAMP_SITE_URL', 'localhost');
if (!defined('YAAMP_SITE_NAME')) define('YAAMP_SITE_NAME', 'YiiMP');
if (!defined('YAAMP_ADMIN_EMAIL')) define('YAAMP_ADMIN_EMAIL', 'yiimp@spam.la');
if (!defined('YAAMP_ADMIN_IP')) define('YAAMP_ADMIN_IP', '127.0.0.1');

if (!defined('YAAMP_LIMIT_ESTIMATE')) define('YAAMP_LIMIT_ESTIMATE', false);
if (!defined('YAAMP_RENTAL')) define('YAAMP_RENTAL', false);
if (!defined('YAAMP_USE_NICEHASH_API')) define('YAAMP_USE_NICEHASH_API', false);

if (!defined('NICEHASH_API_KEY')) define('NICEHASH_API_KEY','');
if (!defined('NICEHASH_API_ID')) define('NICEHASH_API_ID','0000');
if (!defined('NICEHASH_DEPOSIT')) define('NICEHASH_DEPOSIT','');
if (!defined('NICEHASH_DEPOSIT_AMOUNT')) define('NICEHASH_DEPOSIT_AMOUNT','0.01');

