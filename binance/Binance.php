<?php
    interface BinanceInterface {
        public function __construct($API_KEY, $SECRET_KEY);
        public function getFutures($symbol);
    }

    class BinanceAPI implements BinanceInterface {
        
        protected $API_KEY = "";
        protected $SECRET_KEY = "";

        protected $BINANCE_API_URL = "https://api.binance.com/api/v3/";

        protected $cookieStorage = array();

        public function __construct($API_KEY, $SECRET_KEY) {
            $this->API_KEY = $API_KEY;
            $this->SECRET_KEY = $SECRET_KEY;
        }

        protected function curlRequest($method, $params) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->BINANCE_API_URL.$method.http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json',
                'X-MBX-APIKEY: ' . $this->API_KEY,
            ));
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
            //curl_setopt($ch, CURLOPT_POSTFIELDS, "");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/4.0 (compatible; Tellarion)");
            return curl_exec($ch);
        }

        protected function cookie($data) {
            $data = array(
                "openTime" => $data[0][0],
                "openPrice" => $data[0][1],
                "highPrice" => $data[0][2],
                "lowPrice" => $data[0][3],
                "closePrice" => $data[0][3],
                "volume" => $data[0][4],
                "closeTime" => $data[0][5]
            );
            $this->cookieStorage = $data??null;
            $cookiesCount = count($_COOKIE);
            setcookie("graph_".$cookiesCount, json_encode($this->cookieStorage), time()+3600 * 24);
            return true;
        }

        public function getFutures($symbol) {
            $date = new DateTime("now", new DateTimeZone('UTC'));
            $data = $this->curlRequest('klines?', array("symbol" => $symbol, "interval" => '1m', "limit" => 1));
            $this->cookie(json_decode($data));
        }
    }
?>