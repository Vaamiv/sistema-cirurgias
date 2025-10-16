<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Centro Cirúrgico</title>
  @vite(['resources/js/app.js','resources/css/app.css'])
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root{
      --bg:#eef3ff; --bg2:#f7f9ff; --card:#ffffff; --line:#e6eaf5;
      --text:#1f2a44; --muted:#6b7a99;
      --primary:#4f46e5; --primary-600:#4338ca;
      --green:#22c55e; --yellow:#f59e0b; --red:#ef4444; --blue:#3b82f6; --violet:#8b5cf6;
      --shadow:0 10px 25px rgba(0,20,80,.08);
      --radius:14px;
    }
    *{box-sizing:border-box}
    body{margin:0;background:linear-gradient(180deg,var(--bg2),var(--bg)) fixed;color:var(--text);font:16px/1.45 system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Helvetica Neue',Arial}
    a{color:inherit;text-decoration:none}
    .container{max-width:1200px;margin:0 auto;padding:28px 20px}
    .navbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
    .brand{font-weight:800;font-size:1.6rem;letter-spacing:.2px;color:#344167}
    .muted{color:var(--muted)}
    .nav-actions{display:flex;gap:.5rem;flex-wrap:wrap}
    .card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow)}
    .section{padding:18px}
    .toolbar{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:14px}
    .filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
    .btn{display:inline-flex;align-items:center;gap:.5rem;border-radius:10px;padding:.58rem .95rem;border:1px solid transparent;cursor:pointer;transition:all .15s ease}
    .btn:hover{transform:translateY(-1px)}
    .btn-primary{background:var(--primary);color:#fff;border-color:transparent;box-shadow:0 6px 16px rgba(79,70,229,.25)}
    .btn-primary:hover{background:var(--primary-600)}
    .btn-outline{background:#fff;border-color:var(--line);color:var(--text)}
    .btn-success{background:var(--green);color:#fff}
    .btn-danger{background:var(--red);color:#fff}
    .btn-warning{background:var(--yellow);color:#fff}
    .input,.select, .dt{background:#fff;border:1px solid var(--line);color:var(--text);border-radius:10px;padding:.6rem .85rem;min-height:42px}
    .input:focus,.select:focus,.dt:focus{outline:3px solid rgba(79,70,229,.18);border-color:var(--primary)}
    table{width:100%;border-collapse:separate;border-spacing:0}
    th,td{padding:.8rem 1rem;font-size:.95rem}
    thead th{color:var(--muted);font-weight:700;border-bottom:1px solid var(--line);background:#f6f8ff}
    tbody tr{border-bottom:1px solid var(--line)}
    .right{text-align:right}
    .badge{padding:.3rem .6rem;border-radius:999px;font-size:.78rem;font-weight:700;white-space:nowrap}
    .st-agendada    {background:#ede9fe;color:#5b21b6}
    .st-confirmada  {background:#dcfce7;color:#166534}
    .st-em_andamento{background:#dbeafe;color:#1e40af}
    .st-finalizada  {background:#d1fae5;color:#065f46}
    .st-adiada      {background:#fef3c7;color:#92400e}
    .st-cancelada   {background:#fee2e2;color:#991b1b}
    .grid{display:grid;gap:12px}
    .grid-2{grid-template-columns:1fr}
    @media (min-width:768px){ .grid-2{grid-template-columns:1fr 1fr} }
    .form-card{padding:18px}
    .field{display:flex;flex-direction:column;gap:6px}
    .label{font-weight:600;color:#344167}
    .hint{font-size:.85rem;color:var(--muted)}
  </style>
</head>
<body>
  <div class="container">
    <nav class="navbar">
      <div>
        <div class="brand">Sistema de Cirurgias</div>
        <div class="muted" style="margin-top:4px">Gerencie agendamentos de forma simples</div>
      </div>
      <div class="nav-actions">
        <a href="{{ route('surgeries.index') }}" class="btn btn-outline">Cirurgias</a>
      </div>
    </nav>
    <main>@yield('content')</main>
  </div>
  {{-- Toasts globais --}}
<script>
  @if(session('success'))
    Swal.fire({
      toast: true, position: 'top-end', icon: 'success',
      title: @json(session('success')), showConfirmButton: false, timer: 2500,
    });
  @endif

  @if(session('error'))
    Swal.fire({
      toast: true, position: 'top-end', icon: 'error',
      title: @json(session('error')), showConfirmButton: false, timer: 3000,
    });
  @endif

  @if ($errors->any())
    Swal.fire({
      icon: 'error', title: 'Verifique os campos',
      html: @json(implode('<br>', $errors->all())),
      confirmButtonText: 'OK'
    });
  @endif
</script>

@stack('scripts')

</body>
</html>
