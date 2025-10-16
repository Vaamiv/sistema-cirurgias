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
            'patient_name'     => 'required|string|max:255',
            'surgery_datetime' => 'required|date',
            'surgeon_name'     => 'required|string|max:255',
            'status'           => 'nullable|in:agendada,confirmada,em_andamento,finalizada,adiada,cancelada',
        ]);

        $patient = Patient::firstOrCreate(['name' => $data['patient_name']]);

        $start = Carbon::parse($data['surgery_datetime']);
        $end   = (clone $start)->addMinutes(60); // padrão 60min

        $surgery = Surgery::create([
            'patient_id'   => $patient->id,
            'surgeon_name' => $data['surgeon_name'],
            'start_at'     => $start,
            'end_at'       => $end,
            'status'       => $request->input('status','agendada'),
        ]);

        event(new SurgeryUpdated($surgery->fresh('patient')));
        return redirect()->route('surgeries.index')->with('success','Cirurgia criada.');
    }

    public function edit(Surgery $surgery)
    {
        $surgery->load('patient');
        return view('surgeries.edit', compact('surgery'));
    }

    public function update(Request $request, Surgery $surgery)
    {
        $data = $request->validate([
            'patient_name'     => 'required|string|max:255',
            'surgery_datetime' => 'required|date',
            'surgeon_name'     => 'required|string|max:255',
            'status'           => 'nullable|in:agendada,confirmada,em_andamento,finalizada,adiada,cancelada',
        ]);

        $patient = Patient::firstOrCreate(['name' => $data['patient_name']]);

        $start = Carbon::parse($data['surgery_datetime']);
        $end   = (clone $start)->addMinutes(60);

        $surgery->update([
            'patient_id'   => $patient->id,
            'surgeon_name' => $data['surgeon_name'],
            'start_at'     => $start,
            'end_at'       => $end,
            'status'       => $request->input('status','agendada'),
        ]);

        event(new SurgeryUpdated($surgery->fresh('patient')));
        return redirect()->route('surgeries.index')->with('success','Cirurgia atualizada.');
    }

    public function destroy(Surgery $surgery)
    {
        $surgery->delete();
        event(new SurgeryUpdated($surgery));
        return back()->with('success','Cirurgia removida.');
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
