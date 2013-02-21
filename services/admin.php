<?php
try {
	require_once( "../application/configs/config.php" );
	
	$application->bootstrap();

	$response = "toto";

}catch (Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
   		
echo $response;
?>