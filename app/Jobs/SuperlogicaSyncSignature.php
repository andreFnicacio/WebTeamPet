<?php

namespace App\Jobs;

use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\Models\Cancelamento;
use App\Models\Clientes;
use App\Models\Pets;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SuperlogicaSyncSignature implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Clientes
     */
    protected $cliente;
    protected $verifiesPaymentStatus = false;
    protected $forceSync = false;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Clientes $cliente, $verifiesPaymentStatus = false, $forceSync = false)
    {
        $this->cliente = $cliente;
        $this->verifiesPaymentStatus = $verifiesPaymentStatus;
        $this->forceSync = $forceSync;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($verbose = false)
    {
        $logger = new Logger('superlogica');
        $message = 'Iniciando processo de exportação do cliente para o superlógica: ' . $this->cliente->nome_cliente;
        $logger->register(LogEvent::NOTICE, LogPriority::LOW, $message);
        if($verbose) {
            echo $message;
        }

        if(!$this->cliente->ativo) {
            $message = 'O cliente está inativo. Não será sincronizado: ' . $this->cliente->nome_cliente;
            $logger->register(LogEvent::WARNING, LogPriority::LOW, $message, $this->cliente->id);
            if($verbose) {
                echo $message;
            }
            return;
        }

        if($this->verifiesPaymentStatus) {
            if($this->cliente->statusPagamento !== Clientes::PAGAMENTO_EM_DIA) {
                $message = 'O cliente está inadimplente. Não será sincronizado: ' . $this->cliente->nome_cliente;
                $logger->register(LogEvent::WARNING, LogPriority::LOW, $message, $this->cliente->id);
                if($verbose) {
                    echo $message;
                }
                return;
            }
        }

        if(!$this->forceSync) {
            if($this->cliente->last_sync) {
                if(!Carbon::now()->subHour(1)->gte($this->cliente->last_sync)) {
                    $message = 'O cliente já foi sincronizado na última hora. Abortando: ' . $this->cliente->nome_cliente;
                    $logger->register(LogEvent::WARNING, LogPriority::LOW, $message, $this->cliente->id);
                    if($verbose) {
                        echo $message;
                    }
                    return;
                }
            }
        }


        $pets = $this->cliente->pets()->where('ativo', 1)->get();
        foreach ($pets as $pet) {
            /**
             * @var Pets $pet
             */
            //Não sincronizar clientes de conveniadas
            $contrato = $pet->petsPlanosAtual()->first();
            if(!$contrato) {
                $message = 'O pet não possui um contrato atual: ' . $pet->nome_pet;
                $logger->register(LogEvent::WARNING, LogPriority::LOW, 'O pet não possui um contrato atual: ' . $pet->nome_pet, $this->cliente->id);
                if($verbose) {
                    echo $message;
                }
                continue;
            }

            if($contrato->id_conveniada) {
                $message = 'O pet é referente a uma conveniada, portanto não é faturável: ' . $pet->nome_pet;
                $logger->register(LogEvent::WARNING, LogPriority::LOW, $message,  $this->cliente->id);
                if($verbose) {
                    echo $message;
                }
                continue;
            }

            if($pet->cancelamentoAgendado()) {
                $message = 'O pet possui um cancelamento agendado. Abortando: ' . $pet->nome_pet;
                $logger->register(LogEvent::WARNING, LogPriority::LOW, $message, $this->cliente->id);
                if($verbose) {
                    echo $message;
                }
                continue;
            }

            if(!$pet->ativo) {
                $message = 'O pet está inativo. Abortando: ' . $pet->nome_pet;
                $logger->register(LogEvent::WARNING, LogPriority::LOW, 'O pet está inativo. Abortando: ' . $pet->nome_pet, $this->cliente->id);
                if($verbose) {
                    echo $message;
                }
                continue;
            }

            try {
                //Novo contrato
                $pet->assinarSuperlogica();

                //Atualizar contrato
                $contrato->atualizarSuperlogica();
            } catch (\Exception $e) {
                $message = "O cadastro do pet não pôde ser sincronizado com o superlógica. Detalhes abaixo.\n";
                $message .= json_encode([
                    'cliente' => $this->cliente->loggable(),
                    'pet' => $pet->loggable(),
                    'exception' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'message' => $e->getMessage()
                    ]
                ]);

                Logger::log(LogEvent::WARNING, 'superlogica', LogPriority::MEDIUM, $message, null, 'clientes', $this->cliente->id);
                return;
            }
        }

        $this->cliente->last_sync = Carbon::now();
        $this->cliente->update();
    }
}
