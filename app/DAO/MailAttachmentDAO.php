<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 27/10/2021
 * Time: 10:50
 */

namespace App\DAO;


use App\Exceptions\AttachmentFileNotFoundException;
use App\Exceptions\AttachmentMaxFileSizeLimitExceededException;
use Illuminate\Support\Facades\File;

class MailAttachmentDAO
{
    protected $path, $name, $mime;

    /**
     * @var int Max attachment size in Megabytes
     */
    private $maxAttachmentSize = 10;
    /**
     * MailAttachmentDAO constructor.
     * @param $path
     * @param $name
     * @param null $mime
     * @throws AttachmentFileNotFoundException
     * @throws AttachmentMaxFileSizeLimitExceededException
     */
    public function __construct($path, $name, $mime = null)
    {
        $this->path = $path;
        $this->name = $name;
        $this->mime = $mime;

        $this->checkAttachment();
    }

    /**
     * @throws AttachmentFileNotFoundException
     * @throws AttachmentMaxFileSizeLimitExceededException
     */
    private function checkAttachment()
    {
        if(!File::exists($this->path)) {
            throw new AttachmentFileNotFoundException();
        }
        if(File::size($this->path) / (1000000) > $this->maxAttachmentSize) {
            throw new AttachmentMaxFileSizeLimitExceededException($this->maxAttachmentSize);
        }
    }
}