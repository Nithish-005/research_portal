<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>R&D Portal – Login</title>
<style>
/* ---------- CSS VARIABLES ---------- */
:root{
  --bg:#5480d1;          /* your blue */
  --card:#ffffff;
  --accent:#007bff;
  --accent-dark:#0056b3;
  --text:#333;
  --gray:#555;
  --radius:8px;
}
/* ---------- RESET ---------- */
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial,Helvetica,sans-serif}
/* ---------- BODY ---------- */
body{
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  background:var(--bg);
  padding:1rem;
}
/* ---------- CARD ---------- */
.login-container{
  background:var(--card);
  width:100%;
  max-width:400px;              /* phone first */
  border-radius:var(--radius);
  box-shadow:0 4px 18px rgba(0,0,0,.15);
  padding:clamp(1.5rem,4vw,2.5rem);
  text-align:center;
}
/* ---------- LOGO ---------- */
.logo-box{
  margin:0 auto 1.5rem;
  width:clamp(100px,20vw,150px);   /* scales with screen */
  aspect-ratio:1 / 1;
  background:url(logo.png) center/contain no-repeat;
}
/* ---------- FORM ---------- */
h2{margin-bottom:1.5rem;color:var(--text);font-size:clamp(1.3rem,4vw,1.6rem)}
form{text-align:left}
.input-group{margin-bottom:1.25rem}
label{display:block;margin-bottom:.5rem;color:var(--gray);font-size:.9rem}
input{
  width:100%;
  padding:.75rem 1rem;
  font-size:1rem;
  border:1px solid #ddd;
  border-radius:var(--radius);
  transition:border .2s;
}
input:focus{border-color:var(--accent);outline:none}
.login-btn{
  width:100%;
  padding:.85rem;
  background:var(--accent);
  color:#fff;
  font-size:1rem;
  border:none;
  border-radius:var(--radius);
  cursor:pointer;
  transition:background .2s;
}
.login-btn:hover{background:var(--accent-dark)}
.signup-link{margin-top:1.25rem;font-size:.85rem;color:var(--gray)}
.signup-link a{color:var(--accent);text-decoration:none}
.signup-link a:hover{text-decoration:underline}
/* ---------- TOAST ---------- */
.toast{
  position:fixed;
  bottom:25px;
  right:25px;
  background:var(--accent-dark);
  color:#fff;
  padding:.7rem 1.2rem;
  border-radius:4px;
  box-shadow:0 2px 6px rgba(0,0,0,.2);
  display:none;
  align-items:center;
  gap:.5rem;
}
.toast.show{display:flex}
/* ---------- SMALL PHONE TWEAKS ---------- */
@media (max-width:360px){
  .login-container{padding:1.25rem}
  h2{font-size:1.25rem}
}
</style>
</head>

<body>
  <div class="login-container">
    <!-- 1. LOGO -->
    <div class="logo-box" title="Velammal R&D"></div>

    <!-- 2. FORM -->
    <h2>R&D Portal</h2>
    <form action="auth.php" method="POST" id="loginForm">
      <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="login-btn">Login</button>
    </form>

    <!-- 3. FOOTER LINK -->
    <div class="signup-link">
      <p>Don't have an account? <a href="#">Contact R&D cell</a></p>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast" id="toast">
    <span id="toastIcon">⚠</span>
    <span id="toastMsg">Invalid credentials</span>
  </div>

<script>
/* ---------- show toast if PHP redirected with ?err=1 ---------- */
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('err')) {
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = 'Invalid username or password';
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
</body>
</html>