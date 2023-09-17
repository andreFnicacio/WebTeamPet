<?php

namespace App\Http\Util\Superlogica;

class Logger {

	public static function log($message,$alert) {
		if(!is_string($message)) {
                    $message = json_encode($message, JSON_PRETTY_PRINT);
		}
		
		$message .= "\n";
		$message = date("d-m-Y H:i:s") . "[$alert] -> " . $message;

		file_put_contents(__DIR__ . "/../../log/log.txt", $message.PHP_EOL , FILE_APPEND | LOCK_EX);
	}
}