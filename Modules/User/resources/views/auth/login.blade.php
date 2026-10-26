<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in · Dental Chart System</title>
<style>
:root{--blue:#087bd0;--blue2:#0063a8;--ink:#073f70;--muted:#536679;--line:#d6e0e9}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;background:#f4f7fb;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#12324c;display:grid;place-items:center;padding:28px}
.shell{width:min(1120px,100%);min-height:520px;border-radius:24px;overflow:hidden;display:grid;grid-template-columns:1.28fr .95fr;background:linear-gradient(115deg,#0b8fe8 0%,#0875c9 45%,#064f87 100%);box-shadow:0 24px 65px rgba(9,54,94,.20);position:relative}
.welcome{position:relative;overflow:hidden;padding:clamp(36px,6vw,82px);color:#fff;display:flex;flex-direction:column;justify-content:center;isolation:isolate}
.welcome:before,.welcome:after{content:"";position:absolute;border-radius:50%;z-index:-1;pointer-events:none}
.welcome:before{width:660px;height:660px;left:-330px;top:-185px;background:rgba(0,57,126,.18);box-shadow:inset -35px 0 0 rgba(255,255,255,.03)}
.welcome:after{width:470px;height:240px;left:-120px;bottom:-120px;background:linear-gradient(130deg,rgba(0,168,255,.72),rgba(0,68,143,.15));transform:rotate(-17deg)}
.kicker{font-size:13px;font-weight:750;letter-spacing:.22em;text-transform:uppercase;opacity:.82;margin-bottom:22px}
.welcome h1{font-size:clamp(38px,5vw,57px);line-height:1;letter-spacing:.055em;margin:0 0 18px;font-weight:850}
.welcome h2{font-size:15px;letter-spacing:.13em;margin:0 0 18px;font-weight:750}
.welcome p{line-height:1.9;max-width:390px;font-size:14px;color:rgba(255,255,255,.91);margin:0}
.features{margin-top:34px;display:flex;gap:10px;flex-wrap:wrap}
.pill{font-size:11px;padding:8px 12px;border-radius:999px;border:1px solid rgba(255,255,255,.3);background:rgba(255,255,255,.09);letter-spacing:.025em}
.login-side{display:flex;align-items:center;justify-content:center;padding:34px;background:rgba(3,58,99,.16)}
.card{width:min(100%,390px);padding:34px 32px 26px;border-radius:24px;background:#fff;box-shadow:0 18px 38px rgba(0,25,48,.20)}
.brandmark{width:43px;height:43px;border-radius:13px;background:#e6f4ff;display:grid;place-items:center;color:#0878c8;margin-bottom:15px}
.brandmark svg{width:25px;height:25px}
.card h3{margin:0;color:var(--ink);font-size:31px;font-weight:800;letter-spacing:.015em}
.sub{margin:6px 0 22px;color:#6a7886;font-size:11px;line-height:1.65}
.field{margin:0 0 13px}
.field label{display:block;font-size:12px;color:#425469;margin:0 0 7px;font-weight:650}
.input-wrap{display:flex;align-items:center;border:1px solid var(--line);border-radius:12px;height:48px;transition:.2s;box-shadow:0 1px 2px rgba(20,54,85,.025)}
.input-wrap:focus-within{border-color:#1488d8;box-shadow:0 0 0 3px rgba(20,136,216,.12)}
.input-wrap input{min-width:0;width:100%;border:0;outline:0;background:transparent;padding:0 14px;font-size:13px;color:#17354d}
.input-wrap input::placeholder{color:#96a3af}
.input-wrap.has-error{border-color:#dc2626;box-shadow:0 0 0 3px rgba(220,38,38,.12)}
.error-msg{color:#dc2626;font-size:11px;margin-top:5px;font-weight:550}
.show{border:0;background:transparent;color:#07578f;font-size:10px;letter-spacing:.04em;font-weight:800;padding:12px;cursor:pointer}
.row-meta{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:12px 0 18px;font-size:11px;color:#647384}
.remember{display:flex;align-items:center;gap:7px;cursor:pointer}
.remember input{accent-color:#0879ca;width:14px;height:14px}
a{color:#07588f;text-decoration:none;font-weight:650}
a:hover{text-decoration:underline}
.primary{width:100%;height:47px;border:0;border-radius:11px;color:white;font-weight:800;letter-spacing:.03em;background:linear-gradient(100deg,#0786d8,#0063a9);box-shadow:0 5px 12px rgba(0,105,180,.22);cursor:pointer;transition:transform .16s,filter .16s}
.primary:hover{filter:brightness(1.06);transform:translateY(-1px)}
.primary:active{transform:translateY(0)}
.foot{border-top:1px solid #edf1f5;margin-top:25px;padding-top:18px;text-align:center;font-size:11px;color:#6d7b88}
.alert{padding:10px 14px;border-radius:10px;font-size:12px;margin-bottom:14px;line-height:1.4}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.alert-success{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46}
.toast{font-size:11px;min-height:16px;margin-top:8px;color:#0a6a9c}
@media(max-width:760px){body{padding:14px}.shell{grid-template-columns:1fr;max-width:510px}.welcome{padding:28px 30px 25px;min-height:235px}.welcome h1{font-size:39px}.welcome p{font-size:12px;line-height:1.7}.kicker{margin-bottom:15px}.features{margin-top:18px}.login-side{padding:20px 14px 26px}.card{padding:27px 24px 23px}.welcome:before{width:420px;height:420px;left:-235px;top:-145px}}
@media(prefers-reduced-motion:reduce){*{transition:none!important;scroll-behavior:auto!important}}
</style>
</head>
<body>
<main class="shell">
  <section class="welcome" aria-label="Welcome">
    <div class="kicker">Dental care · Management portal</div>
    <h1>WELCOME</h1>
    <h2>YOUR PRACTICE. ONE SIMPLE PLACE.</h2>
    <p>Sign in to manage patients, dental charts, treatment notes, and your clinic workflow securely.</p>
    <div class="features"><span class="pill">Patient records</span><span class="pill">Interactive dental charts</span><span class="pill">Role-based access</span></div>
  </section>
  <section class="login-side" aria-label="Sign in form">
    <form class="card" id="loginForm" method="POST" action="{{ route('login') }}">
      @csrf
      <div class="brandmark" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7.3 3.2c-2.4 0-4 2-3.6 5 .5 4 2.2 11.6 4.4 12.1 1.9.5 1.7-5.1 3.9-5.1s2 5.6 3.9 5.1c2.2-.5 3.9-8.1 4.4-12.1.4-3-1.2-5-3.6-5-1.8 0-2.9 1.1-4.7 1.1S9.1 3.2 7.3 3.2Z"/></svg>
      </div>
      <h3>Sign in</h3>
      <p class="sub">Enter your account details to continue to your workspace.</p>

      @if (session('status'))
        <div class="alert alert-success" role="status">
          {{ session('status') }}
        </div>
      @endif

      @if ($errors->has('username') && !old('username'))
        <div class="alert alert-error" role="alert">
          {{ $errors->first('username') }}
        </div>
      @endif

      <div class="field">
        <label for="username">Username</label>
        <div class="input-wrap {{ $errors->has('username') ? 'has-error' : '' }}">
          <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" placeholder="Enter your username" required autofocus>
        </div>
        @error('username')
          <div class="error-msg" role="alert">{{ $message }}</div>
        @enderror
      </div>

      <div class="field">
        <label for="password">Password</label>
        <div class="input-wrap {{ $errors->has('password') ? 'has-error' : '' }}">
          <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required>
          <button class="show" id="togglePassword" type="button" aria-label="Show password">SHOW</button>
        </div>
        @error('password')
          <div class="error-msg" role="alert">{{ $message }}</div>
        @enderror
      </div>

      <div class="row-meta">
        <label class="remember">
          <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
          <span>Remember me</span>
        </label>
        <a href="#" id="forgotLink">Forgot password?</a>
      </div>

      <button class="primary" type="submit" id="signIn">Sign in</button>
      <div class="toast" id="message" role="status" aria-live="polite"></div>
      <div class="foot">Need access? Contact your clinic administrator.</div>
    </form>
  </section>
</main>
<script>
const password = document.getElementById('password');
const toggle = document.getElementById('togglePassword');
const forgotLink = document.getElementById('forgotLink');
const message = document.getElementById('message');

if (toggle && password) {
  toggle.addEventListener('click', () => {
    const showing = password.type === 'text';
    password.type = showing ? 'password' : 'text';
    toggle.textContent = showing ? 'SHOW' : 'HIDE';
    toggle.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
  });
}

if (forgotLink && message) {
  forgotLink.addEventListener('click', (event) => {
    event.preventDefault();
    message.textContent = 'Please contact your clinic administrator to reset your password.';
  });
}
</script>
</body>
</html>

