<?php

function h($value){
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function selected($actual, $expected){
    return $actual === $expected ? " selected" : "";
}

function render_bullets($items){
    echo "<ul>";
    foreach($items as $item){
        echo "<li>" . h($item) . "</li>";
    }
    echo "</ul>";
}

function render_term_table($items){
    echo "<table>";
    echo "<tr><th>Begriff</th><th>Einfach erklaert</th><th>Anwendung</th></tr>";
    foreach($items as $item){
        echo "<tr><td>" . h($item["term"]) . "</td><td>" . h($item["plain"]) . "</td><td>" . h($item["use"]) . "</td></tr>";
    }
    echo "</table>";
}

function render_dashboard(){
    $company = get_company();
    $internet = get_internet_design($company);
    $network = get_network_hardware($company);
    $vlans = get_vlans($company);
    $server_design = get_server_design($company);
    $storage = get_storage_design($company);
    $security = get_security_and_admin($company);
    $costing = get_costing($company);
    $software_costing = get_software_costing($company);
    $total_costing = get_total_costing($company);
    $providers = get_provider_catalog();
    $open_source = get_open_source_strategy();
    $topology = get_topology_nodes($company);
    $requirements = get_requirement_assessment($company);
    $input_guide = get_input_guide();
    $glossary = get_glossary();
    $monitoring = get_monitoring_design($company);
    $power = get_power_design($company);
    $topology_map = get_topology_map($company);
    $campus_map = get_campus_map($company);
    $rack_layout = get_rack_layout($company);
    $data_flows = get_data_flow_map();
    $missing_checklist = get_missing_planning_checklist();
    $decision_cards = get_decision_cards($company);
    $steps = get_implementation_steps();
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>IT Architektur Engine</title>

<style>
:root{
    --bg:#f5f7fb;
    --panel:#ffffff;
    --ink:#172033;
    --muted:#657086;
    --line:#dfe5ee;
    --blue:#1769aa;
    --cyan:#0f8b8d;
    --green:#1f7a5c;
    --orange:#a75f11;
    --dark:#142236;
    --dark2:#0d1727;
}

*{box-sizing:border-box}

body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background:var(--bg);
    color:var(--ink);
}

.layout{
    display:grid;
    grid-template-columns:320px minmax(0, 1fr);
    min-height:100vh;
}

.sidebar{
    background:linear-gradient(180deg, var(--dark2), var(--dark));
    color:#fff;
    padding:22px;
    position:sticky;
    top:0;
    height:100vh;
    overflow:auto;
}

.brand{
    margin:0 0 18px;
    font-size:22px;
    letter-spacing:0;
}

.brand-logo{
    display:block;
    width:100%;
    max-height:92px;
    object-fit:contain;
    margin:0 0 16px;
    padding:10px;
    border:1px solid rgba(255,255,255,.16);
    border-radius:14px;
    background:linear-gradient(135deg, rgba(255,255,255,.12), rgba(255,255,255,.04));
    box-shadow:0 14px 34px rgba(0,0,0,.28);
}

.sidebar p{
    color:#c7d0df;
    line-height:1.45;
    margin:0 0 18px;
}

label{
    display:block;
    font-size:13px;
    color:#dce6f4;
    margin:12px 0 5px;
}

input,select{
    width:100%;
    padding:10px;
    border:1px solid #40536c;
    border-radius:6px;
    background:#0f1b2d;
    color:#fff;
}

button{
    width:100%;
    margin-top:18px;
    padding:12px;
    background:#2f80c9;
    color:#fff;
    border:0;
    border-radius:6px;
    font-weight:700;
    cursor:pointer;
}

.secondary-button{
    display:block;
    width:100%;
    margin-top:10px;
    padding:11px;
    border:1px solid #60728d;
    border-radius:6px;
    background:#172842;
    color:#fff;
    text-align:center;
    text-decoration:none;
    cursor:pointer;
}

.section-header .secondary-button{
    width:auto;
    min-width:160px;
    margin-top:0;
    background:#1769aa;
    border-color:#1769aa;
}

.quick-nav{
    display:grid;
    gap:6px;
    margin:18px 0;
}

.quick-nav a{
    color:#dce6f4;
    text-decoration:none;
    border:1px solid #2d4059;
    border-radius:6px;
    padding:8px 10px;
    background:#132238;
    font-size:13px;
}

.main{
    padding:28px;
}

.hero{
    background:
        radial-gradient(circle at top right, rgba(15,139,141,.16), transparent 34%),
        linear-gradient(135deg, #ffffff 0%, #eef5fb 100%);
    border-bottom:1px solid var(--line);
    padding:30px 28px;
}

.hero h1{
    margin:0;
    font-size:34px;
    letter-spacing:0;
}

.hero p{
    max-width:980px;
    color:var(--muted);
    line-height:1.5;
    margin:10px 0 0;
}

.metrics{
    display:grid;
    grid-template-columns:repeat(4, minmax(0, 1fr));
    gap:12px;
    margin-top:20px;
}

.metric{
    background:#fff;
    border:1px solid var(--line);
    border-radius:8px;
    padding:13px;
}

.metric.accent{
    border-top:4px solid var(--cyan);
}

.metric strong{
    display:block;
    font-size:22px;
}

.metric span{
    color:var(--muted);
    font-size:13px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:18px;
}

.grid-three{
    display:grid;
    grid-template-columns:repeat(3, minmax(0, 1fr));
    gap:14px;
}

.map-grid{
    display:grid;
    grid-template-columns:repeat(5, minmax(180px, 1fr));
    gap:12px;
    overflow:auto;
    padding-bottom:4px;
}

.map-zone{
    border:1px solid var(--line);
    border-top:4px solid var(--cyan);
    border-radius:8px;
    background:#fbfdff;
    padding:14px;
    min-height:190px;
}

.map-zone strong,
.rack-row strong,
.flow-card strong{
    display:block;
    margin-bottom:6px;
}

.map-zone small{
    color:var(--muted);
    font-weight:700;
}

.rack{
    display:grid;
    gap:6px;
}

.rack-row{
    display:grid;
    grid-template-columns:95px minmax(160px, 1fr) minmax(260px, 2fr);
    gap:10px;
    align-items:center;
    border:1px solid var(--line);
    border-left:5px solid var(--blue);
    border-radius:6px;
    background:#fbfdff;
    padding:10px;
}

.rack-unit{
    font-weight:700;
    color:#175986;
}

.flow-grid{
    display:grid;
    grid-template-columns:repeat(3, minmax(220px, 1fr));
    gap:12px;
}

.flow-card{
    border:1px solid var(--line);
    border-radius:8px;
    background:#fff;
    padding:14px;
}

.flow-path{
    margin:8px 0;
    padding:9px;
    border-radius:6px;
    background:#eef5fb;
    color:#1d4d75;
}

.section{
    background:var(--panel);
    border:1px solid var(--line);
    border-radius:8px;
    padding:18px;
    margin-bottom:18px;
    overflow-x:auto;
}

.section-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:16px;
    margin-bottom:10px;
}

.section-header p{
    margin:0;
    max-width:760px;
}

.section h2{
    margin:0 0 10px;
    font-size:21px;
}

.section h3{
    margin:16px 0 8px;
    font-size:16px;
}

.section p,.section li{
    color:#344052;
    line-height:1.45;
}

ul{
    padding-left:19px;
    margin:8px 0 0;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
    font-size:14px;
}

th,td{
    border-bottom:1px solid var(--line);
    padding:9px 8px;
    text-align:left;
    vertical-align:top;
}

th{
    color:#44516a;
    background:#f7f9fc;
}

.topology{
    display:grid;
    grid-template-columns:repeat(7, minmax(120px, 1fr));
    gap:10px;
    overflow:auto;
    padding-bottom:6px;
}

.node{
    min-height:118px;
    border:1px solid var(--line);
    border-top:4px solid var(--blue);
    border-radius:8px;
    padding:12px;
    background:#fff;
    position:relative;
}

.node:after{
    content:"";
    position:absolute;
    right:-11px;
    top:50%;
    width:12px;
    border-top:2px solid #9aa8bb;
}

.node:last-child:after{display:none}

.node strong{
    display:block;
    margin-bottom:5px;
}

.node span{
    display:block;
    color:var(--blue);
    font-weight:700;
    margin-bottom:7px;
}

.note{
    border-left:4px solid var(--green);
    background:#eff8f4;
    padding:11px 13px;
    color:#254c3d;
    border-radius:6px;
}

.mini-card{
    border:1px solid var(--line);
    border-radius:8px;
    padding:14px;
    background:#fff;
}

.mini-card strong{
    display:block;
    margin-bottom:7px;
}

.badge{
    display:inline-block;
    padding:4px 7px;
    border-radius:5px;
    background:#eef5fb;
    color:#175986;
    font-weight:700;
    font-size:12px;
}

.pill-row{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
}

.pill{
    border:1px solid var(--line);
    border-radius:999px;
    padding:6px 10px;
    background:#f8fbff;
    color:#334052;
    font-size:13px;
}

.cost-total{
    display:inline-block;
    margin-top:12px;
    padding:10px 12px;
    border-radius:6px;
    background:#fff4e7;
    color:#6a3b08;
    font-weight:700;
}

@media (max-width:1050px){
    .layout{grid-template-columns:1fr}
    .sidebar{position:relative;height:auto}
    .grid,.grid-three,.flow-grid,.metrics{grid-template-columns:1fr}
    .map-grid{grid-template-columns:1fr}
    .rack-row{grid-template-columns:1fr}
    .main{padding:16px}
    .section-header{display:block}
    .section-header .secondary-button{width:100%;margin-top:12px}
}

@media print{
    body{background:#fff;color:#111}
    .sidebar,.no-print{display:none !important}
    .layout{display:block}
    .hero,.section{break-inside:avoid;border:1px solid #d0d7e2}
    .main{padding:0}
    table{font-size:11px}
    .topology{grid-template-columns:repeat(4, 1fr);overflow:visible}
    .map-grid,.flow-grid{grid-template-columns:repeat(2, 1fr);overflow:visible}
    .rack-row{grid-template-columns:80px 1fr 2fr}
    .node:after{display:none}
    a{color:#111;text-decoration:none}
}
</style>
<script>
function exportPdf(){
    window.print();
}
</script>
</head>

<body>
<div class="layout">
<aside class="sidebar">
    <img class="brand-logo" src="logo.png">
    <h1 class="brand">IT Architektur Engine</h1>
    <p>Industrie-IT planen: Internet, Firewall, Switches, VLANs, Server, Storage, Endgeraete, OT, Backup und Kosten.</p>
    <p>Trage zuerst nur grobe Zahlen ein. Das Tool rechnet daraus eine Startarchitektur und erklaert die wichtigsten Begriffe, damit die Planung nachvollziehbar bleibt.</p>

    <nav class="quick-nav">
        <a href="#uebersicht">Uebersicht</a>
        <a href="#topologie">Topologie</a>
        <a href="#software">Software & Lizenzen</a>
        <a href="#anbieter">Anbieter</a>
        <a href="#kosten">Kosten</a>
    </nav>

    <form method="POST">
        <label>Firma</label>
        <input name="name" value="<?= h($company["name"]) ?>">

        <label>Mitarbeiter</label>
        <input type="number" name="employees" min="1" value="<?= h($company["employees"]) ?>">

        <label>Gebaeude</label>
        <input type="number" name="buildings" min="1" value="<?= h($company["buildings"]) ?>">

        <label>CNC Maschinen</label>
        <input type="number" name="cnc" min="0" value="<?= h($company["cnc_machines"]) ?>">

        <label>Roboter</label>
        <input type="number" name="robots" min="0" value="<?= h($company["robots"]) ?>">

        <label>SPS / Steuerungen</label>
        <input type="number" name="sps" min="0" value="<?= h($company["sps"]) ?>">

        <label>Homeoffice Benutzer</label>
        <input type="number" name="homeoffice_users" min="0" value="<?= h($company["homeoffice_users"]) ?>">

        <label>Wachstum 5 Jahre in Prozent</label>
        <input type="number" name="growth_5y" min="0" value="<?= h($company["growth_5y"]) ?>">

        <label>Datenmenge aktuell in TB</label>
        <input type="number" name="data_tb" min="1" value="<?= h($company["data_tb"]) ?>">

        <label>WLAN Flaeche in qm</label>
        <input type="number" name="wifi_area" min="0" value="<?= h($company["wifi_area"]) ?>">

        <label>RTO: Wiederanlaufzeit in Stunden</label>
        <input type="number" name="rto_hours" min="1" value="<?= h($company["rto_hours"]) ?>">

        <label>RPO: maximaler Datenverlust in Stunden</label>
        <input type="number" name="rpo_hours" min="1" value="<?= h($company["rpo_hours"]) ?>">

        <label>Internet</label>
        <select name="internet">
            <option<?= selected($company["internet"], "Glasfaser") ?>>Glasfaser</option>
            <option<?= selected($company["internet"], "DSL") ?>>DSL</option>
            <option<?= selected($company["internet"], "SD-WAN") ?>>SD-WAN</option>
            <option<?= selected($company["internet"], "5G Backup") ?>>5G Backup</option>
        </select>

        <label>Firewall / Router</label>
        <select name="firewall">
            <option<?= selected($company["firewall"], "OPNsense") ?>>OPNsense</option>
            <option<?= selected($company["firewall"], "Fortigate") ?>>Fortigate</option>
            <option<?= selected($company["firewall"], "Palo Alto") ?>>Palo Alto</option>
        </select>

        <label>Virtualisierung</label>
        <select name="virtualization">
            <option<?= selected($company["virtualization"], "Proxmox VE") ?>>Proxmox VE</option>
            <option<?= selected($company["virtualization"], "VMware vSphere") ?>>VMware vSphere</option>
            <option<?= selected($company["virtualization"], "Hyper-V") ?>>Hyper-V</option>
        </select>

        <label>Mail</label>
        <select name="mail">
            <option<?= selected($company["mail"], "Microsoft 365 mit Mailstore") ?>>Microsoft 365 mit Mailstore</option>
            <option<?= selected($company["mail"], "Exchange Server lokal") ?>>Exchange Server lokal</option>
            <option<?= selected($company["mail"], "Hosted Mail") ?>>Hosted Mail</option>
        </select>

        <label>Backup</label>
        <select name="backup">
            <option<?= selected($company["backup"], "Disk-to-Disk-to-Tape") ?>>Disk-to-Disk-to-Tape</option>
            <option<?= selected($company["backup"], "Disk + Cloud") ?>>Disk + Cloud</option>
            <option<?= selected($company["backup"], "Disk + Replikation") ?>>Disk + Replikation</option>
        </select>

        <label>Verfuegbarkeit</label>
        <select name="availability">
            <option<?= selected($company["availability"], "Basis") ?>>Basis</option>
            <option<?= selected($company["availability"], "Business Standard") ?>>Business Standard</option>
            <option<?= selected($company["availability"], "Hochverfuegbar") ?>>Hochverfuegbar</option>
        </select>

        <label>Sicherheitsniveau</label>
        <select name="security_level">
            <option<?= selected($company["security_level"], "Normal") ?>>Normal</option>
            <option<?= selected($company["security_level"], "Erhoeht") ?>>Erhoeht</option>
            <option<?= selected($company["security_level"], "Kritisch") ?>>Kritisch</option>
        </select>

        <label>Cloud-Strategie</label>
        <select name="cloud_strategy">
            <option<?= selected($company["cloud_strategy"], "Lokal") ?>>Lokal</option>
            <option<?= selected($company["cloud_strategy"], "Hybrid") ?>>Hybrid</option>
            <option<?= selected($company["cloud_strategy"], "Cloud first") ?>>Cloud first</option>
        </select>

        <label>IT-Betrieb</label>
        <select name="it_operation">
            <option<?= selected($company["it_operation"], "Intern") ?>>Intern</option>
            <option<?= selected($company["it_operation"], "Intern + Dienstleister") ?>>Intern + Dienstleister</option>
            <option<?= selected($company["it_operation"], "Managed Service") ?>>Managed Service</option>
        </select>

        <label>OT-Kritikalitaet</label>
        <select name="ot_criticality">
            <option<?= selected($company["ot_criticality"], "Normal") ?>>Normal</option>
            <option<?= selected($company["ot_criticality"], "Hoch") ?>>Hoch</option>
            <option<?= selected($company["ot_criticality"], "Produktionskritisch") ?>>Produktionskritisch</option>
        </select>

        <button type="submit">Architektur berechnen</button>
        <button type="button" class="secondary-button" onclick="exportPdf()">PDF exportieren</button>
    </form>
</aside>

<main>
    <section class="hero" id="uebersicht">
        <h1><?= h($company["name"]) ?></h1>
        <p>Planungsvorschlag fuer eine moderne Industrie-IT mit sauberer Trennung von Office-IT, Servern, Telefonie, Gaesten, Backup und OT-Netz. Die Werte sind Startpunkte fuer eine echte Detailplanung mit Verkabelungsplan, Herstellerwahl, Lizenzierung und Sicherheitskonzept.</p>
        <div class="pill-row no-print">
            <span class="pill">PDF ueber Browserdruck</span>
            <span class="pill">Hardware + Software getrennt</span>
            <span class="pill">Open-Source-Optionen markiert</span>
            <span class="pill">Anbieteruebersicht enthalten</span>
        </div>
        <div class="metrics">
            <div class="metric"><strong><?= h($company["employees"]) ?></strong><span>Mitarbeiter</span></div>
            <div class="metric"><strong><?= h($company["buildings"]) ?></strong><span>Gebaeude</span></div>
            <div class="metric"><strong><?= h($company["cnc_machines"] + $company["robots"] + $company["sps"]) ?></strong><span>OT-Geraete</span></div>
            <div class="metric accent"><strong><?= h(money($total_costing["total"])) ?></strong><span>grobe Gesamtinvestition</span></div>
        </div>
    </section>

    <div class="main">
        <section class="section">
            <h2>Was muss eingegeben werden?</h2>
            <p>Die Eingaben sind bewusst einfach gehalten. In einer echten Planung werden spaeter Raumplaene, Portlisten, Kabellaengen, Hersteller, Lizenzmodelle und Sicherheitsanforderungen genauer erfasst.</p>
            <table>
                <tr><th>Eingabe</th><th>Bedeutung</th><th>Auswirkung auf die Planung</th></tr>
                <?php foreach($input_guide as $item): ?>
                <tr>
                    <td><?= h($item["field"]) ?></td>
                    <td><?= h($item["meaning"]) ?></td>
                    <td><?= h($item["impact"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="section">
            <h2>Zusaetzliche Bedarfsermittlung</h2>
            <p>Diese Fragen sind wichtig, wenn aus einer groben Idee eine belastbare IT-Infrastruktur werden soll. Sie beeinflussen Redundanz, Backup, Security, Cloud-Anteil, WLAN und Betriebsmodell.</p>
            <table>
                <tr><th>Thema</th><th>Aktuelle Eingabe</th><th>Bedeutung</th><th>Warum wichtig</th></tr>
                <?php foreach($requirements as $item): ?>
                <tr>
                    <td><?= h($item["topic"]) ?></td>
                    <td><?= h($item["input"]) ?></td>
                    <td><?= h($item["meaning"]) ?></td>
                    <td><?= h($item["needed"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="section" id="topologie">
            <h2>Visuelle Topologie</h2>
            <div class="topology">
                <?php foreach($topology as $node): ?>
                <div class="node">
                    <strong><?= h($node[0]) ?></strong>
                    <span><?= h($node[1]) ?></span>
                    <p><?= h($node[2]) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <h2>Campus-Skizze nach Bereichen</h2>
            <p>Diese Skizze zeigt, welche IT-Bausteine typischerweise in welchem Unternehmensbereich liegen. Sie ersetzt keinen echten Grundriss, hilft aber beim Denken in Zonen.</p>
            <div class="map-grid">
                <?php foreach($campus_map as $zone): ?>
                <div class="map-zone">
                    <small><?= h($zone["area"]) ?></small>
                    <strong><?= h($zone["title"]) ?></strong>
                    <p><?= h($zone["items"]) ?></p>
                    <span class="badge"><?= h($zone["vlans"]) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <h2>Datenfluss-Karte</h2>
            <p>Ein modernes Netzwerk wird nicht nur nach Geraeten geplant, sondern nach Datenfluessen. Daraus entstehen Firewall-Regeln, VLAN-Grenzen, Backupwege und Monitoringpunkte.</p>
            <div class="flow-grid">
                <?php foreach($data_flows as $flow): ?>
                <div class="flow-card">
                    <strong><?= h($flow["flow"]) ?></strong>
                    <div class="flow-path"><?= h($flow["path"]) ?></div>
                    <p><?= h($flow["risk"]) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <h2>Rack-Skizze Serverraum</h2>
            <p>Der Rackplan zeigt eine sinnvolle grobe Anordnung. In der Praxis werden Hoeheneinheiten, Kabelfuehrung, Luftstrom, Stromkreise und Wartungszugaenge genauer geplant.</p>
            <div class="rack">
                <?php foreach($rack_layout as $row): ?>
                <div class="rack-row">
                    <div class="rack-unit"><?= h($row["unit"]) ?></div>
                    <strong><?= h($row["device"]) ?></strong>
                    <p><?= h($row["purpose"]) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <h2>Netzwerkkarte mit Knotenpunkten</h2>
            <p>Diese Karte beschreibt die wichtigsten Knoten. Ein Knoten ist ein zentraler Punkt im Netzwerk, an dem Verbindungen zusammenlaufen oder wichtige Dienste bereitgestellt werden.</p>
            <table>
                <tr><th>Knoten</th><th>Typ</th><th>Standort</th><th>Verbindung</th><th>Pruefen / dokumentieren</th></tr>
                <?php foreach($topology_map as $node): ?>
                <tr>
                    <td><?= h($node["node"]) ?></td>
                    <td><span class="badge"><?= h($node["type"]) ?></span></td>
                    <td><?= h($node["location"]) ?></td>
                    <td><?= h($node["connects"]) ?></td>
                    <td><?= h($node["check"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="section">
            <h2>Planungsentscheidungen</h2>
            <div class="grid">
                <?php foreach($decision_cards as $card): ?>
                <div class="mini-card">
                    <strong><?= h($card["title"]) ?></strong>
                    <p><strong>Empfehlung:</strong> <?= h($card["recommendation"]) ?></p>
                    <p><?= h($card["why"]) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="grid">
            <section class="section">
                <h2>Internet, Router und Firewall</h2>
                <p><strong>Empfehlung:</strong> <?= h($internet["recommendation"]) ?></p>
                <p><strong>Endpunkt:</strong> <?= h($internet["endpoint"]) ?></p>
                <p><strong>Bandbreite:</strong> <?= h($internet["bandwidth"]) ?></p>
                <h3>Wichtige Einstellungen</h3>
                <?php render_bullets($internet["router_config"]); ?>
                <p class="note"><?= h($internet["learning"]) ?></p>
            </section>

            <section class="section">
                <h2>Switches, Standorte und Verkabelung</h2>
                <table>
                    <tr><th>Ebene</th><th>Geraete</th><th>Wo und wie</th></tr>
                    <?php foreach($network as $key => $item): if($key === "endpoints") continue; ?>
                    <tr>
                        <td><?= h(ucfirst($key)) ?></td>
                        <td><?= h($item["device"]) ?></td>
                        <td><?= h($item["location"]) ?><br><?= h($item["cabling"]) ?><br><?= h($item["config"]) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </section>
        </div>

        <section class="section">
            <h2>VLAN-Plan</h2>
            <table>
                <tr><th>VLAN</th><th>Name</th><th>Subnetz</th><th>Zweck</th><th>Zugriff</th></tr>
                <?php foreach($vlans as $vlan): ?>
                <tr>
                    <td><?= h($vlan["id"]) ?></td>
                    <td><?= h($vlan["name"]) ?></td>
                    <td><?= h($vlan["subnet"]) ?></td>
                    <td><?= h($vlan["purpose"]) ?></td>
                    <td><?= h($vlan["access"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="section">
            <h2>Technik einfach erklaert</h2>
            <p>Diese Begriffe tauchen in fast jeder IT-Infrastruktur auf. Wichtig ist nicht nur der Name, sondern wo der Baustein praktisch eingesetzt wird.</p>
            <?php render_term_table($glossary); ?>
        </section>

        <div class="grid">
            <section class="section">
                <h2>Server und Virtualisierung</h2>
                <p><strong>Plattform:</strong> <?= h($server_design["platform"]) ?></p>
                <p><strong>Hosts:</strong> <?= h($server_design["physical_hosts"]) ?></p>
                <p><strong>Ausstattung:</strong> <?= h($server_design["host_specs"]) ?></p>
                <h3>Cluster-Grundregeln</h3>
                <?php render_bullets($server_design["cluster"]); ?>
            </section>

            <section class="section">
                <h2>Storage, NAS und Backup</h2>
                <p><strong>Produktivspeicher:</strong> <?= h($storage["primary"]) ?></p>
                <p><strong>RAID:</strong> <?= h($storage["raid"]) ?></p>
                <p><strong>Einbindung:</strong> <?= h($storage["nas"]) ?></p>
                <h3>Freigaben</h3>
                <?php render_bullets($storage["shares"]); ?>
                <h3>Backup</h3>
                <?php render_bullets($storage["backup"]); ?>
            </section>
        </div>

        <div class="grid">
            <section class="section">
                <h2>Monitoring und Betrieb</h2>
                <p><strong>Empfehlung:</strong> <?= h($monitoring["system"]) ?></p>
                <p><strong>Server:</strong> <?= h($monitoring["server"]) ?></p>
                <h3>Was wird ueberwacht?</h3>
                <?php render_bullets($monitoring["targets"]); ?>
                <h3>Wie wird ueberwacht?</h3>
                <?php render_bullets($monitoring["protocols"]); ?>
                <h3>Alarmstufen</h3>
                <?php render_bullets($monitoring["alerts"]); ?>
            </section>

            <section class="section">
                <h2>Strom, USV und Serverraum</h2>
                <p><?= h($power["goal"]) ?></p>
                <p><strong>Auslegung:</strong> <?= h($power["runtime"]) ?></p>
                <?php render_bullets($power["items"]); ?>
                <p class="note">Ohne Strom- und USV-Plan kann ein gutes Netzwerk trotzdem instabil sein. Besonders Storage, Virtualisierung und Core Switches muessen sauber herunterfahren koennen.</p>
            </section>
        </div>

        <section class="section">
            <h2>Umsetzungsreihenfolge</h2>
            <div class="grid-three">
                <?php foreach($steps as $step): ?>
                <div class="mini-card">
                    <strong><?= h($step["phase"]) ?></strong>
                    <p><?= h($step["tasks"]) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section">
            <h2>Was in einer echten Planung nicht fehlen darf</h2>
            <p>Diese Punkte sind oft nicht sichtbar wie Server oder Switches, entscheiden aber, ob die Infrastruktur spaeter gut betrieben werden kann.</p>
            <table>
                <tr><th>Bereich</th><th>Einplanen und dokumentieren</th></tr>
                <?php foreach($missing_checklist as $item): ?>
                <tr>
                    <td><?= h($item["area"]) ?></td>
                    <td><?= h($item["items"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="section">
            <h2>Virtuelle Maschinen und Serverrollen</h2>
            <table>
                <tr><th>Server</th><th>Aufgabe</th><th>Betriebssystem</th><th>Backup-Hinweis</th></tr>
                <?php foreach($server_design["vms"] as $vm): ?>
                <tr>
                    <td><?= h($vm["name"]) ?></td>
                    <td><?= h($vm["role"]) ?></td>
                    <td><?= h($vm["os"]) ?></td>
                    <td><?= h($vm["backup"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section class="section" id="software">
            <div class="section-header">
                <div>
                    <h2>Software, Lizenzkosten und Open Source</h2>
                    <p>Softwarekosten sind grobe Richtwerte. Echte Preise haengen von Vertragsmodell, Edition, Support, Laufzeit, Benutzerzahl und Herstellerangebot ab.</p>
                </div>
                <span class="badge">Jahr 1 / grob</span>
            </div>
            <table>
                <tr><th>Software / Dienst</th><th>Lizenzmodell</th><th>Anzahl</th><th>Einzel/Jahr</th><th>Summe</th><th>Open Source</th><th>Hinweis</th></tr>
                <?php foreach($software_costing["items"] as $item): ?>
                <tr>
                    <td><?= h($item["area"]) ?></td>
                    <td><?= h($item["model"]) ?></td>
                    <td><?= h($item["qty"]) ?></td>
                    <td><?= h(money($item["unit"])) ?></td>
                    <td><?= h(money($item["qty"] * $item["unit"])) ?></td>
                    <td><?= h($item["open_source"]) ?></td>
                    <td><?= h($item["note"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <span class="cost-total">Grobe Software-/Lizenzkosten: <?= h(money($software_costing["total"])) ?></span>
        </section>

        <section class="section">
            <h2>Open-Source-Strategie</h2>
            <div class="grid">
                <?php foreach($open_source as $item): ?>
                <div class="mini-card">
                    <strong><?= h($item["area"]) ?></strong>
                    <p><?= h($item["items"]) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="section" id="anbieter">
            <h2>Hardwareanbieter und Softwareanbieter</h2>
            <p>Diese Tabelle hilft bei der Auswahl. Fuer Industrieeinsatz zaehlen nicht nur Anschaffungskosten, sondern Ersatzteilverfuegbarkeit, Support, Dokumentation, Sicherheitsupdates und Know-how im Betrieb.</p>
            <table>
                <tr><th>Bereich</th><th>Hardwareanbieter</th><th>Software / Plattform</th><th>Open-Source-Hinweis</th></tr>
                <?php foreach($providers as $provider): ?>
                <tr>
                    <td><?= h($provider["category"]) ?></td>
                    <td><?= h($provider["hardware"]) ?></td>
                    <td><?= h($provider["software"]) ?></td>
                    <td><?= h($provider["open_source"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <div class="grid">
            <section class="section">
                <h2>Endgeraete, Bueros und Produktion</h2>
                <table>
                    <tr><th>Bereich</th><th>Vorschlag</th></tr>
                    <?php foreach($network["endpoints"] as $area => $value): ?>
                    <tr><td><?= h($area) ?></td><td><?= h($value) ?></td></tr>
                    <?php endforeach; ?>
                </table>
                <p>Maschinen, SPS und Roboter gehoeren in das OT-VLAN. Nur definierte Server wie MES, Rueckmeldung, Datenbank oder Update-Proxy duerfen mit ihnen sprechen.</p>
            </section>

            <section class="section">
                <h2>Benutzer, VPN und Regeln</h2>
                <h3>Benutzerverwaltung</h3>
                <?php render_bullets($security["identity"]); ?>
                <h3>VPN</h3>
                <?php render_bullets($security["vpn"]); ?>
                <h3>Firewall-Regeln</h3>
                <?php render_bullets($security["firewall_rules"]); ?>
            </section>
        </div>

        <section class="section" id="kosten">
            <div class="section-header">
                <div>
                    <h2>Kostenrechnung und PDF-Zusammenfassung</h2>
                    <p>Die Kosten sind bewusst als Planungsrahmen gedacht. Angebote, Wartung, Installation, Schulung, Migration und laufende Betriebskosten muessen spaeter separat bewertet werden.</p>
                </div>
                <button type="button" class="secondary-button no-print" onclick="exportPdf()">Als PDF speichern</button>
            </div>
            <div class="metrics">
                <div class="metric"><strong><?= h(money($total_costing["hardware"])) ?></strong><span>Hardware grob</span></div>
                <div class="metric"><strong><?= h(money($total_costing["software"])) ?></strong><span>Software/Lizenzen grob</span></div>
                <div class="metric accent"><strong><?= h(money($total_costing["total"])) ?></strong><span>Gesamt grob</span></div>
                <div class="metric"><strong>PDF</strong><span>Drucken -> Als PDF speichern</span></div>
            </div>
            <h3>Hardwarekosten</h3>
            <table>
                <tr><th>Bereich</th><th>Anzahl</th><th>Einzelpreis</th><th>Summe</th><th>Hinweis</th></tr>
                <?php foreach($costing["items"] as $item): ?>
                <tr>
                    <td><?= h($item["area"]) ?></td>
                    <td><?= h($item["qty"]) ?></td>
                    <td><?= h(money($item["unit"])) ?></td>
                    <td><?= h(money($item["qty"] * $item["unit"])) ?></td>
                    <td><?= h($item["note"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <span class="cost-total">Grobe Hardware-Summe: <?= h(money($costing["total"])) ?></span>

            <h3>Software- und Lizenzkosten</h3>
            <table>
                <tr><th>Bereich</th><th>Anzahl</th><th>Einzel/Jahr</th><th>Summe</th><th>Open Source</th></tr>
                <?php foreach($software_costing["items"] as $item): ?>
                <tr>
                    <td><?= h($item["area"]) ?></td>
                    <td><?= h($item["qty"]) ?></td>
                    <td><?= h(money($item["unit"])) ?></td>
                    <td><?= h(money($item["qty"] * $item["unit"])) ?></td>
                    <td><?= h($item["open_source"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <span class="cost-total">Grobe Gesamt-Summe: <?= h(money($total_costing["total"])) ?></span>
        </section>
    </div>
</main>
</div>
</body>
</html>

<?php } ?>
