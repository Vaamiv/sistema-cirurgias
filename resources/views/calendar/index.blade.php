@extends('layouts.app')
@section('content')
<div id="calendar" class="bg-white rounded-lg shadow p-4"></div>

<script type="module">
import { Calendar } from 'fullcalendar';
import dayGridPlugin from 'fullcalendar/daygrid';
import timeGridPlugin from 'fullcalendar/timegrid';
import interactionPlugin from 'fullcalendar/interaction';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
  wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
  wsPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 6001),
  wssPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 6001),
  forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
  enabledTransports: ['ws','wss'],
});

const el = document.getElementById('calendar');
const calendar = new Calendar(el, {
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'timeGridWeek',
  locale: 'pt-br',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  events: '/api/surgeries',
  eventContent: function(arg){
    const s = arg.event.extendedProps;
    const med = s.surgeon_name ? "\nDr(a). " + s.surgeon_name : '';
    return { text: arg.event.title + med };
  }
});
calendar.render();

window.Echo.channel('surgeries').listen('.surgery.updated', () => {
  calendar.refetchEvents();
});
</script>
@endsection
