<?php
// Headers obrigatórios para SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Header especial para o Nginx: força o bypass do proxy_buffering dinamicamente
header('X-Accel-Buffering: no');

// Garante que o PHP descarregue qualquer buffer restante
while (ob_get_level() > 0) {
    ob_end_flush();
}

$counter = 0;

while (true) {
    if (connection_aborted()) {
        break;
    }

    $counter++;
    $time = date('H:i:s');

    $payload = json_encode(['time' => $time, 'counter' => $counter]);
    echo "data: {$payload}\n\n";

    // Força o envio imediato
    flush();

    sleep(2);
}