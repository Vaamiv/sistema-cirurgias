@extends('layouts.app')
@section('content')
  <div class="card form-card">
    <div class="toolbar">
      <h1 style="font-size:1.3rem;font-weight:800">Nova Cirurgia</h1>
      <div style="display:flex;gap:8px">
        <a href="{{ route('surgeries.index') }}" class="btn btn-outline">Voltar</a>
      </div>
    </div>

    <form method="POST" action="{{ route('surgeries.store') }}" class="grid grid-2">
      @csrf

      <div class="field">
        <label class="label">Nome do paciente</label>
        <input name="patient_name" value="{{ old('patient_name') }}" class="input" required>
        @error('patient_name')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Data de nascimento do paciente</label>
        <input type="date" name="patient_birth_date" value="{{ old('patient_birth_date') }}" class="input">
        @error('patient_birth_date')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Telefone do paciente</label>
        <input name="patient_phone" value="{{ old('patient_phone') }}" class="input">
        @error('patient_phone')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Médico responsável</label>
        <input name="surgeon_name" value="{{ old('surgeon_name') }}" class="input" required>
        @error('surgeon_name')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Auxiliar responsável</label>
        <input name="responsible_assistant" value="{{ old('responsible_assistant') }}" class="input">
        @error('responsible_assistant')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Tipo de cirurgia</label>
        <select name="surgery_type" class="select">
          <option value="limpa" @selected(old('surgery_type') === 'limpa')>Limpa</option>
          <option value="contaminada" @selected(old('surgery_type') === 'contaminada')>Contaminada</option>
        </select>
        @error('surgery_type')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Tipo de procedimento</label>
        <input name="procedure_type" value="{{ old('procedure_type') }}" class="input">
        @error('procedure_type')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Material necessário</label>
        <textarea name="necessary_materials" class="input">{{ old('necessary_materials') }}</textarea>
        @error('necessary_materials')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Quem agendou</label>
        <input name="scheduled_by" value="{{ old('scheduled_by') }}" class="input">
        @error('scheduled_by')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Cirurgia eletiva ou de urgência</label>
        <select name="is_elective" class="select">
          <option value="1" @selected(old('is_elective') === '1')>Eletiva</option>
          <option value="0" @selected(old('is_elective') === '0')>Urgência</option>
        </select>
        @error('is_elective')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Data e hora</label>
        <input type="datetime-local" name="surgery_datetime" value="{{ old('surgery_datetime') }}" class="dt" required>
        <div class="hint">Duração padrão: 60 minutos</div>
        @error('surgery_datetime')<div class="hint" style="color:#b91c1c">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label class="label">Status</label>
        <select name="status" class="select">
          @foreach(['agendada','confirmada','em_andamento','finalizada','adiada','cancelada'] as $st)
            <option value="{{ $st }}" @selected(old('status')===$st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
          @endforeach
        </select>
      </div>

      <div style="grid-column:1 / -1;display:flex;justify-content:flex-end;gap:8px;margin-top:8px">
        <a href="{{ route('surgeries.index') }}" class="btn btn-outline">Cancelar</a>
        <button class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
@endsection
