<?php
    $raw = file_get_contents("php://input");
    $data = json_decode($raw);
    
    if(!isset($data->text)){
        
        $err = fopen("error.txt","w+");
        fwrite($err,$data->text);
        fclose($err);
        
        http_response_code(400);
        echo "need 'text' parameter.";
        // echo print_r($raw,true);
        // echo mb_detect_encoding($raw);
        exit();
    }
    
    header("Content-type: application/json");
    
    $text = $data->text;
    
    $locale='en_US.UTF-8';
    setlocale(LC_ALL,$locale);
    putenv('LC_ALL='.$locale);
    $output = exec("echo '$text' | nc 127.0.0.1 6789");
    
    echo "[$output]";
