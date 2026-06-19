<?php
session_start();
if (!isset($_SESSION['uid']) || $_SESSION['role'] !== 'staff') {
    header('Location: index.html?err=2'); exit;
}
require 'db.php';
$uid = $_SESSION['uid'];
$cycle = $_SESSION['cycle'];

// counts
$j = $pdo->prepare("SELECT COUNT(*) FROM journals WHERE user_id = ? AND cycle_id = ?");
$j->execute([$uid, $cycle]); $jcount = $j->fetchColumn();
$p = $pdo->prepare("SELECT COUNT(*) FROM papers WHERE user_id = ? AND cycle_id = ?");
$p->execute([$uid, $cycle]); $pcount = $p->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Dashboard</title>
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
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-bottom:1.5rem}
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
    <span>R&D Portal</span>
  </div>
  <div class="user">Welcome, <?=htmlspecialchars($_SESSION['name'])?> | <a href="logout.php">Logout</a></div>
</header>

<div class="wrapper">
  <nav class="sidebar" id="sidebar">
    <h4>Menu</h4>
    <a href="#" onclick="showSec('dashboard')">Dashboard</a>
    <a href="#" onclick="showSec('add')">Add Journal/Paper</a>
  </nav>

  <main class="main" id="main">
    <!-- DASHBOARD -->
    <div id="dashboard">
      <h3>Dashboard – Cycle <?=$cycle?></h3>
      <div class="cards">
        <div class="card"><h5>Journals</h5><div class="num"><?=$jcount?> / <?=$_SESSION['tj']?></div><progress max="<?=$_SESSION['tj']?>" value="<?=$jcount?>"></progress></div>
        <div class="card"><h5>Papers</h5><div class="num"><?=$pcount?> / <?=$_SESSION['tp']?></div><progress max="<?=$_SESSION['tp']?>" value="<?=$pcount?>"></progress></div>
      </div>

      <h4>Your Entries</h4>
      <table>
        <thead><tr><th>Title</th><th>Journal/Conf</th><th>Status</th><th>PDF</th></tr></thead>
        <tbody>
<?php
$stmt = $pdo->prepare("SELECT * FROM journals WHERE user_id = ? AND cycle_id = ? ORDER BY id DESC");
$stmt->execute([$uid, $cycle]);
while ($r = $stmt->fetch()) {
    $pdf = $r['pdf_path'] ? '<a href="'.$r['pdf_path'].'" target="_blank">📄</a>' : '-';
    echo '<tr><td>'.htmlspecialchars($r['title']).'</td><td>'.htmlspecialchars($r['journal_name']).'</td><td>'.$r['status'].'</td><td>'.$pdf.'</td></tr>';
}
?>
        </tbody>
      </table>
    </div>

    <!-- ADD FORM -->
    <div id="add" style="display:none">
      <h3>Add Journal Entry</h3>
      <form action="add_journal.php" method="POST" enctype="multipart/form-data">
        <div class="input-group"><label>Title *<br><input required name="title" style="width:100%"></label></div>
        <div class="input-group"><label>Authors *<br><input required name="authors" style="width:100%"></label></div>
        <div class="input-group"><label>Journal Name *<br><input required name="journal_name" style="width:100%"></label></div>
        <div class="input-group"><label>ISSN<br><input name="issn" style="width:100%"></label></div>
        <div class="input-group"><label>DOI<br><input name="doi" style="width:100%"></label></div>
        <div class="input-group"><label>Volume<br><input name="vol" style="width:100%"></label></div>
        <div class="input-group"><label>Issue<br><input name="issue" style="width:100%"></label></div>
        <div class="input-group"><label>Pages<br><input name="pages" style="width:100%"></label></div>
        <div class="input-group"><label>Impact Factor<br><input name="impact_factor" style="width:100%"></label></div>
        <div class="input-group"><label>Indexing<br><input name="indexing" style="width:100%"></label></div>
        <div class="input-group"><label>Status<br>
          <select name="status"><option>Submitted</option><option>Accepted</option><option>Published</option></select>
        </label></div>
        <div class="input-group"><label>Upload PDF (optional)<br><input type="file" name="pdf" accept="application/pdf"></label></div>
        <button class="btn btn-success" type="submit">Save Journal</button>
      </form>
    </div>
  </main>
</div>

<script>
const toggleBtn = document.getElementById('toggleBtn');
const sidebar = document.getElementById('sidebar');
toggleBtn.onclick = () => sidebar.classList.toggle('hide');

function showSec(id){
  document.querySelectorAll('#main > div').forEach(d=>d.style.display='none');
  document.getElementById(id).style.display='block';
}
</script>
</body>
</html>