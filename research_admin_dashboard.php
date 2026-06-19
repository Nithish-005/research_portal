<?php
session_start();
if (!isset($_SESSION['uid']) || $_SESSION['role'] !== 'research_admin') {
    header('Location: index.html?err=2'); exit;
}
require 'db.php';
$cycle = $_SESSION['cycle'] ?? 1;

/* ---------- department numbers ---------- */
$staffNum  = $pdo->query("SELECT COUNT(*) FROM users WHERE role='staff'")->fetchColumn();
$jDept     = $pdo->query("SELECT COUNT(*) FROM journals j JOIN users u ON u.id=j.user_id WHERE j.cycle_id=$cycle")->fetchColumn();
$pDept     = $pdo->query("SELECT COUNT(*) FROM papers   p JOIN users u ON u.id=p.user_id WHERE p.cycle_id=$cycle")->fetchColumn();
$onTrack   = $pdo->query("SELECT COUNT(DISTINCT j.user_id) FROM journals j WHERE j.cycle_id=$cycle")->fetchColumn();

/* ---------- all entries ---------- */
$allEntries = $pdo->query(
  "SELECT j.*, u.full_name, u.role
   FROM journals j
   JOIN users u ON u.id = j.user_id
   WHERE j.cycle_id = $cycle
   ORDER BY u.full_name, j.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Research Admin – R&D Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root{--primary:#6f42c1;--light:#f8f9fa;--dark:#212529;--success:#198754;--danger:#dc3545}
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
    <span>Research Admin – R&D Portal</span>
  </div>
  <div class="user">Welcome, <?=htmlspecialchars($_SESSION['name'])?> | <a href="logout.php">Logout</a></div>
</header>

<div class="wrapper">
  <nav class="sidebar" id="sidebar">
    <h4>Admin Menu</h4>
    <a href="#" onclick="showSec('summary')">📊 Summary</a>
    <a href="#" onclick="showSec('entries')">📝 All Entries</a>
    <a href="#" onclick="showSec('reports')">📁 Reports</a>
  </nav>

  <main class="main" id="main">
    <!-- SUMMARY -->
    <div id="summary">
      <h3>Department Summary – Cycle <?=$cycle?></h3>
      <div class="cards">
        <div class="card"><h5>Total Staff</h5><div class="num"><?=$staffNum?></div></div>
        <div class="card"><h5>Journals</h5><div class="num"><?=$jDept?></div></div>
        <div class="card"><h5>Papers</h5><div class="num"><?=$pDept?></div></div>
        <div class="card"><h5>Staff On-Track</h5><div class="num"><?=$onTrack?></div></div>
      </div>
    </div>

    <!-- ALL ENTRIES (EDIT/DELETE) -->
    <div id="entries" style="display:none">
      <h3>All Department Entries</h3>
      <table id="entriesTable">
        <thead><tr><th>Staff</th><th>Title</th><th>Journal</th><th>Status</th><th>PDF</th><th>Action</th></tr></thead>
        <tbody>
<?php foreach ($allEntries as $e):
      $pdf = $e['pdf_path'] ? '<a href="'.$e['pdf_path'].'" target="_blank">📄</a>' : '-';
?>
          <tr data-id="<?=$e['id']?>">
            <td><?=htmlspecialchars($e['full_name'])?></td>
            <td><?=htmlspecialchars($e['title'])?></td>
            <td><?=htmlspecialchars($e['journal_name'])?></td>
            <td><?=$e['status']?></td>
            <td><?=$pdf?></td>
            <td>
              <button class="btn" onclick="editEntry(this)">Edit</button>
              <button class="btn btn-danger" onclick="delEntry(this)">Del</button>
            </td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- REPORTS -->
    <div id="reports" style="display:none">
      <h3>Reports</h3>
      <div class="card" style="max-width:420px">
        <button class="btn btn-success" onclick="location.href='generate_report.php?cycle=<?=$cycle?>'">📥 Generate Excel Report (Cycle <?=$cycle?>)</button>
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

/* ---------- edit row (inline) ---------- */
function editEntry(btn){
  const tr  = btn.closest('tr');
  const id  = tr.dataset.id;
  // make status editable
  const statTd = tr.cells[3];
  const curVal = statTd.textContent;
  statTd.innerHTML = `<select>
                        <option ${curVal==='Submitted'?'selected':''}>Submitted</option>
                        <option ${curVal==='Accepted'?'selected':''}>Accepted</option>
                        <option ${curVal==='Published'?'selected':''}>Published</option>
                      </select>`;
  btn.textContent = 'Save';
  btn.onclick = () => {
    const newStatus = statTd.querySelector('select').value;
    fetch('update_entry.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'id='+id+'&status='+newStatus
    }).then(r=>r.text())
      .then(txt=>{ showToast(txt); statTd.textContent=newStatus; })
      .finally(()=>{ btn.textContent='Edit'; btn.onclick=()=>editEntry(btn); });
  };
}

/* ---------- delete row ---------- */
function delEntry(btn){
  if(!confirm('Delete this entry?')) return;
  const tr = btn.closest('tr');
  const id = tr.dataset.id;
  fetch('delete_entry.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'id='+id})
    .then(r=>r.text())
    .then(txt=>{ showToast(txt); tr.remove(); });
}

/* ---------- toast helper ---------- */
function showToast(msg='Success'){
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),2500);
}
</script>
</body>
</html>