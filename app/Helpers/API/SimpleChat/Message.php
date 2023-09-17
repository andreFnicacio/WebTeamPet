<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 13/11/2020
 * Time: 13:43
 */

namespace App\Helpers\API\SimpleChat;

use GuzzleHttp\Client;

class Message
{
    private $nome;
    private $message;
    private $celular;
    private $path;
    private $extension;

    const TOKEN = '306340c37f8849ef3521dd6a8afe2d9a';
    const URL = 'https://simplechat.com.br/api/send/';
    const URL_IMAGE = 'https://simplechat.com.br/api/send-image/';

    public function __construct($nome, $message, $celular, $path = null, $extension = 'jpg') {
        $this->nome = $nome;
        $this->message = $message;
        $this->celular =  "+55" . preg_replace( '/[^0-9]/', '', $celular);
        $this->path = $path;
        $this->extension = $extension;
    }

    public function url() {
        if($this->path) {
            return self::URL_IMAGE . self::TOKEN;
        }

        return self::URL . self::TOKEN;
    }

    public function send()
    {
        $http = new Client();

        $response = null;

        $form = [
            'nome' => $this->nome,
            'message' => $this->message,
            'celular' => $this->celular,
        ];

        if($this->path) {
            $form['file'] = $this->path;
            $form['extensao'] = $this->extension;
        }

        $response = $http->request('POST', $this->url(), [
            'form_params' => $form
        ]);

        return $response;
    }
}