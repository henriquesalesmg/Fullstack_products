<?php

namespace App\Listeners;

use App\Events\ProductCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener para evento de produto criado.
 * Reflete OCP e DIP.
 */
class SendProductCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ProductCreated $event)
    {
        // Exemplo: logar criação do produto
        Log::info('Produto criado: ' . $event->product->name);
        // Aqui pode enviar e-mail, notificação, etc.
    }
}
