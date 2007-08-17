<?php

/**
 * Represenets an HTTPResponse returned by a controller.
 */
class HTTPResponse extends Object {
	protected static $status_codes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Request Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
	);
	
	protected $statusCode = 200;
	protected $headers = array();
	protected $body = null;
	
	function setStatusCode($code) {
		if(isset(self::$status_codes[$code])) $this->statusCode = $code;
		else user_error("Unrecognised HTTP status code '$code'", E_USER_WARNING);
	}
	
	function setBody($body) {
		$this->body = $body;
	}
	function getBody() {
		return $this->body;
	}
	
	function addHeader($header, $value) {
		$this->headers[$header] = $value;
	}
	
	function redirect($dest) {
		$this->statusCode = 302;
		$this->headers['Location'] = $dest;
	}

	/**
	 * Send this HTTPReponse to the browser
	 */
	function output() {
		if($this->statusCode == 302 && headers_sent($file, $line)) {
			$url = $this->headers['Location'];
			echo 
			"<p>Redirecting to <a href=\"$url\" title=\"Please click this link if your browser does not redirect you\">$url... (output started on $file, line $line)</a></p>
			<meta http-equiv=\"refresh\" content=\"1; url=$url\" />
			<script type=\"text/javascript\">setTimeout('window.location.href = \"$url\"', 50);</script>";
		} else {
			if(!headers_sent()) {
				header("HTTP/1.1 $this->statusCode " . self::$status_codes[$this->statusCode]);
				foreach($this->headers as $header => $value) {
					header("$header: $value");
				}
			}
			
			echo $this->body;
		}
	}
	
	/**
	 * Returns true if this response is "finished", that is, no more script execution should be done.
	 * Specifically, returns true if a redirect has already been requested
	 */
	function isFinished() {
		return $this->statusCode == 302 || $this->statusCode == 301;
	}
	
}