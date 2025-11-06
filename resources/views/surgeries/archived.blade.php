@extends('layouts.app')
@section('content')
  <div class="card section">
    {{-- Header --}}
    <div class="toolbar">
      <h1 style="font-size:1.3rem;font-weight:800">Cirurgias Arquivadas</h1>
      <div style="display:flex;gap:8px">
        <a href="{{ route('surgeries.index') }}" class="btn btn-outline">Voltar</a>
      </div>
    </div>

    {{-- Table --}}
    <div class="card" style="overflow:hidden">
      <table>
        <thead>
          <tr>
            <th>Data/Hora</th>
            <th>Paciente</th>
            <th>Médico</th>
            <th>Motivo do Arquivamento</th>
          </tr>
        </thead>
        <tbody>
          @forelse($surgeries as $s)
            <tr>
              <td>{{ $s->start_at->format('d/m/Y H:i') }}</td>
              <td>{{ $s->patient->name }}</td>
              <td>{{ $s->surgeon_name }}</td>
              <td>{{ $s->archive_reason }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="muted" style="padding:16px">Nenhum registro arquivado</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:12px">
      {{ $surgeries->withQueryString()->links() }}
    </div>
  </div>
@endsection
