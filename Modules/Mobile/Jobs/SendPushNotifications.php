<?php

namespace Modules\Mobile\Jobs;

use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Mobile\Entities\Push;

class SendPushNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $clients;
    protected $title;
    protected $message;
    /**
     * @var Push
     */
    protected $push;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Push $push, $clients = [])
    {
        $this->push = $push;
        $this->clients = $clients;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if(!$this->push->start()) {
                return;
            }

            Logger::log(LogEvent::NOTICE, 'pushes', LogPriority::LOW, "O processamento de pushes #{$this->push->id} foi iniciado.");

            foreach($this->clients as $index => $c) {
                $pushNotification = (new \Modules\Mobile\Services\PushNotificationService($c, $this->push->title, $this->push->message, []));
                $pushNotification->send();
                $this->push->iterate($index+1);
            }

            Logger::log(LogEvent::NOTICE, 'pushes', LogPriority::LOW, "O processamento de pushes #{$this->push->id} foi finalizado.");
            $this->push->end();
        }
        catch (\Exception $e){
            Logger::log(LogEvent::ERROR, 'pushes', LogPriority::HIGH, "Ocorreu um erro ao processar a fila de pushes.\n{$e->getMessage()}");
        }
    }
}
