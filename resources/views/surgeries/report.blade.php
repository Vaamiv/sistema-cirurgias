<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Relatório — {{ $periodLabel }}</title>
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,'Helvetica Neue',Arial;color:#0f172a;margin:24px}
  h1{margin:0 0 12px 0}
  .muted{color:#475569}
  table{width:100%;border-collapse:collapse;margin-top:12px}
  th,td{border:1px solid #e2e8f0;padding:8px 10px;font-size:14px}
  th{background:#f1f5f9;text-align:left}
  .toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
  .btn{display:inline-block;border:1px solid #cbd5e1;padding:8px 12px;border-radius:8px;text-decoration:none;color:#0f172a}
  @media print {.btn{display:none}}
</style>
</head>
<body>
  <div class="toolbar">
    <div>
      <h1>Relatório de cirurgias — {{ $periodLabel }}</h1>
      <div class="muted">Total: {{ $items->count() }}</div>
    </div>
    <div>
      <a href="javascript:window.print()" class="btn">Imprimir</a>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Data/Hora</th>
        <th>Paciente</th>
        <th>Médico</th>
        <th>Auxiliar Responsável</th>
        <th>Tipo de Cirurgia</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $s)
        <tr>
          <td>{{ $s->start_at->format('d/m/Y H:i') }}</td>
          <td>{{ $s->patient->name }}</td>
          <td>{{ $s->surgeon_name }}</td>
          <td>{{ $s->responsible_assistant }}</td>
          <td>{{ ucfirst($s->surgery_type) }}</td>
          <td>{{ ucfirst(str_replace('_',' ',$s->status)) }}</td>
        </tr>
      @empty
        <tr><td colspan="5">Nenhum registro no período.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
