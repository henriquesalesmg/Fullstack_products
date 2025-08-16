<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Listener para evento de usuário registrado.
 * Reflete OCP e DIP.
 */
class SendUserRegisteredNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserRegistered $event)
    {
        // Exemplo: logar registro do usuário
        Log::info('Usuário registrado: ' . $event->user->email);
        // Aqui pode enviar e-mail, notificação, etc.
    }
}
