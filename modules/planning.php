<?php

function plan_size($company){
    $score = $company["employees"] + ($company["cnc_machines"] * 3) + ($company["robots"] * 4) + $company["sps"];

    if($score < 80){
        return "small";
    }

    if($score < 180){
        return "medium";
    }

    return "large";
}

function money($value){
    return number_format($value, 0, ",", ".") . " EUR";
}

function get_vlans($company){
    return [
        ["id" => 10, "name" => "MGMT", "subnet" => "10.10.10.0/24", "purpose" => "Switches, Firewall, iDRAC/iLO, AP-Management", "access" => "Nur IT-Admins per VPN/MFA"],
        ["id" => 20, "name" => "SERVER", "subnet" => "10.10.20.0/24", "purpose" => "Domaincontroller, DNS, DHCP, Fileserver, ERP, SQL, Backup", "access" => "Clients nur zu benoetigten Diensten"],
        ["id" => 30, "name" => "CLIENTS", "subnet" => "10.10.30.0/23", "purpose" => "Buerorechner, CAD, Verwaltung, Entwicklung", "access" => "Standard-Benutzer mit Internet und Serverfreigaben"],
        ["id" => 40, "name" => "VOICE", "subnet" => "10.10.40.0/24", "purpose" => "PoE-Telefone und 3CX/SIP", "access" => "QoS aktiv, nur Telefonanlage/SIP erlaubt"],
        ["id" => 50, "name" => "PRINTERS", "subnet" => "10.10.50.0/24", "purpose" => "Drucker, Scanner, Etikettendrucker", "access" => "Kein direkter Internetzugriff"],
        ["id" => 60, "name" => "WIFI-CORP", "subnet" => "10.10.60.0/24", "purpose" => "Firmengeraete ueber WLAN", "access" => "802.1X oder WPA Enterprise"],
        ["id" => 70, "name" => "GUEST", "subnet" => "10.10.70.0/24", "purpose" => "Gaeste-WLAN", "access" => "Nur Internet, isoliert vom LAN"],
        ["id" => 80, "name" => "OT", "subnet" => "10.10.80.0/22", "purpose" => "CNC, SPS, Roboter, Maschinen-PCs, IoT", "access" => "Strenge Firewall-Regeln zwischen IT und OT"],
        ["id" => 90, "name" => "BACKUP", "subnet" => "10.10.90.0/24", "purpose" => "Backupserver, Repository, Tape-Library, Replikation", "access" => "Nur Backup-Jobs und Admins"],
        ["id" => 100, "name" => "DMZ", "subnet" => "10.10.100.0/24", "purpose" => "Reverse Proxy, Webserver, externe Schnittstellen", "access" => "Getrennt vom internen Netz"]
    ];
}

function get_internet_design($company){
    $size = plan_size($company);
    $primary = $company["internet"];
    $bandwidth = $size === "large" ? "2 x 1-10 Gbit/s" : ($size === "medium" ? "1 Gbit/s symmetrisch" : "300-1000 Mbit/s");

    return [
        "recommendation" => $primary . " als primaerer Anschluss, LTE/5G oder DSL als Failover",
        "endpoint" => "Provider-APL/ONT im Serverraum oder im abschliessbaren Technikverteiler. Von dort per Glasfaser/Kupfer direkt zur Firewall.",
        "bandwidth" => $bandwidth,
        "router_config" => [
            "WAN-Failover mit Gateway-Gruppen und Monitoring-IP",
            "PPPoE/VLAN-ID des Providers dokumentieren",
            "DNS-Forwarder oder Resolver aktivieren, interne DNS-Zonen an Domaincontroller weiterleiten",
            "Keine Portfreigaben direkt ins LAN, externe Dienste nur ueber DMZ/Reverse Proxy/VPN",
            "Traffic Shaping fuer VoIP, Backup und Produktionsdaten planen",
            "Konfigurationsbackup nach jeder Aenderung exportieren"
        ],
        "learning" => "Internet endet technisch am APL/ONT. Die Sicherheitsgrenze beginnt erst an der Firewall, nicht am Provider-Router."
    ];
}

function get_network_hardware($company){
    $employees = $company["employees"];
    $buildings = $company["buildings"];
    $machine_ports = $company["cnc_machines"] + $company["robots"] + $company["sps"];
    $client_ports = (int)ceil($employees * 1.35);
    $phones = (int)ceil($employees * 0.65);
    $aps = max($buildings * 2, (int)ceil($employees / 25));
    $access_switches = max($buildings, (int)ceil(($client_ports + $phones + $machine_ports + $aps) / 40));

    return [
        "core" => [
            "device" => "2 x Layer-3 Core Switch 10/25G",
            "location" => "Serverraum Gebaeude 1",
            "cabling" => "Firewall, Server, Storage und Hauptverteilungen redundant anbinden",
            "config" => "Alle VLANs als Trunks, LACP-Uplinks, STP Root Primary/Secondary"
        ],
        "distribution" => [
            "device" => max(1, $buildings - 1) . " x Gebaeude-/Etagenverteiler",
            "location" => "Je Gebaeude oder Produktionshalle ein abschliessbarer Netzwerkschrank",
            "cabling" => "Glasfaser vom Core zum Verteiler, mindestens 2 Fasern je Pfad",
            "config" => "Trunk zum Core, nur benoetigte VLANs erlauben"
        ],
        "access" => [
            "device" => $access_switches . " x 48-Port PoE+ Access Switch",
            "location" => "Bueros, Produktion, Lager, Maschinenbereiche",
            "cabling" => "Endgeraete als Access Ports, APs/Telefone mit PoE, Maschinen getrennt im OT-VLAN",
            "config" => "Portprofile: Client, Voice+Client, Printer, OT, AP-Trunk"
        ],
        "wireless" => [
            "device" => $aps . " x Wi-Fi 6/6E Access Point",
            "location" => "Bueros, Besprechungsraeume, Lager, Produktionszonen nach Ausleuchtung",
            "cabling" => "Je AP ein Cat.6A-Kabel mit PoE+",
            "config" => "SSIDs auf VLANs mappen: Firma, Gast, optional Scanner/IoT"
        ],
        "endpoints" => [
            "clients" => $employees . " Hauptarbeitsplaetze plus " . (int)ceil($employees * 0.15) . " Reserve-/Werkstattgeraete",
            "phones" => $phones . " PoE-Telefone oder Softphone-Headsets",
            "printers" => max(2, (int)ceil($employees / 25)) . " Netzwerkdrucker/Scanner",
            "ot" => $machine_ports . " OT-Geraete fuer CNC, SPS, Roboter und Rueckmeldung"
        ]
    ];
}

function get_server_design($company){
    $size = plan_size($company);
    $hosts = $size === "large" ? 3 : 2;
    $ram = $size === "large" ? "384-768 GB RAM je Host" : "192-384 GB RAM je Host";
    $cpu = $size === "large" ? "2 CPUs oder 1 starke EPYC/Xeon CPU je Host" : "1-2 CPUs je Host";

    return [
        "platform" => $company["virtualization"],
        "physical_hosts" => $hosts . " Virtualisierungshosts im Serverraum",
        "host_specs" => $cpu . ", " . $ram . ", 10/25G Netzwerk, redundante Netzteile",
        "cluster" => [
            "Cluster mit Quorum, Management im VLAN 10",
            "VM-Netze als VLAN-aware Bridges/Portgroups",
            "Snapshots nur kurzzeitig fuer Updates, nicht als Backup-Ersatz",
            "Templates fuer Windows Server, Ubuntu Server und Debian LXC pflegen",
            "Live-Migration nur mit gemeinsamem Storage oder Replikation sauber planen"
        ],
        "vms" => [
            ["name" => "AD01/AD02", "role" => "Domaincontroller, DNS, DHCP, Gruppenrichtlinien", "os" => "Windows Server", "backup" => "System State + VM Backup"],
            ["name" => "FILE01", "role" => "SMB/DFS Datenserver, Abteilungsfreigaben", "os" => "Windows Server oder Samba", "backup" => "Taeiglich inkrementell, Versionierung"],
            ["name" => "ERP01", "role" => "ERP/Buchhaltung/Business Control", "os" => "Windows oder Linux je Hersteller", "backup" => "Applikationskonsistent + Datenbankdump"],
            ["name" => "SQL01", "role" => "SQL/PostgreSQL/MariaDB fuer ERP, Zeiterfassung, Schichtplan", "os" => "Windows Server oder Ubuntu", "backup" => "Transaktionslogs + Vollbackup"],
            ["name" => "VOIP01", "role" => "3CX Telefonanlage, SIP-Trunk, Nebenstellen", "os" => "Debian/Linux", "backup" => "3CX Config + VM Backup"],
            ["name" => "MON01", "role" => "Monitoring fuer Server, Switches, Firewall, OT", "os" => "Ubuntu/Debian", "backup" => "Config + Datenbank"],
            ["name" => "DOCKER01", "role" => "Container fuer interne Tools, Webapps, Entwicklung", "os" => "Ubuntu Server", "backup" => "Volumes + Compose/Git"],
            ["name" => "RDS01", "role" => "Terminalserver fuer Homeoffice und externe Firmen", "os" => "Windows Server", "backup" => "Profil- und VM-Backup"],
            ["name" => "BACKUP01", "role" => "Backup-Repository, Tape-Anbindung, Restore-Tests", "os" => "Linux oder Windows je Backupsoftware", "backup" => "Nicht Teil der Domain-Admins machen"],
            ["name" => "TEST01", "role" => "Spielwiese fuer Updates, Simulationen, neue Projekte", "os" => "Gemischt", "backup" => "Optional, getrennt von Produktion"]
        ]
    ];
}

function get_storage_design($company){
    $employees = $company["employees"];
    $usable_tb = max($company["data_tb"], max(12, (int)ceil(($employees * 0.18) + ($company["cnc_machines"] * 0.5) + ($company["growth_5y"] / 10))));

    return [
        "primary" => $usable_tb . " TB nutzbarer Produktivspeicher, erweiterbar um 50 Prozent",
        "raid" => "RAIDZ2/RAID6 fuer Kapazitaet oder RAID10 fuer hohe VM-IOPS",
        "nas" => "NAS/SAN im Serverraum, angebunden mit 10/25G, separates Storage- oder Backup-VLAN",
        "shares" => ["Abteilungen", "CAD/Engineering", "Produktion", "Scans", "ERP-Export", "Benutzerprofile", "Archiv"],
        "backup" => [
            "3-2-1-Regel: 3 Kopien, 2 Medien, 1 Kopie offline/offsite",
            "Disk-Repository fuer schnelle Restores",
            "Magnetband/LTO fuer Offline-Schutz gegen Ransomware und Langzeitarchiv",
            "Replikationsziel in zweitem Gebaeude oder externem Standort",
            "Monatlicher Restore-Test mit Protokoll"
        ]
    ];
}

function get_requirement_assessment($company){
    $availability_note = [
        "Basis" => "Einzelne Systeme duerfen kurz ausfallen. Ersatzteile und Backups sind wichtiger als Hochverfuegbarkeit.",
        "Business Standard" => "Firewall, Core, Server und Storage sollten redundant geplant werden. Das passt fuer die meisten Industriebetriebe.",
        "Hochverfuegbar" => "Redundante Firewall, Core-Switches, mehrere Hosts, Replikation, USV, Ersatzteile und klare Notfallprozesse sind Pflicht."
    ];

    $security_note = [
        "Normal" => "Grundschutz mit Firewall, Benutzergruppen, Backup, Updates und Virenschutz.",
        "Erhoeht" => "MFA, VLAN-Trennung, Monitoring, EDR, Admin-Konzept, Logging und regelmaessige Restore-Tests.",
        "Kritisch" => "Zero-Trust-Ansaetze, strenge OT-Trennung, SIEM/Logmanagement, PAM, Notfallhandbuch und externe Audits."
    ];

    return [
        ["topic" => "Verfuegbarkeit", "input" => $company["availability"], "meaning" => $availability_note[$company["availability"]] ?? $availability_note["Business Standard"], "needed" => "Entscheidet ueber HA-Firewall, Cluster, Ersatzteile, USV, Wartungsvertraege und Replikation."],
        ["topic" => "Sicherheitsniveau", "input" => $company["security_level"], "meaning" => $security_note[$company["security_level"]] ?? $security_note["Erhoeht"], "needed" => "Entscheidet ueber MFA, Segmentierung, EDR, Logging, Adminrechte, VPN-Regeln und OT-Schutz."],
        ["topic" => "Cloud-Strategie", "input" => $company["cloud_strategy"], "meaning" => "Legt fest, ob Dienste lokal, in Microsoft 365/Azure, in einer Private Cloud oder gemischt betrieben werden.", "needed" => "Entscheidet ueber Internet-Redundanz, Identitaet, Backup, Datenschutz und Lizenzmodell."],
        ["topic" => "IT-Betrieb", "input" => $company["it_operation"], "meaning" => "Klaert, wer Updates, Monitoring, Backup, Benutzer und Stoerungen betreut.", "needed" => "Entscheidet ueber Dokumentation, Fernwartung, Rollen, Adminzugriffe und Supportvertraege."],
        ["topic" => "Datenmenge", "input" => $company["data_tb"] . " TB aktuell", "meaning" => "Geschaetzte Nutzdaten ohne Backup-Kopien.", "needed" => "Entscheidet ueber Storage, NAS/SAN, Backupfenster, Replikation, Archiv und Wachstumspuffer."],
        ["topic" => "RTO", "input" => $company["rto_hours"] . " Stunden", "meaning" => "Maximale Zeit, bis ein Dienst nach Ausfall wieder laufen muss.", "needed" => "Kurzes RTO braucht HA, schnelle Restores, Ersatzhardware und klare Notfallplaene."],
        ["topic" => "RPO", "input" => $company["rpo_hours"] . " Stunden", "meaning" => "Maximaler Datenverlust in Zeit gemessen.", "needed" => "Kurzes RPO braucht haeufige Backups, Snapshots, Replikation oder Datenbank-Logbackups."],
        ["topic" => "WLAN-Flaeche", "input" => $company["wifi_area"] . " qm", "meaning" => "Grobe Flaeche fuer Buero, Lager und Produktion.", "needed" => "Entscheidet ueber AP-Anzahl, Ausleuchtung, PoE-Budget, Gastnetz und Roaming."],
        ["topic" => "OT-Kritikalitaet", "input" => $company["ot_criticality"], "meaning" => "Bewertet, wie stark Produktionsstillstand das Unternehmen trifft.", "needed" => "Entscheidet ueber Industrie-Switches, OT-Firewall-Regeln, Monitoring, Ersatzteile und Wartungsfenster."]
    ];
}

function get_input_guide(){
    return [
        ["field" => "Mitarbeiter", "meaning" => "Anzahl der Personen, die mit IT arbeiten.", "impact" => "Bestimmt Clients, Benutzerkonten, Lizenzen, Telefonie, WLAN-Dichte und Servergroesse."],
        ["field" => "Gebaeude", "meaning" => "Alle Standorte, Hallen oder Buerogebaeude auf dem Betriebsgelaende.", "impact" => "Bestimmt Hauptverteiler, Unterverteiler, Glasfaserstrecken, APs und Redundanzwege."],
        ["field" => "CNC, Roboter, SPS", "meaning" => "Produktionsgeraete und Steuerungen im OT-Netz.", "impact" => "Bestimmt OT-VLAN, Maschinen-Switches, Firewall-Regeln, MES/SQL-Anbindung und Monitoring."],
        ["field" => "Homeoffice Benutzer", "meaning" => "Personen, die von extern auf Firmenanwendungen zugreifen.", "impact" => "Bestimmt VPN-Kapazitaet, MFA, Terminalserver, Lizenzierung und Sicherheitsregeln."],
        ["field" => "Wachstum", "meaning" => "Reserve fuer die naechsten Jahre.", "impact" => "Verhindert, dass Switchports, Storage, Server-RAM und IP-Netze zu knapp geplant werden."],
        ["field" => "Internet", "meaning" => "Primaerer Anschluss des Unternehmens.", "impact" => "Bestimmt Bandbreite, Provider-Endpunkt, Failover, Firewall-WAN und SLA-Anforderungen."]
    ];
}

function get_glossary(){
    return [
        ["term" => "APL/ONT", "plain" => "Der physische Uebergabepunkt des Providers.", "use" => "Hier kommt DSL oder Glasfaser an. Von dort geht es zur Firewall oder zum Provider-Modem."],
        ["term" => "Firewall/Router", "plain" => "Das Sicherheits- und Routing-Geraet am Rand des Netzes.", "use" => "Trennt Internet, LAN, DMZ, VPN und VLANs. Hier werden Regeln, NAT, VPN und Failover eingestellt."],
        ["term" => "Core Switch", "plain" => "Der zentrale Hauptswitch im Serverraum.", "use" => "Alle groesseren Verbindungen laufen hier zusammen: Firewall, Server, Storage und Unterverteiler."],
        ["term" => "Access Switch", "plain" => "Switch fuer Endgeraete.", "use" => "Hier stecken PCs, Telefone, Drucker, Access Points und Maschinen-PCs."],
        ["term" => "VLAN", "plain" => "Ein logisch getrenntes Netzwerk auf derselben Switch-Infrastruktur.", "use" => "Damit Gaeste, Produktion, Server, Drucker und Benutzer nicht unkontrolliert miteinander sprechen."],
        ["term" => "Trunk Port", "plain" => "Ein Switchport, der mehrere VLANs transportiert.", "use" => "Zwischen Firewall, Core, Unterverteilern und Access Points."],
        ["term" => "Access Port", "plain" => "Ein Switchport fuer genau ein VLAN.", "use" => "Typisch fuer PCs, Drucker, Kameras, Telefone oder Maschinen."],
        ["term" => "PoE", "plain" => "Strom ueber Netzwerkkabel.", "use" => "Telefone, Access Points und Kameras brauchen dann kein separates Netzteil."],
        ["term" => "DMZ", "plain" => "Abgeschirmtes Netz fuer Dienste mit Kontakt nach aussen.", "use" => "Webserver, Reverse Proxy oder Schnittstellenserver werden dort platziert, nicht direkt im LAN."],
        ["term" => "USV", "plain" => "Unterbrechungsfreie Stromversorgung.", "use" => "Haelt Firewall, Switches, Server und Storage bei Stromausfall kurz am Leben und faehrt Systeme sauber herunter."],
        ["term" => "Monitoring", "plain" => "Automatische Ueberwachung der IT.", "use" => "Erkennt Ausfaelle, volle Festplatten, langsame Verbindungen, defekte Netzteile oder Backupfehler."],
        ["term" => "LTO/Magnetband", "plain" => "Offline-Backupmedium.", "use" => "Sehr wichtig gegen Ransomware, weil ein ausgelagertes Band nicht online verschluesselt werden kann."]
    ];
}

function get_monitoring_design($company){
    return [
        "system" => "Zabbix, Checkmk, PRTG oder Icinga als zentrale Ueberwachung",
        "server" => "MON01 im Server-VLAN, Benachrichtigung per Mail, Teams oder SMS-Gateway",
        "targets" => [
            "Firewall: WAN-Ausfall, VPN-Status, CPU, RAM, Paketverlust, Latenz",
            "Switches: Uplinks, Portfehler, PoE-Verbrauch, Temperatur, Netzteile",
            "Server/VMs: CPU, RAM, Storage, Dienste, Windows-Updates, Linux-Pakete",
            "Storage/NAS: RAID-Status, freie Kapazitaet, Snapshots, SMART-Werte",
            "Backup: erfolgreiche Jobs, letzte Restore-Tests, Repository-Fuellstand",
            "USV/Strom: Batterieladung, Laufzeit, Last, Stromausfall, Batterietest",
            "OT: Erreichbarkeit von Maschinen, SPS, Robotern und Rueckmelde-Terminals"
        ],
        "protocols" => [
            "SNMP fuer Switches, Firewall, USV, Drucker und viele Industriegeraete",
            "Agenten fuer Windows- und Linux-Server",
            "Syslog fuer Firewall-, Switch- und Serverereignisse",
            "ICMP/Ping fuer einfache Erreichbarkeitspruefung",
            "NetFlow/sFlow fuer Analyse, wer wie viel Netzwerkverkehr erzeugt"
        ],
        "alerts" => [
            "Kritisch: Internet down, Core Switch down, Storage voll, Backup fehlgeschlagen, USV auf Batterie",
            "Warnung: Festplatte unter 20 Prozent frei, hohe CPU, Paketfehler, Zertifikat laeuft ab",
            "Info: Geraet neu gestartet, Link gewechselt, neuer Client im Netz"
        ]
    ];
}

function get_power_design($company){
    $size = plan_size($company);
    $runtime = $size === "large" ? "30-60 Minuten fuer geordneten Shutdown" : "15-30 Minuten fuer geordneten Shutdown";

    return [
        "goal" => "Stromplanung verhindert harte Ausfaelle, Datenverlust und beschaedigte Storage-Systeme.",
        "runtime" => $runtime,
        "items" => [
            "Serverraum: getrennte Stromkreise fuer Rack A und Rack B",
            "USV fuer Firewall, Core Switches, Server, Storage, Backupserver und Monitoring",
            "PoE-Budget pro Switch berechnen: Telefone, APs und Kameras ziehen Strom aus dem Switch",
            "USV per USB/SNMP an Monitoring und Virtualisierung anbinden",
            "Automatischer Shutdown: erst VMs, dann Hosts, zuletzt Storage",
            "Temperatur, Luftstrom und Klima im Serverraum ueberwachen",
            "Jaehrlicher Batterietest und Dokumentation der Steckdosen/Rack-PDUs"
        ]
    ];
}

function get_topology_map($company){
    $network = get_network_hardware($company);

    return [
        ["node" => "Provider", "type" => "WAN", "location" => "Hausanschluss / Technikraum", "connects" => "APL/ONT zu Firewall WAN1/WAN2", "check" => "SLA, Zugangsdaten, VLAN-ID, feste IPs dokumentieren"],
        ["node" => "Firewall HA", "type" => "Security", "location" => "Serverraum Rack 1/2", "connects" => "WAN, Core Switches, DMZ, VPN", "check" => "Regeln, NAT, VPN, DNS, DHCP Relay, Konfigbackup"],
        ["node" => "Core Switch Stack", "type" => "Backbone", "location" => "Serverraum", "connects" => "Firewall, Server, Storage, Unterverteiler", "check" => "LACP, STP, VLAN-Trunks, 10/25G Uplinks"],
        ["node" => "Server Cluster", "type" => "Compute", "location" => "Serverrack", "connects" => "Core, Storage, Backup", "check" => "Management, VM-Netze, Replikation, Snapshots, Templates"],
        ["node" => "Storage/NAS", "type" => "Data", "location" => "Serverrack", "connects" => "Server Cluster und Backupnetz", "check" => "RAID, Snapshots, Quotas, Freigaben, Kapazitaet"],
        ["node" => "Backup/Tape", "type" => "Recovery", "location" => "Serverraum und Offsite", "connects" => "Backup VLAN, Tape-Auslagerung", "check" => "3-2-1, Offline-Medien, Restore-Test"],
        ["node" => "UV Buero", "type" => "Access", "location" => "Etagenverteiler/Bueros", "connects" => $network["access"]["device"], "check" => "Client-, Voice-, Printer- und AP-Portprofile"],
        ["node" => "UV Produktion", "type" => "OT", "location" => "Produktionshalle", "connects" => "OT-Switches zu CNC, SPS, Robotern", "check" => "OT-VLAN, Industrie-Switches, nur erlaubte Serververbindungen"],
        ["node" => "WLAN", "type" => "Wireless", "location" => "Bueros, Lager, Halle", "connects" => $network["wireless"]["device"], "check" => "Ausleuchtung, SSID zu VLAN, Gastnetz isolieren"],
        ["node" => "Monitoring", "type" => "Operations", "location" => "MON01 im Servernetz", "connects" => "SNMP, Agenten, Syslog, Backupmeldungen", "check" => "Alarmwege testen und Verantwortliche eintragen"]
    ];
}

function get_campus_map($company){
    return [
        ["area" => "Gebaeude 1", "title" => "Zentrale / Serverraum", "items" => "Provider-Endpunkt, Firewall, Core Switches, Server, Storage, Backup, Monitoring", "vlans" => "MGMT, SERVER, CLIENTS, VOICE, BACKUP, DMZ"],
        ["area" => "Gebaeude 2", "title" => "Produktion / OT", "items" => "CNC, SPS, Roboter, Rueckmeldung, Industrie-Switches, optionale MES-Terminals", "vlans" => "OT, MGMT, optional WIFI-CORP"],
        ["area" => "Bueros", "title" => "Arbeitsplaetze", "items" => "PCs, Notebooks, Telefone, Drucker, Access Points, Besprechungsraeume", "vlans" => "CLIENTS, VOICE, PRINTERS, WIFI-CORP"],
        ["area" => "Lager / Versand", "title" => "Mobile Geraete", "items" => "Scanner, Etikettendrucker, WLAN, Terminals, Kameras nach Bedarf", "vlans" => "CLIENTS, PRINTERS, WIFI-CORP, optional OT"],
        ["area" => "Extern", "title" => "Homeoffice / Dienstleister", "items" => "VPN, MFA, Terminalserver, eingeschraenkte Zugriffe, Ablaufdatum fuer externe Konten", "vlans" => "VPN-Zonen, SERVER nur ueber Regeln"]
    ];
}

function get_rack_layout($company){
    return [
        ["unit" => "U42-U39", "device" => "Patchfelder Kupfer/Glasfaser", "purpose" => "Alle Netzwerkdosen und Gebaeudeverbindungen sauber auflegen und beschriften."],
        ["unit" => "U38-U36", "device" => "Core Switch A/B", "purpose" => "Zentrale Netzwerkverteilung, VLAN-Trunks, Server- und Firewall-Anbindung."],
        ["unit" => "U35-U34", "device" => "Firewall A/B + Provider-Uebergabe", "purpose" => "Internet, VPN, Routing, Sicherheitsregeln und WAN-Failover."],
        ["unit" => "U33-U28", "device" => "Virtualisierungshosts", "purpose" => "Proxmox/VMware/Hyper-V Cluster fuer Serverrollen und Dienste."],
        ["unit" => "U27-U23", "device" => "Storage / NAS / SAN", "purpose" => "Produktivdaten, VM-Storage, Snapshots und Freigaben."],
        ["unit" => "U22-U18", "device" => "Backup Repository / LTO", "purpose" => "Schnelle Restores, Tape-Auslagerung, Offline-Schutz."],
        ["unit" => "U17-U15", "device" => "Monitoring / Management", "purpose" => "MON01, KVM, Konsolenzugriff, Admin-Arbeitsplatz nach Bedarf."],
        ["unit" => "U14-U1", "device" => "USV, PDUs, Reserve", "purpose" => "Stromversorgung, geordneter Shutdown, Platz fuer Erweiterung."]
    ];
}

function get_data_flow_map(){
    return [
        ["flow" => "Benutzer -> Anwendung", "path" => "Client oder VPN -> Firewall-Regel -> Server-VLAN -> Anwendung/Dateifreigabe", "risk" => "Zu breite Freigaben oder direkte Adminrechte vermeiden."],
        ["flow" => "Produktion -> Datenbank", "path" => "CNC/SPS/Roboter -> OT-Switch -> Firewall -> MES/SQL/Fileserver", "risk" => "OT darf nur zu definierten Servern und Ports sprechen."],
        ["flow" => "Telefonie", "path" => "PoE-Telefon -> Voice VLAN -> 3CX/SIP-Trunk -> Provider", "risk" => "QoS, Notruf, SIP-Sicherheit und VLAN-Trennung beachten."],
        ["flow" => "Backup", "path" => "VM/Server -> Backup VLAN -> Repository -> Tape/Replikationsziel", "risk" => "Backupserver nicht als normales Domain-Admin-System betreiben."],
        ["flow" => "Monitoring", "path" => "MON01 -> SNMP/Agent/Syslog -> Firewall, Switches, Server, USV, Storage", "risk" => "Monitoring braucht Leserechte, aber keine unnoetigen Adminrechte."],
        ["flow" => "Gast WLAN", "path" => "Gastgeraet -> Guest VLAN -> Firewall -> Internet", "risk" => "Keine Route in interne Netze erlauben."]
    ];
}

function get_missing_planning_checklist(){
    return [
        ["area" => "Dokumentation", "items" => "IP-Plan, VLAN-Plan, Portliste, Rackplan, Patchplan, Lizenzliste, Admin-Konten, Notfallkontakte."],
        ["area" => "Sicherheit", "items" => "MFA, Passwortregeln, Admin-Trennung, EDR, Patchmanagement, Firewall-Regeln, VPN-Gruppen, Logging."],
        ["area" => "Notfall", "items" => "Restore-Anleitung, Ersatzhardware, Offline-Backups, USV-Test, Kontaktliste, Wiederanlaufreihenfolge."],
        ["area" => "Datenschutz", "items" => "Personenbezogene Daten, Aufbewahrung, Zugriffsrechte, externe Dienstleister, AV-Vertraege, Loeschkonzept."],
        ["area" => "Betrieb", "items" => "Wartungsfenster, Updateplan, Monitoring-Alarme, Verantwortliche, Ticketprozess, Inventar."],
        ["area" => "OT / Produktion", "items" => "Maschinenfreigaben, Wartungszugriffe, Hersteller-VPN, Ersatz-Switches, Protokolle wie OPC UA/Modbus, Segmentierung."],
        ["area" => "Erweiterung", "items" => "Freie Switchports, Rackplatz, IP-Reserve, Storage-Wachstum, Glasfaserreserve, Lizenzpuffer."]
    ];
}

function get_decision_cards($company){
    return [
        ["title" => "Open Source oder kommerziell?", "recommendation" => $company["security_level"] === "Kritisch" ? "Open Source mit Supportvertrag oder kommerzielle Enterprise-Loesung waehlen." : "Open Source ist gut geeignet, wenn Know-how und Dokumentation vorhanden sind.", "why" => "Kosten sind nicht nur Lizenzpreise. Betrieb, Support, Updates und Verantwortung zaehlen genauso."],
        ["title" => "Cloud oder lokal?", "recommendation" => $company["cloud_strategy"] === "Cloud first" ? "Identitaet, Mail, Kollaboration und Backup stark cloudnah planen." : "Hybrid ist fuer Industrie oft sinnvoll: kritische Produktion lokal, Mail/Kollaboration in der Cloud.", "why" => "Produktion braucht oft lokale Verfuegbarkeit, waehrend Mail und Zusammenarbeit gut in der Cloud laufen."],
        ["title" => "Ein Server oder Cluster?", "recommendation" => $company["availability"] === "Basis" ? "Mindestens guter Backup- und Ersatzteilplan; Cluster optional." : "Mindestens 2 Hosts, besser 3 bei hoher Verfuegbarkeit.", "why" => "Ein einzelner Host ist guenstiger, aber ein Hardwarefehler kann viele Dienste gleichzeitig stoppen."],
        ["title" => "Flaches Netz oder VLANs?", "recommendation" => "VLANs einplanen, auch bei kleinen Umgebungen.", "why" => "Saubere Trennung reduziert Risiko, macht Regeln nachvollziehbar und erleichtert spaetere Erweiterungen."]
    ];
}

function get_implementation_steps(){
    return [
        ["phase" => "1. Aufnahme", "tasks" => "Gebaeude, Raeume, Maschinen, Benutzer, Drucker, Telefone, Provider, vorhandene Kabel und Schaltschranke aufnehmen."],
        ["phase" => "2. IP- und VLAN-Plan", "tasks" => "Subnetze festlegen, VLAN-Namen vergeben, Gateway je VLAN bestimmen, DHCP-Bereiche und Reservierungen planen."],
        ["phase" => "3. Rack und Verkabelung", "tasks" => "Serverraum, Patchfelder, Core, Unterverteiler, Glasfaserstrecken, Portnummern und Dosen beschriften."],
        ["phase" => "4. Grundsysteme", "tasks" => "Firewall, Core Switches, Virtualisierung, Storage, Domaincontroller, DNS, DHCP und Backup einrichten."],
        ["phase" => "5. Dienste", "tasks" => "Fileserver, ERP, SQL, Telefonanlage, Druckserver, Terminalserver, Monitoring und Entwicklungsumgebung bereitstellen."],
        ["phase" => "6. Sicherheit", "tasks" => "Firewall-Regeln, MFA, VPN, Benutzergruppen, GPOs, Admin-Konten, Updates und Logging konfigurieren."],
        ["phase" => "7. Test und Betrieb", "tasks" => "Restore-Test, Stromausfalltest, VPN-Test, VLAN-Test, Monitoring-Alarme und Dokumentation pruefen."]
    ];
}

function get_security_and_admin($company){
    return [
        "identity" => [
            "Active Directory mit zwei Domaincontrollern",
            "Gruppen nach Rollen: Verwaltung, Produktion, IT, Entwicklung, Externe",
            "GPOs fuer BitLocker, Firewall, Updates, Laufwerkszuordnung, Drucker, Passwort/MFA",
            "Keine normalen Benutzer als lokale Administratoren"
        ],
        "vpn" => [
            "WireGuard, IPsec oder SSL-VPN auf der Firewall",
            "MFA fuer Homeoffice und externe Firmen",
            "Externe nur in eigene VPN-Gruppe mit Ablaufdatum",
            "RDS/Terminalserver statt direktem Zugriff auf interne PCs"
        ],
        "firewall_rules" => [
            "Clients duerfen zu Serverdiensten, aber nicht frei ins Servernetz",
            "OT darf nur zu definierten Servern wie MES, Historian, Update-Proxy",
            "Guest VLAN nur Internet",
            "Backup VLAN nur von Backupservern erreichbar",
            "DMZ trennt Web/Proxy/Schnittstellen vom LAN"
        ]
    ];
}

function get_software_costing($company){
    $employees = $company["employees"];
    $homeoffice = $company["homeoffice_users"];
    $server_design = get_server_design($company);
    $vm_count = count($server_design["vms"]);

    $items = [
        ["area" => "Microsoft 365 / Office", "model" => "Abo pro Benutzer/Monat", "qty" => $employees, "unit" => 12 * 12, "open_source" => "Nein", "note" => "E-Mail, Office Apps, Teams; je nach Tarif deutlich variabel"],
        ["area" => "Windows Server", "model" => "Serverlizenz + CALs", "qty" => 2, "unit" => 950, "open_source" => "Nein", "note" => "Domaincontroller, RDS, Fileserver oder Spezialsoftware"],
        ["area" => "Windows Server CALs", "model" => "Zugriffslizenz pro Benutzer", "qty" => $employees, "unit" => 45, "open_source" => "Nein", "note" => "Noetig fuer Windows Server Dienste"],
        ["area" => "RDS CALs", "model" => "Remote Desktop pro Homeoffice-Benutzer", "qty" => $homeoffice, "unit" => 110, "open_source" => "Nein", "note" => "Nur wenn Terminalserver genutzt wird"],
        ["area" => "Backupsoftware", "model" => "Pro VM/Socket/Workload", "qty" => $vm_count, "unit" => 180, "open_source" => "Teilweise", "note" => "Beispiele: Veeam, Nakivo, Proxmox Backup Server"],
        ["area" => "Monitoring", "model" => "Open Source oder Sensor-Lizenz", "qty" => 1, "unit" => 0, "open_source" => "Ja", "note" => "Zabbix, Icinga und Checkmk Raw sind typische Startpunkte"],
        ["area" => "Firewall Software", "model" => "Open Source oder Subscription", "qty" => 2, "unit" => $company["firewall"] === "OPNsense" ? 0 : 900, "open_source" => $company["firewall"] === "OPNsense" ? "Ja" : "Nein", "note" => "OPNsense ist frei nutzbar; Enterprise-Firewalls haben Support/Subscriptions"],
        ["area" => "Virtualisierung", "model" => "Open Source, Support oder Subscription", "qty" => 2, "unit" => $company["virtualization"] === "Proxmox VE" ? 350 : 1600, "open_source" => $company["virtualization"] === "Proxmox VE" ? "Ja" : "Nein", "note" => "Proxmox kann frei genutzt werden, Support-Subscription ist separat"],
        ["area" => "3CX / Telefonanlage", "model" => "Simultane Calls oder Benutzer", "qty" => max(1, (int)ceil($employees / 25)), "unit" => 350, "open_source" => "Nein", "note" => "Alternativen: Starface, FreePBX, Teams Phone"],
        ["area" => "ERP / Buchhaltung", "model" => "Projekt- und Benutzerlizenz", "qty" => max(10, (int)ceil($employees * 0.25)), "unit" => 450, "open_source" => "Teilweise", "note" => "Odoo Community ist Open Source, viele ERP-Systeme sind lizenzpflichtig"],
        ["area" => "Datenbank", "model" => "Open Source oder Server/Core-Lizenz", "qty" => 1, "unit" => 0, "open_source" => "Ja", "note" => "PostgreSQL/MariaDB frei; Microsoft SQL Server je Edition kostenpflichtig"],
        ["area" => "Endpoint Security", "model" => "Pro Client/Jahr", "qty" => $employees, "unit" => 45, "open_source" => "Nein", "note" => "Virenschutz, EDR, Webschutz, zentrale Verwaltung"]
    ];

    $total = 0;
    foreach($items as $item){
        $total += $item["qty"] * $item["unit"];
    }

    return ["items" => $items, "total" => $total];
}

function get_provider_catalog(){
    return [
        ["category" => "Firewall / Router", "hardware" => "Beispiele: Deciso OPNsense Appliance, Thomas-Krenn LES, Protectli Vault, Netgate, Fortinet FortiGate 60F/100F, Palo Alto PA-400, Sophos XGS", "software" => "OPNsense, pfSense, FortiOS, PAN-OS, Sophos Firewall", "open_source" => "OPNsense und pfSense Community sind Open Source bzw. frei nutzbar"],
        ["category" => "Core Switches", "hardware" => "Beispiele: HPE Aruba CX 6300/6200, Cisco Catalyst 9300, Dell PowerSwitch N/S-Serie, MikroTik CRS, Ubiquiti Enterprise XG", "software" => "ArubaOS-CX, Cisco IOS-XE, Dell OS10, RouterOS/SwitchOS, UniFi Network", "open_source" => "Firmware meist proprietaer; Standards wie VLAN, LACP, SNMP sind herstelleruebergreifend"],
        ["category" => "Access Switches / PoE", "hardware" => "Beispiele: Aruba 6100/6200, Cisco Catalyst 9200, Ubiquiti UniFi Pro/Enterprise, TP-Link Omada JetStream, Netgear M4300", "software" => "UniFi, Omada, Aruba Central, Cisco DNA/Catalyst Center, lokale Web/CLI-Verwaltung", "open_source" => "Controller teils kostenlos, Enterprise-Cloudfunktionen oft lizenzpflichtig"],
        ["category" => "WLAN", "hardware" => "Beispiele: Ubiquiti U6/U7, Aruba AP-5xx/6xx, Cisco Catalyst/Meraki, TP-Link Omada EAP, Ruckus R-Serie", "software" => "UniFi, Omada, Aruba Central, Cisco Meraki/Catalyst, Ruckus SmartZone", "open_source" => "Controller oft kostenlos oder cloudbasiert; genaue Lizenzierung pruefen"],
        ["category" => "Server", "hardware" => "Beispiele: Dell PowerEdge R550/R650, HPE ProLiant DL360/DL380, Lenovo ThinkSystem SR, Supermicro, Thomas-Krenn", "software" => "iDRAC, iLO, XClarity, IPMI, Hersteller-Supporttools", "open_source" => "Management-Firmware proprietaer, Linux als Server-OS Open Source"],
        ["category" => "Storage / NAS", "hardware" => "Beispiele: Synology RackStation, QNAP QuTS hero, TrueNAS Mini/R-Series, Dell PowerVault, HPE MSA, Lenovo DE", "software" => "TrueNAS SCALE, Synology DSM, QTS/QuTS, Windows Server Storage, Ceph/ZFS", "open_source" => "TrueNAS SCALE, ZFS, Ceph und Samba sind Open Source"],
        ["category" => "Virtualisierung", "hardware" => "Beispiele: 2-3 Server mit ECC-RAM, NVMe/SSD, 10/25G, redundanten Netzteilen", "software" => "Proxmox VE, VMware vSphere, Hyper-V, XCP-ng, Nutanix AHV", "open_source" => "Proxmox VE und XCP-ng sind Open Source bzw. frei nutzbar"],
        ["category" => "Backup", "hardware" => "Beispiele: HPE StoreEver LTO, Quantum Scalar, Dell LTO, Synology/QNAP Backup-NAS, dedizierter Repository-Server", "software" => "Veeam, Nakivo, Proxmox Backup Server, Bareos, Bacula, Acronis", "open_source" => "Proxmox Backup Server, Bareos und Bacula sind Open Source"],
        ["category" => "Monitoring", "hardware" => "Beispiele: VM MON01, kleiner Management-Server, LTE/SMS-Gateway, Sensoren fuer Temperatur/USV", "software" => "Zabbix, Checkmk, PRTG, Icinga, Grafana, Prometheus, Graylog", "open_source" => "Zabbix, Icinga, Grafana, Prometheus und Graylog Open sind Open Source"],
        ["category" => "Identity / Benutzer", "hardware" => "Virtualisierte Domaincontroller, optional Cloud Identity", "software" => "Active Directory, Samba AD, Microsoft Entra ID, Keycloak", "open_source" => "Samba AD und Keycloak sind Open Source, Microsoft AD/Entra lizenzpflichtig"],
        ["category" => "Endpoint / Clients", "hardware" => "Beispiele: Dell OptiPlex/Latitude, HP EliteDesk/EliteBook, Lenovo ThinkCentre/ThinkPad, Fujitsu Esprimo", "software" => "Windows 11 Pro, Ubuntu Desktop, Intune, Baramundi, opsi, Microsoft Defender, Sophos, ESET", "open_source" => "Ubuntu Desktop und opsi sind Open Source bzw. frei nutzbar"],
        ["category" => "ERP / Datenbanken", "hardware" => "VMs oder dedizierter Datenbankserver mit schnellem Storage", "software" => "Odoo, SAP Business One, Microsoft Dynamics, Sage, PostgreSQL, MariaDB, MS SQL", "open_source" => "Odoo Community, PostgreSQL und MariaDB sind Open Source"],
        ["category" => "Telefonie", "hardware" => "Beispiele: Yealink, Snom, Grandstream, Fanvil, Jabra/Poly Headsets", "software" => "3CX, Starface, FreePBX, Teams Phone, Sipgate/Placetel", "open_source" => "FreePBX/Asterisk sind Open Source, viele Cloud-PBX-Systeme lizenzpflichtig"],
        ["category" => "OT / Industrie", "hardware" => "Beispiele: Siemens Scalance, Hirschmann, Phoenix Contact, Moxa, Wago, Advantech", "software" => "SCADA/MES, OPC UA Gateways, Node-RED, Kepware, Ignition", "open_source" => "Node-RED ist Open Source; Industrie-SCADA meist lizenzpflichtig"]
    ];
}

function get_open_source_strategy(){
    return [
        ["area" => "Sehr gut geeignet", "items" => "OPNsense, Proxmox VE, Linux Server, PostgreSQL, MariaDB, Zabbix, Grafana, Proxmox Backup Server, Samba, Docker"],
        ["area" => "Mit Support pruefen", "items" => "Proxmox Subscription, OPNsense Business Support, TrueNAS Enterprise, Checkmk Enterprise, kommerzielle Linux-Supportvertraege"],
        ["area" => "Oft lizenzpflichtig", "items" => "Microsoft 365, Windows Server, RDS, VMware, Enterprise-Firewalls, ERP, CAD, Endpoint Security, Telefonanlage"],
        ["area" => "Entscheidungsregel", "items" => "Open Source spart Lizenzkosten, braucht aber Know-how. Kritische Systeme sollten Support, Dokumentation und klare Verantwortliche haben."]
    ];
}

function get_costing($company){
    $network = get_network_hardware($company);
    $server_hosts = plan_size($company) === "large" ? 3 : 2;
    $access_switch_count = (int)$network["access"]["device"];
    $ap_count = (int)$network["wireless"]["device"];

    $items = [
        ["area" => "Firewall/Router", "qty" => 2, "unit" => 1800, "note" => "HA-Paar oder Appliance + Ersatz"],
        ["area" => "Core Switches", "qty" => 2, "unit" => 3500, "note" => "10/25G, redundant"],
        ["area" => "Access Switches PoE", "qty" => $access_switch_count, "unit" => 1200, "note" => "48-Port fuer Clients, Telefone, APs"],
        ["area" => "Access Points", "qty" => $ap_count, "unit" => 250, "note" => "Wi-Fi 6/6E"],
        ["area" => "Virtualisierungshosts", "qty" => $server_hosts, "unit" => 9000, "note" => "RAM, CPU, 10/25G, Support"],
        ["area" => "Storage/NAS", "qty" => 1, "unit" => 12000, "note" => "Produktivdaten und VM-Storage"],
        ["area" => "Backup + Tape", "qty" => 1, "unit" => 9000, "note" => "Repository, LTO, Medien"],
        ["area" => "Clients", "qty" => $company["employees"], "unit" => 950, "note" => "Windows/Ubuntu Arbeitsplatz"],
        ["area" => "Telefone/Headsets", "qty" => (int)ceil($company["employees"] * 0.65), "unit" => 130, "note" => "PoE-Telefone oder Headsets"]
    ];

    $total = 0;
    foreach($items as $item){
        $total += $item["qty"] * $item["unit"];
    }

    return ["items" => $items, "total" => $total];
}

function get_total_costing($company){
    $hardware = get_costing($company);
    $software = get_software_costing($company);

    return [
        "hardware" => $hardware["total"],
        "software" => $software["total"],
        "total" => $hardware["total"] + $software["total"]
    ];
}

function get_topology_nodes($company){
    return [
        ["Internet", "APL/ONT", "Provider-Endpunkt: DSL, Glasfaser oder SD-WAN"],
        ["Firewall", $company["firewall"], "Routing, VPN, VLAN-Gateways, Regeln, Failover"],
        ["Core", "Core Switch Stack", "Zentrale Verteilung im Serverraum"],
        ["Server", $company["virtualization"], "Cluster, VMs, Storage, Backup"],
        ["Bueros", "Access Switches", "Clients, Telefone, Drucker, WLAN"],
        ["Produktion", "OT Switches", "CNC, SPS, Roboter, Rueckmeldung"],
        ["Backup", "Backup/Tape/Replica", "Offline- und Offsite-Schutz"]
    ];
}
?>
