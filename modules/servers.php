<?php

function get_server_roles($company){

$emp = $company["employees"];

$servers = [
"AD01" => "Active Directory Domain Controller 1",
"AD02" => "Active Directory Domain Controller 2",
"FILE01" => "File Server (SMB/DFS)",
"BACKUP01" => "Backup Server (3-2-1 Strategy)",
"VPN01" => "WireGuard VPN Gateway",
"MON01" => "Monitoring Server (Zabbix)"
];

if($emp > 80){

$servers["ERP01"] = "ERP System (Odoo / Dynamics / SAP)";
$servers["SQL01"] = "Database Server";
$servers["VOIP01"] = "3CX Telefonanlage Server";
}

if($emp > 120){

$servers["MES01"] = "Manufacturing Execution System";
$servers["DOCKER01"] = "Container Platform (Proxmox LXC/Docker)";
$servers["TEST01"] = "Test / Sandbox Environment";
}

return $servers;
}
?>