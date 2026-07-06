<?php
$ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . base64_encode('yRTVGM8W5sVr4BE97gmHQxb2Bbw94qKSv6rCtuwRey6C3Tl9:aKHF6rJb5KVwPGovKW0KD31YElSkGY97a1qtn7aAglAPyNXxMSG8UKfCLwXcC6cf')]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
if(curl_errno($ch)) { echo "Error 1: " . curl_error($ch) . "\n"; }
echo "Secret 1 (MPESA): " . $response . "\n";

$ch2 = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . base64_encode('yRTVGM8W5sVr4BE97gmHQxb2Bbw94qKSv6rCtuwRey6C3Tl9:aXmxmP71b9JIG7S6J3GAOzJJiZD3J1r056QGuOzHcApmFoRPOShEnQ0VAgbNTkYe')]);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
$response2 = curl_exec($ch2);
if(curl_errno($ch2)) { echo "Error 2: " . curl_error($ch2) . "\n"; }
echo "Secret 2 (DARAJA): " . $response2 . "\n";
