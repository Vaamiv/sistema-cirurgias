<?php
use Illuminate\Support\Facades\Broadcast;
Broadcast::channel('surgeries', fn() => true); // ajuste para canais privados quando houver auth
