<?php

function get_internet_options(){

return [
"DSL" => [
"speed" => "bis 250 Mbit",
"use" => "kleine Standorte"
],

"Glasfaser" => [
"speed" => "1–10 Gbit",
"use" => "Standard Business (empfohlen)"
],

"SD-WAN" => [
"use" => "Multi-Standort Unternehmen",
"advantage" => "Failover + Routing Optimierung"
],

"5G/LTE Backup" => [
"use" => "Redundanz bei Ausfall",
"priority" => "Backup WAN"
]
];
}
?>