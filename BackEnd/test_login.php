<?php
$ch=curl_init('http://127.0.0.1:8000/api/auth/login');
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POST,true);
curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json','Accept: application/json','X-Api-Token: KampusResik_Secret_Token_2026']);
curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(['email'=>'admin@kampusresik.id','password'=>'admin123']));
echo curl_exec($ch);
echo "\nCODE: " . curl_getinfo($ch, CURLINFO_HTTP_CODE);
