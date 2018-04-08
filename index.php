<?php

require_once 'wunderground_api.php';
require_once 'currate_api.php';
require_once 'currencylayer_api.php';
require_once 'config.php';

if (!isset($_REQUEST)) {
    exit;
}

//Строка для подтверждения адреса сервера из настроек Callback API
$confirmationToken = '0b656b66';

//Ключ доступа сообщества
$token = 'd609d14ad4d9809c7b067d168b1a320cebdd50211289ffe7a607cbfc0b823918843c67696ae3934387a68';

// Secret key
$secretKey = 'dream';

//Получаем и декодируем уведомление
$data = json_decode(file_get_contents('php://input'));

// проверяем secretKey
if(strcmp($data->secret, $secretKey) !== 0 && strcmp($data->type, 'confirmation') !== 0)
    return;

//Проверяем, что находится в поле "type"
switch ($data->type) {
    //Если это уведомление для подтверждения адреса сервера...
    case 'confirmation':
        //...отправляем строку для подтверждения адреса
        echo $confirmationToken;
        break;

    //Если это уведомление о новом сообщении...
    case 'message_new':
        //...получаем id его автора
        $userId = $data->object->user_id;
        //затем с помощью users.get получаем данные об авторе
        $userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&v=5.0"));

        //и извлекаем из ответа его имя
        $user_name = $userInfo->response[0]->first_name;

        $message = $data->object->body;
        //$attachments = array();
        $msg = "{$user_name}, пивет!<br>".
        "Погода на текущий час следующая: ";
        if ($message == 'КИНЬ СИСЬКИ'){
            $attachments = array(
                'photo'.$photo['owner_id'].'_'.$photo['id']
              );
              $msg = " тебе есть 18?";
        }

        $forecast_type = "/hourly/q/";
        $country = "Russia/";
        $city = "Saint_Petesburg".".json";
    
        $address =  $forecast_type . $country . $city;

        $forecast = wundergroundApi_getForecast($address);
        $forecast_str = "время: ". $forecast['time'] . "часов"."<br>".
                        "температура: " . $forecast['temperature'] . "<br>".
                        "Состояние погоды: " . $forecast['condition'] . "<br>".
                        "Влажность: " . $forecast['humidity'] . "<br>".
                        "Давление: ". $forecast['mslp'] . "<br>";

        //var_dump($message);

        //http://api.wunderground.com/api/091c41d9519a9a9d/hourly/q/Russia/Saint_Petesburg.json

        //$pairs = 'EURRUB,USDRUB';
        //$data = json_encode(currateApi_getCurrency($pairs));

        $currencies = "USD,EUR,RUB";
        $access_key = CURRENCYLAYER_API_TOKEN;
        $format = 1;

        $query = [
            "currencies" => $currencies,
            "access_key" => CURRENCYLAYER_API_TOKEN,
            "format" => $format
        ];

        $currencies_rate = currencylayerApi_getCurrency($query)->quotes;
        $USDRUB = $currencies_rate->USDRUB;
        $USDEUR = $currencies_rate->USDEUR;
        $EURRUB = (1 / $USDEUR) * $USDRUB;
        $currency = json_encode(currencylayerApi_getCurrency($query)->quotes);
        //$USDRUB = $data -> USDRUB;
        //$EURRUB= $data->EURRUB;
         
        //С помощью messages.send и токена сообщества отправляем ответное сообщение
        $request_params = array(
            'message' => "$msg"."<br>". 
                        "*********** ПРОГНОЗ ПОГОДЫ ************"."<br>".
                        "$forecast_str"."<br>".
                        "*********** КУРС ВАЛЮТ ************"."<br>".
                        "Доллар/руб: " . $USDRUB . "<br>".
                        "Евро/руб: " . $EURRUB . "<br>",
            'user_id' => $userId,
            'access_token' => $token,
            'v' => '5.0'
        );

        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);

        //Возвращаем "ok" серверу Callback API
        echo('ok');

        break;

    // Если это уведомление о вступлении в группу
    case 'group_join':
        //...получаем id нового участника
        $userId = $data->object->user_id;

        //затем с помощью users.get получаем данные об авторе
        $userInfo = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$userId}&v=5.0"));

        //и извлекаем из ответа его имя
        $user_name = $userInfo->response[0]->first_name;

        //С помощью messages.send и токена сообщества отправляем ответное сообщение
        $request_params = array(
            'message' =>    "Добро пожаловать в наше сообщество, {$user_name}!" ,
            'user_id' => $userId,
            'access_token' => $token,
            'v' => '5.0'
        );

        $get_params = http_build_query($request_params);

        file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);

        //Возвращаем "ok" серверу Callback API
        echo('ok');

        break;
}
?>