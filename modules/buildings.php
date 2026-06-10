<?php

function get_buildings($company){

$buildings = [];

for($i=1;$i<=$company["buildings"];$i++){

if($i == 1){
$type = "🏢 Zentrale IT + Serverraum + Management";
}
elseif($i == 2){
$type = "🏭 Produktion (OT Netzwerk: CNC / SPS / Roboter)";
}
else{
$type = "📦 Lager / Erweiterung / Logistik";
}

$buildings["Gebäude $i"] = [
"type" => $type,
"server_room" => ($i==1),
"ot_network" => ($i==2)
];
}

return $buildings;
}
?>