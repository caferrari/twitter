<?php
/* Twitter Simple Class
*
* Copyright (c) 2002-2010, Carlos André Ferrari <caferrari@gmail.com>.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
* notice, this list of conditions and the following disclaimer.
*
* * Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in
* the documentation and/or other materials provided with the
* distribution.
*
* * Neither the name of Sebastian Bergmann nor the names of his
* contributors may be used to endorse or promote products derived
* from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRIC
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.

	Usage:
	
	$t = new Twitter('usuario', 'senha');
	$t->send('Testando classe do twitter');
	$t->send('Testando classe do twitter2', 'http://www.to.gov.br');

*/

class Twitter{

	var $user;
	var $pass;

	public function __construct($user, $pass)
	{
		$this->user = $user;
		$this->pass = $pass;
	}

	private function geraLink($url){
		$url = "http://migre.me/xml.php?url=" . urlencode($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$o = curl_exec($ch);
		try{
			$xml = simplexml_load_string($o);
			return ": " . $xml->migre;
		}catch (Exception $e) {
			return "";
		}
	}

	private function mkTweet($texto, $url, $user, $pass){
		if (function_exists("curl_init")){
			$link = ($url) ? geraLink($url) : '';

			$texto = trim($texto);
			if (strlen ($texto . $link) > 140){
				$tweet = trim(substr($texto, 0, 137 - strlen($link)));
				if ($texto != $novo) $novo .= "...";
				$tweet .= $link;
			}else $tweet = $texto . $link;
						
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://twitter.com/statuses/update.json");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:' ) );
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array("status" => "{$tweet}"));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
			return curl_exec($ch);
		}
		return false;
	}
	
	public function send($texto, $url='')
	{
		return $this->mkTweet($texto, $url, $this->user, $this->pass);
	}
}

