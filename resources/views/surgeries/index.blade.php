@extends('layouts.app')
@section('content')
  <div class="card section">
    {{-- Cabeçalho --}}
    <div class="toolbar">
      <h1 style="font-size:1.3rem;font-weight:800">Consultar Cirurgias</h1>
      <div style="display:flex;gap:8px">
        <a href="{{ route('surgeries.report', ['month' => $month, 'year' => $year]) }}"
           target="_blank" class="btn btn-outline">Relatório do mês</a>
        <a href="{{ route('surgeries.archived') }}" class="btn btn-outline">Cirurgias Arquivadas</a>
        <a href="{{ route('surgeries.create') }}" class="btn btn-primary">+ Nova cirurgia</a>
      </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="filters">
      <select name="month" class="select">
        @foreach($months as $m => $label)
          <option value="{{ $m }}" @selected($m == $month)>{{ $label }}</option>
        @endforeach
      </select>
      <select name="year" class="select">
        @foreach($years as $y)
          <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
        @endforeach
      </select>
      <input type="text" name="q" value="{{ $q ?? '' }}" class="input" placeholder="Buscar paciente ou médico">
      <button class="btn btn-outline" type="submit">Filtrar</button>
    </form>

    {{-- Tabela --}}
    <div class="card" style="overflow:hidden">
      <table>
        <thead>
          <tr>
            <th>Data/Hora</th>
            <th>Paciente</th>
            <th>Médico</th>
            <th>Tipo de Cirurgia</th>
            <th>Status</th>
            <th class="right">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($surgeries as $s)
            <tr>
              <td>{{ $s->start_at->format('d/m/Y H:i') }}</td>
              <td>{{ $s->patient->name }}</td>
              <td>{{ $s->surgeon_name }}</td>
              <td>{{ ucfirst($s->surgery_type) }}</td>
              <td>
                <span class="badge st-{{ $s->status }}">{{ ucfirst(str_replace('_',' ',$s->status)) }}</span>
              </td>
              <td class="right">
                <div style="display:flex;gap:8px;justify-content:flex-end">
                  <a href="{{ route('surgeries.edit',$s) }}"
                    class="btn btn-outline js-edit"
                    data-edit-url="{{ route('surgeries.edit',$s) }}"
                    data-patient="{{ $s->patient->name }}">
                    Editar
                  </a>

                  <form method="POST" action="{{ route('surgeries.destroy',$s) }}" class="js-delete-form" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="archive_reason" class="archive_reason_input">
                    <input type="hidden" name="__patient" value="{{ $s->patient->name }}">
                    <button type="submit" class="btn btn-danger js-delete-btn">Arquivar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="muted" style="padding:16px">Nenhum registro</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Paginação --}}
    <div style="margin-top:12px">
      {{ $surgeries->withQueryString()->links() }}
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Confirmar ARQUIVAMENTO
  document.querySelectorAll('.js-delete-form').forEach(form => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const patient = form.querySelector('input[name="__patient"]')?.value || 'o paciente';
      Swal.fire({
        title: 'Arquivar cirurgia?',
        html: `Esta ação não pode ser desfeita.<br><b>Paciente:</b> ${patient}`,
        icon: 'warning',
        input: 'text',
        inputLabel: 'Motivo do arquivamento',
        inputPlaceholder: 'Digite o motivo...',
        showCancelButton: true,
        confirmButtonText: 'Sim, arquivar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
      }).then((result) => {
        if (result.isConfirmed) {
          form.querySelector('.archive_reason_input').value = result.value;
          form.submit();
        }
      });
    }, { once:false });
  });

  // Confirmar EDIÇÃO (antes de navegar)
  document.querySelectorAll('.js-edit').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const url = link.dataset.editUrl;
      const patient = link.dataset.patient || 'o paciente';
      Swal.fire({
        title: 'Editar cirurgia?',
        html: `Abrir a tela de edição de <b>${patient}</b>.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Abrir edição',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#4f46e5',
      }).then((res) => {
        if (res.isConfirmed) window.location.href = url;
      });
    });
  });
});
</script>
@endpush

