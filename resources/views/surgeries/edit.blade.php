@extends('layouts.app')
@section('content')
  <div class="card form-card">
    <div class="toolbar">
      <h1 style="font-size:1.3rem;font-weight:800">Editar Cirurgia</h1>
      <div style="display:flex;gap:8px">
        <a href="{{ route('surgeries.index') }}" class="btn btn-outline">Voltar</a>
      </div>
    </div>

    <form method="POST" action="{{ route('surgeries.update',$surgery) }}" class="grid grid-2">
      @csrf
      @method('PUT')

      <div class="field">
        <label class="label">Nome do paciente</label>
        <input name="patient_name" value="{{ old('patient_name',$surgery->patient->name) }}" class="input" required>
        @error('patient_name')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Médico responsável</label>
        <input name="surgeon_name" value="{{ old('surgeon_name',$surgery->surgeon_name) }}" class="input" required>
        @error('surgeon_name')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Data e hora</label>
        <input type="datetime-local" name="surgery_datetime"
               value="{{ old('surgery_datetime',$surgery->start_at->format('Y-m-d\\TH:i')) }}"
               class="dt" required>
        <div class="hint">Duração padrão: 60 minutos</div>
        @error('surgery_datetime')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Status</label>
        <select name="status" class="select">
          @foreach(['agendada','confirmada','em_andamento','finalizada','adiada','cancelada'] as $st)
            <option value="{{ $st }}" @selected(old('status',$surgery->status)===$st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
          @endforeach
        </select>
      </div>

      <div style="grid-column:1 / -1;display:flex;justify-content:flex-end;gap:8px;margin-top:8px">
        <a href="{{ route('surgeries.index') }}" class="btn btn-outline">Cancelar</a>
        <button class="btn btn-primary">Atualizar</button>
      </div>
    </form>
  </div>
@endsection
