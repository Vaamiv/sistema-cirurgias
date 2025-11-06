<?php

namespace App\Http\Controllers;

use App\Events\SurgeryUpdated;
use App\Models\{Surgery, Patient};
use Carbon\Carbon;
use Illuminate\Http\Request;

class SurgeryController extends Controller
{
    /** Lista com filtros (mês/ano/busca) */
    public function index()
    {
        $q     = request('q');
        $month = (int) request('month', now()->month);
        $year  = (int) request('year',  now()->year);

        $surgeries = Surgery::with('patient')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->whereHas('patient', fn($p) => $p->where('name', 'like', "%{$q}%"))
                      ->orWhere('surgeon_name', 'like', "%{$q}%");
                });
            })
            ->whereYear('start_at', $year)
            ->whereMonth('start_at', $month)
            ->orderBy('start_at')
            ->paginate(20);

        // selects de mês/ano
        $months = [
            1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',5=>'Mai',6=>'Jun',
            7=>'Jul',8=>'Ago',9=>'Set',10=>'Out',11=>'Nov',12=>'Dez'
        ];
        $years = range(now()->year - 2, now()->year + 1);

        return view('surgeries.index', compact('surgeries','months','years','month','year','q'));
    }

    /** Relatório mensal (HTML imprimível) */
    public function monthlyReport()
    {
        $month = (int) request('month', now()->month);
        $year  = (int) request('year',  now()->year);

        $items = Surgery::with('patient')
            ->whereYear('start_at', $year)
            ->whereMonth('start_at', $month)
            ->orderBy('start_at')
            ->get();

        $periodLabel = Carbon::create($year, $month, 1)->translatedFormat('F/Y');

        return view('surgeries.report', [
            'items' => $items,
            'periodLabel' => $periodLabel,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function create()
    {
        return view('surgeries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_name'          => 'required|string|max:255',
            'patient_birth_date'    => 'nullable|date',
            'patient_phone'         => 'nullable|string|max:255',
            'surgery_datetime'      => 'required|date',
            'surgeon_name'          => 'required|string|max:255',
            'responsible_assistant' => 'nullable|string|max:255',
            'surgery_type'          => 'required|in:limpa,contaminada',
            'procedure_type'        => 'nullable|string|max:255',
            'necessary_materials'   => 'nullable|string',
            'scheduled_by'          => 'nullable|string|max:255',
            'is_elective'           => 'required|boolean',
            'status'                => 'nullable|in:agendada,confirmada,em_andamento,finalizada,adiada,cancelada',
        ]);

        $patient = Patient::updateOrCreate(
            ['name' => $data['patient_name']],
            [
                'birth_date' => $data['patient_birth_date'],
                'contact' => $data['patient_phone'],
            ]
        );

        $start = Carbon::parse($data['surgery_datetime']);
        $end   = (clone $start)->addMinutes(60); // padrão 60min

        $surgery = Surgery::create([
            'patient_id'            => $patient->id,
            'surgeon_name'          => $data['surgeon_name'],
            'start_at'              => $start,
            'end_at'                => $end,
            'status'                => $request->input('status', 'agendada'),
            'responsible_assistant' => $data['responsible_assistant'],
            'surgery_type'          => $data['surgery_type'],
            'procedure_type'        => $data['procedure_type'],
            'necessary_materials'   => $data['necessary_materials'],
            'scheduled_by'          => $data['scheduled_by'],
            'is_elective'           => $data['is_elective'],
        ]);

        event(new SurgeryUpdated($surgery->fresh('patient')));
        return redirect()->route('surgeries.index')->with('success', 'Cirurgia criada.');
    }

    public function edit(Surgery $surgery)
    {
        $surgery->load('patient');
        return view('surgeries.edit', compact('surgery'));
    }

    public function update(Request $request, Surgery $surgery)
    {
        $data = $request->validate([
            'patient_name'          => 'required|string|max:255',
            'patient_birth_date'    => 'nullable|date',
            'patient_phone'         => 'nullable|string|max:255',
            'surgery_datetime'      => 'required|date',
            'surgeon_name'          => 'required|string|max:255',
            'responsible_assistant' => 'nullable|string|max:255',
            'surgery_type'          => 'required|in:limpa,contaminada',
            'procedure_type'        => 'nullable|string|max:255',
            'necessary_materials'   => 'nullable|string',
            'scheduled_by'          => 'nullable|string|max:255',
            'is_elective'           => 'required|boolean',
            'status'                => 'nullable|in:agendada,confirmada,em_andamento,finalizada,adiada,cancelada',
        ]);

        $patient = Patient::updateOrCreate(
            ['name' => $data['patient_name']],
            [
                'birth_date' => $data['patient_birth_date'],
                'contact' => $data['patient_phone'],
            ]
        );

        $start = Carbon::parse($data['surgery_datetime']);
        $end   = (clone $start)->addMinutes(60);

        $surgery->update([
            'patient_id'            => $patient->id,
            'surgeon_name'          => $data['surgeon_name'],
            'start_at'              => $start,
            'end_at'                => $end,
            'status'                => $request->input('status', 'agendada'),
            'responsible_assistant' => $data['responsible_assistant'],
            'surgery_type'          => $data['surgery_type'],
            'procedure_type'        => $data['procedure_type'],
            'necessary_materials'   => $data['necessary_materials'],
            'scheduled_by'          => $data['scheduled_by'],
            'is_elective'           => $data['is_elective'],
        ]);

        event(new SurgeryUpdated($surgery->fresh('patient')));
        return redirect()->route('surgeries.index')->with('success', 'Cirurgia atualizada.');
    }

    public function destroy(Request $request, Surgery $surgery)
    {
        $request->validate(['archive_reason' => 'required|string|max:255']);
        $surgery->update(['archive_reason' => $request->input('archive_reason')]);
        $surgery->delete();
        event(new SurgeryUpdated($surgery));
        return back()->with('success', 'Cirurgia arquivada.');
    }

    public function archived()
    {
        $surgeries = Surgery::onlyTrashed()->with('patient')->paginate(20);
        return view('surgeries.archived', compact('surgeries'));
    }

    /** API para o FullCalendar */
    public function apiIndex(Request $request)
    {
        $start = $request->query('start');
        $end   = $request->query('end');

        $query = Surgery::with('patient');

        if ($start && $end) {
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                  ->orWhereBetween('end_at',   [$start, $end]);
            });
        }

        $events = $query->get()->map(fn($s) => [
            'id'    => $s->id,
            'title' => $s->patient->name,
            'start' => $s->start_at->toIso8601String(),
            'end'   => $s->end_at->toIso8601String(),
            'extendedProps' => [
                'surgeon_name' => $s->surgeon_name,
                'status'       => $s->status,
            ],
        ]);

        return response()->json($events);
    }
}
