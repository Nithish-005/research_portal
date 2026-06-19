<?php
session_start();
if (!isset($_SESSION['uid']) || $_SESSION['role'] !== 'hod') {
    header('Location: index.html?err=2'); exit;
}
require 'db.php';
$cycle = $_SESSION['cycle'] ?? 1;

/* ---------- department numbers ---------- */
$staffNum  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='staff'")->fetchColumn();
$jDept     = $pdo->query("SELECT COUNT(*) FROM journals j JOIN users u ON u.id=j.user_id WHERE j.cycle_id=$cycle")->fetchColumn();
$pDept     = $pdo->query("SELECT COUNT(*) FROM papers   p JOIN users u ON u.id=p.user_id WHERE p.cycle_id=$cycle")->fetchColumn();
$onTrack   = $pdo->query("SELECT COUNT(DISTINCT j.user_id) FROM journals j WHERE j.cycle_id=$cycle")->fetchColumn();

/* ---------- staff list + progress ---------- */
$staffProg = $pdo->query(
  "SELECT u.id,u.full_name,u.role,u.target_j,u.target_p,
          (SELECT COUNT(*) FROM journals WHERE user_id=u.id AND cycle_id=$cycle) AS j_done,
          (SELECT COUNT(*) FROM papers   WHERE user_id=u.id AND cycle_id=$cycle) AS p_done
   FROM users u WHERE u.role='staff' ORDER BY u.full_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>HOD – R&D Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root{--primary:#0d6efd;--light:#f8f9fa;--dark:#212529;--success:#198754;--danger:#dc3545}
*{box-sizing:border-box;font-family:Segoe UI,Tahoma,sans-serif}
body{margin:0;background:var(--light);color:var(--dark)}
a{text-decoration:none;color:inherit}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:.6rem 1rem;background:var(--primary);color:#fff}
.topbar .user{font-size:.9rem}
.btn{padding:.4rem .8rem;border:none;border-radius:4px;cursor:pointer;font-size:.85rem;background:var(--primary);color:#fff}
.btn-success{background:var(--success)}.btn-danger{background:var(--danger)}
.wrapper{display:flex;height:calc(100vh - 48px)}
.sidebar{width:220px;background:#fff;border-right:1px solid #ddd;display:flex;flex-direction:column;transition:.3s}
.sidebar.hide{width:0;overflow:hidden}
.sidebar h4{padding:.8rem 1rem;margin:0;font-size:1rem;border-bottom:1px solid #eee}
.sidebar a{padding:.7rem 1rem;display:block;border-bottom:1px solid #f1f1f1}
.sidebar a:hover{background:var(--light)}
.main{flex:1;padding:1.2rem;overflow-y:auto}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem}
.card{background:#fff;padding:1rem;border-radius:6px;box-shadow:0 1px 3px rgba(0,0,0,.1)}
.card h5{margin:0 0 .4rem;font-size:.95rem;color:#555}
.card .num{font-size:1.8rem;font-weight:600}
progress{width:100%;height:8px}
table{width:100%;border-collapse:collapse;background:#fff;font-size:.85rem}
th,td{padding:.6rem .8rem;text-align:left;border-bottom:1px solid #eee}
th{background:#fafafa}
tr:hover{background:#fdfdfd}
.toast{position:fixed;bottom:20px;right:20px;background:var(--success);color:#fff;padding:.7rem 1.2rem;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,.2);display:none;align-items:center;gap:.5rem}
.toast.show{display:flex}
@media(max-width:768px){.sidebar{position:absolute;z-index:9;height:100%}}
</style>
</head>
<body>

<header class="topbar">
  <div>
    <button class="btn" id="toggleBtn">☰</button>
    <span>HOD – R&D Portal</span>
  </div>
  <div class="user">Welcome, <?=htmlspecialchars($_SESSION['name'])?> | <a href="logout.php">Logout</a></div>
</header>

<div class="wrapper">
  <nav class="sidebar" id="sidebar">
    <h4>Super-Admin Menu</h4>
    <a href="#" onclick="showSec('summary')">📊 Summary</a>
    <a href="#" onclick="showSec('staff')">👥 Staff Progress</a>
    <a href="#" onclick="showSec('cycles')">🔄 Cycle Control</a>
    <a href="#" onclick="showSec('reports')">📁 Reports</a>
  </nav>

  <main class="main" id="main">
    <!-- SUMMARY -->
    <div id="summary">
      <h3>Department Summary – Cycle <?=$cycle?></h3>
      <div class="cards">
        <div class="card"><h5>Total Staff</h5><div class="num"><?=$staffNum?></div></div>
        <div class="card"><h5>Journals (Dept)</h5><div class="num"><?=$jDept?></div></div>
        <div class="card"><h5>Papers (Dept)</h5><div class="num"><?=$pDept?></div></div>
        <div class="card"><h5>Staff On-Track</h5><div class="num"><?=$onTrack?></div></div>
      </div>
    </div>

    <!-- STAFF PROGRESS TABLE -->
    <div id="staff" style="display:none">
      <h3>Staff Progress</h3>
      <table>
        <thead><tr><th>Name</th><th>Role</th><th>Journals</th><th>Papers</th><th>Target (J/P)</th><th>% Complete</th></tr></thead>
        <tbody>
<?php foreach ($staffProg as $s):
      $jPct = $s['target_j'] ? round($s['j_done']/$s['target_j']*100) : 0;
      $pPct = $s['target_p'] ? round($s['p_done']/$s['target_p']*100) : 0;
      $avg  = round(($jPct+$pPct)/2);
?>
          <tr>
            <td><?=htmlspecialchars($s['full_name'])?></td>
            <td><?=$s['role']?></td>
            <td><?=$s['j_done']?> / <?=$s['target_j']?></td>
            <td><?=$s['p_done']?> / <?=$s['target_p']?></td>
            <td><?=$s['target_j']?> / <?=$s['target_p']?></td>
            <td><progress max="100" value="<?=$avg?>"></progress> <?=$avg?>%</td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- CYCLE CONTROL -->
    <div id="cycles" style="display:none">
      <h3>Cycle Control</h3>
      <div class="card" style="max-width:420px">
        <p><strong>Current Cycle:</strong> Cycle <?=$cycle?></p>
        <p>Closing this will automatically open Cycle <?=($cycle+1)?> for every staff member.</p>
        <button class="btn btn-danger" onclick="return confirm('Close Cycle <?=$cycle?> and open next?')" id="closeBtn">Close Cycle <?=$cycle?></button>
      </div>
    </div>

    <!-- REPORTS -->
    <div id="reports" style="display:none">
      <h3>Reports</h3>
      <div class="card" style="max-width:420px">
        <button class="btn btn-success" onclick="location.href='generate_report.php?cycle=<?=$cycle?>'">📥 Generate Excel Report (Cycle <?=$cycle?>)</button>
        <p style="margin-top:1rem;font-size:.8rem">Exports every journal & paper for the active cycle.</p>
      </div>
    </div>
  </main>
</div>

<!-- Toast -->
<div class="toast" id="toast"><span>✔</span><span id="toastMsg">Success</span></div>

<script>
const toggleBtn = document.getElementById('toggleBtn');
const sidebar   = document.getElementById('sidebar');
toggleBtn.onclick = () => sidebar.classList.toggle('hide');

function showSec(id){
  document.querySelectorAll('#main > div').forEach(d=>d.style.display='none');
  document.getElementById(id).style.display='block';
}

/* close cycle via AJAX (quick & dirty) */
document.getElementById('closeBtn').addEventListener('click', function(){
  fetch('close_cycle.php', {method:'POST'})
    .then(r=>r.text())
    .then(txt=>{
        showToast(txt);
        setTimeout(()=>location.reload(), 1200);
    });
});

function showToast(msg='Success'){
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'), 2500);
}
</script>
</body>
</html>