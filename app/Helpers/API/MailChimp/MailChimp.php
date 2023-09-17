<?php
/**
 * Created by PhpStorm.
 * User: Breno Grillo
 * Date: 06/06/2019
 * Time: 20:23
 */

namespace App\Helpers\API\MailChimp;

use DrewM\MailChimp\MailChimp as M;

class MailChimp
{
    private $apiKey = "aaea25974f4eeed71c3c459061f14d46-us16";
    private $listId = null;
    private $templateId = null;
    private $subject = null;
    private $message = null;
    private $email = null;
    private $campaign_title = null;
    private $fromName = "Lifepet Saúde";
    private $fromEmail = "atendimento@lifepet.com.br";


    public static function prepare($listId, $templateId, $email, $subject, $message, $fromName = null, $fromEmail = null)
    {
        $mailer = new self();
        $mailer->listId = $listId;
        $mailer->templateId = $templateId;
        $mailer->email = $email;
        $mailer->subject = $subject;
        $mailer->message = $message;

        if($fromName) {
            $mailer->fromName = $fromName;
        }

        if($fromName) {
            $mailer->fromName = $fromName;
        }

        return $mailer;
    }

    public function send($mergeFields = [])
    {
        $apiMailer = new M($this->apiKey);
        $subscriberHash = $apiMailer->subscriberHash($this->email);
        $subscribed = $apiMailer->get("lists/{$this->listId}/members/{$subscriberHash}");

        //Cadastra o cliente na "Audience"
        if (!isset( $subscribed['status'] ) || 'subscribed' !== $subscribed['status']) {
            $apiMailer->post("lists/{$this->listId}/members", [
                'email_address' => $this->email,
                'status'        => 'subscribed',
                'merge_fields'  => $mergeFields
            ]);
        }

        //Criar campanha/Enviar
        $campaign = $apiMailer->post('campaigns', [
            'type' => 'regular',
            'recipients' => [
                'list_id' => $this->listId,
                'segment_opts' => [
                    'match' => 'all',
                    'conditions' => [
                        [
                            'condition_type' => 'EmailAddress',
                            'field' => 'EMAIL',
                            'op' => 'is',
                            'value' => $this->email
                        ]
                    ]
                ],
            ],
            'settings' => [
                'title' => $this->campaign_title,
                'subject_line' => $this->subject,
                'from_name' => $this->fromName,
                'reply_to' => $this->fromEmail,
                'template_id' => $this->templateId,
            ],
        ]);

        if (!isset( $campaign['id'] ) || !$campaign['id']) {
            return false;
        }


        $campaignId = $campaign['id'];

        $result = $apiMailer->post("campaigns/{$campaignId}/actions/send");

        return is_bool($result) && $result;
    }

    public static function novoCliente($email, $senha)
    {
        $subject = "Bem-vindo à Lifepet!";

        $mailer = self::prepare("a717903e73", 102595, $email, $subject, "");
        return $mailer->send([
            'MMERGE7' => $email,
            'MMERGE8' => $senha
        ]);
    }

    public static function novoClienteInsideSales($cliente, $senha)
    {
        $subject = "Bem-vindo à Lifepet!";

        $mailer = self::prepare("a717903e73", 106263, $cliente->email, $subject, "");
        $mailer->campaign_title = "Boas Vindas - Inside Sales - " . $cliente->email;
        return $mailer->send([
            'MMERGE7' => $cliente->email,
            'MMERGE8' => $senha,
            'MMERGE3' => route('clientes.proposta', ['id' => $cliente->id, 'numProposta' => 0]),
        ]);
    }
}