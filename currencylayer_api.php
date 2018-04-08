<?php

    require_once 'config.php';
    /*
    $currencies = "USD,EUR,RUB";
    $access_key = CURRENCYLAYER_API_TOKEN;
    $format = 1;

    $query = [
        "currencies" => $currencies,
        "access_key" => CURRENCYLAYER_API_TOKEN,
        "format" => $format
    ];*/
    function currencylayerApi_getCurrency($query){

        //http://www.apilayer.net/api/live?access_key=36d79a3a15e94499d2ceb7aaf6afabc2&currencies=USD,EUR,RUB&format=1
        $url = CURRENCYLAYER_API_ENDPOINT . '?' . http_build_query($query);
   
        $currate = file_get_contents($url);
        $currency = json_decode($currate);
                       // $USDRUB = $currency->data->USDRUB;
                       // $EURRUB = $currency->data->EURRUB;
                         

            return $currency;
        }
?>