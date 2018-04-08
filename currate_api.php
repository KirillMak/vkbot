<?php

    require_once 'config.php';

    function currateApi_getCurrency($pairs){

        $url = CURRATE_API_ENDPOINT . '?get=rates&pairs='. $pairs .'&key='. CURRATE_API_TOKEN;
            
        /*if ($curl = curl_init()) //инициализация сеанса

        {
        
            curl_setopt($curl, CURLOPT_URL, $url);
        
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
            curl_setopt($curl, CURLOPT_HEADER, 0);
        
            $currate = curl_exec($curl);
            curl_close($curl); 
        }
         */
        
            $currate = file_get_contents('https://currate.ru/api/?get=rates&pairs=EURRUB,USDRUB&key=941a1ce07a6dbfe0a8dba5df89713ef9');
            
                if (is_array($currate)){
                        $currency = json_decode($currate);  
                }
            return $currency->data;
        }
?>