<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $Binance = require_once('binance/Binance.php');
    $Binance = new BinanceAPI("", "");

    $symbol = "BTCUSDT";
    $json = $Binance->getFutures($symbol);
?>