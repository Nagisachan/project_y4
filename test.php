<?php
    echo "test";
	
	$jsonget	= file_get_contents('php://input');
	$json = utf8_encode($jsonget); 
	$obj = json_decode($json, TRUE);
	print $obj->{'content'}; // 12345
	print_r($obj);
	
	
	echo $obj['count'];
	
	print_r($_POST['content']);
   
    $json1 = json_decode($_POST['count']);
    print_r($json1);
	
	foreach ($_POST as $key => $value) {
    switch ($key) {
        case 'firstKey':
            $firstKey = $value;
            break;
        case 'secondKey':
            $secondKey = $value;
            break;
        default:
            break;
    }
}
	
?>