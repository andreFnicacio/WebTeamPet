<?php
namespace App\Helpers\API\LifeQueueClient;

class LifeQueueClient
{
    /**
     * @var Request
     */
    private $queue;

    /**
     * @var array
     */
    private $payload = [];

    /**
     * @var boolean
     */
    private $queued = false;

    /**
     * @var array
     */
    private $data = [];

    /**
     * Construtor da classe
     *
     * @param string $environment Environment::STAGING|Environment::PRODUCTION
     */
    public function __construct(string $environment)
    {
        $this->queue = new Request($environment);
    }

    /**
     * Define a data e hora para o disparo
     *
     * @param \DateTime $date
     * @return string|null
     */
    public function schedule(\DateTime $date): LifeQueueClient
    {
        $this->payload['schedule_date'] = $date->format('Y-m-d H:i:s');
        return $this;
    }

    /**
     * Define a URL de retorno para o webhook
     *
     * @param string $url
     * @return string|null
     */
    public function returnUrl(string $url): LifeQueueClient
    {
        $this->payload['return_url'] = $url;
        return $this;

    }

    /**
     * Cria um novo item do tipo email para a fila de disparo
     *
     * @param string $subject
     * @param string $content
     * @param array $address
     * @param array $cc
     * @param array $bcc
     * @return LifeQueueClient
     */
    public function email(string $subject,
        string $content,
        array $address,
        array $cc = [],
        array $bcc = []): LifeQueueClient {
        $this->addEmail(
            [
                'content' => $content,
                'subject' => $subject,
                'address' => $address,
                'cc' => $cc,
                'bcc' => $bcc,
            ]
        );
        return $this;
    }

    /**
     * Envia o registro para a fila de disparo
     *
     * @throws LifeQueueClientException
     * @return void
     */
    public function toQueue()
    {
        if (empty($this->payload)) {
            throw new LifeQueueClientException('Informe o tipo de registro antes de enviar os dados.');
        }

        if (!empty($this->data)) {
            $this->payload['extra']['data'] = $this->data;
        }

        if ($this->queue->request($this->payload) === true) {
            $this->queued = true;
        }
    }

    /**
     * Retorna a resposta da fila
     *
     * @return void
     */
    public function response(): ?object
    {
        return $this->queue->response();
    }

    /**
     * Retorna o status da fila
     *
     * @return boolean
     */
    public function queued(): boolean
    {
        return $this->queued;
    }

    /**
     * Cria um novo registro do tipo e-mails
     *
     * @param array $params
     * @return void
     */
    private function addEmail(array $params): LifeQueueClient
    {
        if (empty($params['content'] ?? null)) {
            throw new LifeQueueClientException('Informe o conteúdo.');
        }

        if (!isset($params['address'])) {
            throw new LifeQueueClientException('Informe o endereço de e-mail.');
        }

        if (!\is_array($params['address'])) {
            throw new LifeQueueClientException('O endereço de e-mail deve ser um vetor.');
        }

        $address = [];
        foreach ($params['address'] as $a) {
            if (!isset($a['name'], $a['email'])) {
                throw new LifeQueueClientException('Informe e-mail e nome no vetor de endereço.');
            }
            $address[] = ['name' => $a['name'], 'email' => $a['email']];
        }

        $bcc = [];
        foreach ($params['bcc'] as $a) {
            if (!isset($a['name'], $a['email'])) {
                throw new LifeQueueClientException('Informe e-mail e nome no vetor de endereço bcc');
            }
            $bcc[] = ['name' => $a['name'], 'email' => $a['email']];
        }

        $cc = [];
        foreach ($params['cc'] as $a) {
            if (!isset($a['name'], $a['email'])) {
                throw new LifeQueueClientException('Informe e-mail e nome no vetor de endereço cc');
            }
            $cc[] = ['name' => $a['name'], 'email' => $a['email']];
        }

        if (empty($params['subject'] ?? null)) {
            throw new LifeQueueClientException('Informe o assunto');
        }

        $this->payload['type'] = 'email';
        $this->payload['content'] = \base64_encode($params['content']);
        $this->payload['extra'] = [
            'address' => $address,
            'bcc' => $bcc,
            'cc' => $cc,
            'subject' => $params['subject'],
        ];

        return $this;
    }

    /**
     * Adiciona um novo push
     *
     * @param array $params
     * @return LifeQueueClient
     */
    public function addPush(array $params): LifeQueueClient
    {
        if (empty($params['device_token'] ?? null)) {
            throw new LifeQueueClientException('Informe o token do dispositivo.');
        }

        if (empty($params['content'] ?? null)) {
            throw new LifeQueueClientException('Informe o conteúdo da notificação.');
        }

        if (empty($params['title'] ?? null)) {
            throw new LifeQueueClientException('Informe o título da notificação');
        }

        $this->payload['type'] = 'push';
        $this->payload['content'] = \base64_encode($params['content']);
        $this->payload['extra'] = [
            'title' => $params['title'],
            'device_token' => $params['device_token'],
        ];

        return $this;
    }

    /**
     * Cria um push
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $content
     * @return LifeQueueClient
     */
    public function push(string $deviceToken, string $title, string $content): LifeQueueClient
    {
        $this->addPush(
            [
                'device_token' => $deviceToken,
                'title' => $title,
                'content' => $content,
            ]
        );
        return $this;
    }

    /**
     * Payload data
     *
     * @param string $key
     * @param string $value
     * @return LifeQueueClient
     */
    public function addData(string $key, string $value): LifeQueueClient
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Click action da notificação
     *
     * @param string $action
     * @return LifeQueueClient
     */
    public function clickAction(string $action): LifeQueueClient
    {
        $this->payload['extra']['click_action'] = $action;
        return $this;
    }

    /**
     * Icone
     *
     * @param string $icon
     * @return LifeQueueClient
     */
    public function icon(string $icon): LifeQueueClient
    {
        $this->payload['extra']['icon'] = $icon;
        return $this;
    }

    /**
     * Som
     *
     * @param string $sound
     * @return LifeQueueClient
     */
    public function sound(string $sound): LifeQueueClient
    {
        $this->payload['extra']['sound'] = $sound;
        return $this;
    }

    /**
     * Cor de fundo
     *
     * @param string $color
     * @return LifeQueueClient
     */
    public function color(string $color): LifeQueueClient
    {
        $this->payload['extra']['color'] = $color;
        return $this;
    }

    /**
     * Define a prioridade do PUSH
     *
     * @param string|null $priority
     * @return LifeQueueClient
     */
    public function priority(?string $priority = 'high'): LifeQueueClient
    {
        $this->payload['extra']['priority'] = $priority;
        return $this;
    }

    /**
     * Badge
     *
     * @param string $badge
     * @return LifeQueueClient
     */
    public function badge(string $badge): LifeQueueClient
    {
        $this->payload['extra']['badge'] = $badge;
        return $this;
    }

    /**
     * Debug
     *
     * @return void
     */
    public function debug()
    {
        print_r($this->payload);
    }
}