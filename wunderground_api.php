<?php

    require_once 'config.php';
    
    function wundergroundApi_getForecast($address){
        $result = "";
            
        if ($curl = curl_init()) //инициализация сеанса
        {
        
            curl_setopt($curl, CURLOPT_URL, WUNDERGROUND_API_ENDPOINT . WUNDERGROUND_API_TOKEN . $address);//указываем адрес страницы
        
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
            curl_setopt($curl, CURLOPT_HEADER, 0);
        
            $result = curl_exec($curl);//выполнение запроса
            curl_close($curl);//закрытие сеанса     
        }
        

        //$result = file_get_contents('http://api.wunderground.com/api/091c41d9519a9a9d/hourly/q/Russia/Saint_Petesburg.json');

        $weather = json_decode($result);
        $time = $weather->hourly_forecast[0]->FCTTIME->hour;
        $temperature = $weather->hourly_forecast[0]->temp->english;
        $condition = $weather->hourly_forecast[0]->condition;
        $humidity = $weather->hourly_forecast[0]->humidity;
        $wind = $weather->hourly_forecast[0]->wspd->english;
        $pop = $weather->hourly_forecast[0]->pop;
        $mslp = $weather->hourly_forecast[0]->mslp->english;
        
        $mslp_mm = $mslp * 25.4;
        
        $forecast = [
            "time" => $time,
            "temperature" => $temperature,
            "condition" => $condition,
            "humidity" => $humidity,
            "pop" => $pop,
            "mslp" => $mslp_mm
        ];
        
        //var_dump($forecast);
        
        /*foreach ($forecast as $key=>$value){
            echo "$value <br>";
        }*/
        //$forecast_json = json_encode($forecast);
        /*
            $forecast_str = "время: $time". "часов"."<br>".
                        "температура: $temperature" . "<br>".
                        "Состояние погоды: $condition" . "<br>".
                        "Влажность: $humidity" . "<br>".
                        "Давление: $mslp_mm". "<br>";
        */
            return  $forecast;
    }

?>