<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 21/10/2021
 * Time: 16:23
 */

namespace App\Services;


use App\DAO\MailDAO;
use App\Exceptions\AttachmentFileNotFoundException;
use App\Exceptions\AttachmentMaxFileSizeLimitExceededException;
use App\Exceptions\ViewNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class MailerService
{
    /**
     * @param Model $related
     * @param $subject
     * @param $to
     * @param $view
     * @param array $data
     * @param string $from
     * @param string $fromName
     * @throws ViewNotFoundException
     * @throws \Throwable
     * @return MailDAO
     */
    public function compose(Model $related, $subject, $to, $view, $data = [], $from = 'noreply@lifepet.com.br', $fromName = 'Lifepet')
    {
        return new MailDAO($subject, $view, $to, $from, $data, $related);
    }

    public function send(MailDAO $mail)
    {

    }
}