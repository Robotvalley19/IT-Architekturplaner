<?php

function get_firewall_options($company){

$emp = $company["employees"];

if($emp < 60){
return [
"recommended" => "OPNsense Single Appliance",
"reason" => "Small Business / Cost Efficient"
];
}

if($emp < 150){
return [
"recommended" => "OPNsense HA Cluster",
"reason" => "Redundanz + Business Stability"
];
}

return [
"recommended" => "Fortigate / Palo Alto Cluster",
"reason" => "Enterprise Security + High Availability"
];
}
?>