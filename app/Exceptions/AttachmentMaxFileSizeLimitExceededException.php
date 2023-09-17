<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 26/10/2021
 * Time: 15:35
 */

namespace App\Exceptions;


class AttachmentMaxFileSizeLimitExceededException extends \Exception
{
    public function __construct($maxFileSize)
    {
        parent::__construct("The maximum size of a file that can be attached ($maxFileSize MB) was exceeded.", 0, null);
    }
}