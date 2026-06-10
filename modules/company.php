<?php

function field_int($name, $default, $min = 0){
    return max($min, (int)($_POST[$name] ?? $default));
}

function field_text($name, $default){
    $value = trim($_POST[$name] ?? $default);
    return $value === "" ? $default : $value;
}

function get_company(){
    return [
        "name" => field_text("name", "Gesenkschmiede Beispiel GmbH"),
        "employees" => field_int("employees", 100, 1),
        "buildings" => field_int("buildings", 2, 1),
        "cnc_machines" => field_int("cnc", 8),
        "robots" => field_int("robots", 3),
        "sps" => field_int("sps", 12),
        "homeoffice_users" => field_int("homeoffice_users", 25),
        "growth_5y" => field_int("growth_5y", 30),
        "data_tb" => field_int("data_tb", 15),
        "wifi_area" => field_int("wifi_area", 1200),
        "rto_hours" => field_int("rto_hours", 4),
        "rpo_hours" => field_int("rpo_hours", 4),
        "internet" => field_text("internet", "Glasfaser"),
        "firewall" => field_text("firewall", "OPNsense"),
        "virtualization" => field_text("virtualization", "Proxmox VE"),
        "mail" => field_text("mail", "Microsoft 365 mit Mailstore"),
        "backup" => field_text("backup", "Disk-to-Disk-to-Tape"),
        "availability" => field_text("availability", "Business Standard"),
        "security_level" => field_text("security_level", "Erhoeht"),
        "cloud_strategy" => field_text("cloud_strategy", "Hybrid"),
        "it_operation" => field_text("it_operation", "Intern + Dienstleister"),
        "ot_criticality" => field_text("ot_criticality", "Hoch")
    ];
}
?>
