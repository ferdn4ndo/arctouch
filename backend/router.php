<?php

#Classes
include(__DIR__."/class/EndpointHandler.php");
include(__DIR__."/class/HttpHandler.php");

#Check for request URL
$requestURL = $_GET['requestURL'] ?? '';

#API key
$apiKey = '1f54bd990f1cdfb230adb312546d765d';

#Instantiate a new EndpointHandler class
$endpointHandler = new EndpointHandler($requestURL,$apiKey);

#Process the request
$responseArray = $endpointHandler->proccess();

#Output result in a JSON format
echo json_encode($responseArray);
?>