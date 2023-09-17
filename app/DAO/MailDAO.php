<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 30/11/2021
 * Time: 16:18
 */

namespace App\DAO;


use App\Exceptions\ViewNotFoundException;
use Illuminate\Support\Str;

class MailDAO
{
    protected $subject;
    protected $view;
    protected $data;
    protected $to;
    protected $from;
    protected $attachments;
    protected $related;
    protected $hash;

    /**
     * MailDAO constructor.
     * @param $subject
     * @param $view
     * @param $to
     * @param $from
     * @param null $related
     * @throws ViewNotFoundException
     */
    public function __construct($subject, $view, $to, $from, $data = [], $related = null)
    {
        if(!view()->exists($view)) {
            throw new ViewNotFoundException();
        }

        $this->subject = $subject;
        $this->view = $view;
        $this->to = $to;
        $this->from = $from;
        $this->attachments = [];
        $this->related = $related;
        $this->hash = (string) Str::uuid();
    }

    public function addAttachment(MailAttachmentDAO $attachment)
    {
        $this->attachments[] = $attachment;
    }
}