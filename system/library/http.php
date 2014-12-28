<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

/* [Searchanise] */

class Http {
	const TIMEOUT = 30;
	private static $_curl_ssl_support = false;
	private static $_curl_followlocation_support = false;
	private static $_headers = '';
	private static $_error = '';

	public static function request($method, $url, $data = array(), $extra = array()) {
		list($url, $data) = self::prepareData($url, $data);

		if (self::curlExists()) {
		   $content = self::curlRequest($method, $url, $data, $extra);
		} else {
			$content = self::socketRequest($method, $url, $data, $extra);
		}

		return $content;
	}

	private static function prepareData($url, $data) {
		$components = parse_url($url);

		$upass = '';
		if (!empty($components['user'])) {
			$_pass = !empty($components['pass']) ? ':' . $components['pass'] : '';
			$upass = $components['user'] . $_pass . '@';
		}

		if (empty($components['path'])) {
			$components['path'] = '/';
		}

		$port = empty($components['port']) ? '' : (':' . $components['port']);

		$url = $components['scheme'] . '://' . $upass . $components['host'] 
			   . $port . $components['path'];

		if (!empty($components['query'])) {
			parse_str($components['query'], $args);

			if (!empty($data) && !is_array($data) && !empty($args)) {
				echo('Http: incompatible data type passed');
				exit;
			}

			$data = array_merge($args, $data);
		}

		return array($url, is_array($data) ? http_build_query($data) : $data);
	}

	private static function curlExists() {
		if (!function_exists('curl_init')) {
			return false;
		}

		$ver = curl_version();
		if (is_array($ver)) {
			self::$_curl_ssl_support = !empty($ver['ssl_version']);
		} else {
			self::$_curl_ssl_support = (strpos($ver, 'SSL') !== false);
		}

		self::$_curl_followlocation_support = !ini_get('open_basedir') && !ini_get('safe_mode');

		return true;
	}

	private static function curlRequest($method, $url, $data, $extra) {
		$ch = curl_init();

		if (!empty($extra['basic_auth'])) {
			curl_setopt($ch, CURLOPT_USERPWD, implode(':', $extra['basic_auth']));
		}
		if (!empty($extra['referer'])) {
			curl_setopt($ch, CURLOPT_REFERER, $extra['referer']);
		}
		if (!empty($extra['ssl_cert'])) {
			curl_setopt($ch, CURLOPT_SSLCERT, $extra['ssl_cert']);
			if (!empty($extra['ssl_key'])) {
				curl_setopt($ch, CURLOPT_SSLKEY, $extra['ssl_key']);
			}
		}
		if (!empty($extra['timeout'])) {
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $extra['timeout']);
		}
		if (!empty($extra['headers'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $extra['headers']);
		}
		if (!empty($extra['cookie'])) {
			curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $extra['cookies']));
		}
		if (!empty($extra['binary_transfer'])) {
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		}

		if ($method == 'GET') {
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
			$url .= '?' . $data;
		} else {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		if (self::$_curl_followlocation_support) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if (!empty($extra['return_handler'])) {
			return $ch;
		}

		$content = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if (empty($error)) {
			$content = self::parseContent($content);
			$content = self::processHeadersRedirect($method, $url, $extra, $content);

		} else {
			self::setError('curl', $error, $errno);
			$content = false;
		}

		return $content;
	}

	private static function parseContent($content) {
		list(self::$_headers, $content) = preg_split("/\R\R/", $content, 2);

		if (strpos(self::$_headers, '100 Continue') !== false) {
			list(self::$_headers, $content) = preg_split("/\R\R/", $content, 2);
		}

		return $content;
	}

	private static function processHeadersRedirect($method, $url, $extra, $content) {
		if (!self::$_curl_followlocation_support && 
			preg_match("/Location:\s([^\s]*)/", self::$_headers, $matches)) {

			$extra['redirects_left'] = isset($extra['redirects_left']) ? $extra['redirects_left'] : 10;

			if ($extra['redirects_left'] < 1) {
				self::setError('curl', 'HTTPS: redirect limit exceeded', 0);
				$content = false;
			} else {
				$extra['redirects_left']--;
				$newUrl = self::completeUrl($matches[1], $url);

				$extra['return_handler'] = false;

				$content = self::curlRequest($method, $newUrl, '', $extra);
			}
		}

		return $content;
	}

	private static function setError($transport, $error, $errno) {
		self::$_error = "$transport ($errno): $error";
	}

	private static function completeUrl($url, $base_url) {
		$result = false;
		$parts = parse_url($url);
		$base_parts = parse_url($base_url);

		if (!empty($parts['scheme']) && !empty($parts['host'])) {
			$result = $url;
		} elseif (!empty($parts['path']) && 
				  !empty($base_parts['scheme']) && 
				  !empty($base_parts['host'])
			) {

			$result = $base_parts['scheme'] . '://' . $base_parts['host'];

			if (!empty($base_parts['port'])) {
				$result .= ':' . $base_parts['port'];
			}

			$pathinfo = pathinfo($base_parts['path']);
			$result .= $pathinfo['dirname'] . '/' . $parts['path'];

			if (!empty($parts['query'])) {
				$result .= '?' . $parts['query'];
			}
		}

		return $result;
	}

	private static function socketRequest($method, $url, $data, $extra) {
		$components = parse_url($url);

		if (empty($components['port'])) {
			$components['port'] = 80;
		}

		$sh = @fsockopen(
			$components['host'],
			$components['port'],
			$errno,
			$error,
			!empty($extra['timeout']) ? $extra['timeout'] : self::TIMEOUT
		);

		if ($sh) {
			if ($method == 'GET') {
				$postUrl = $components['path'] . '?' . $data;
			} else {
				$postUrl = $components['path'];
			}

			fputs($sh, "$method $postUrl HTTP/1.0\r\n");
			fputs($sh, "Host: $components[host]\r\n");

			if (!empty($extra['referer'])) {
				fputs($sh, 'Referer: ' . $extra['referer'] . "\r\n");
			}
			if (!empty($extra['headers'])) {
				foreach ($extra['headers'] as $header) {
					if (stripos($header, 'Content-type:') === 0) {
						$content_type_set = true;
					}
					fputs($sh, $header . "\r\n");
				}
			}
			if (!empty($extra['basic_auth'])) {
				fputs($sh, "Authorization: Basic " . base64_encode(implode(':', $extra['basic_auth'])) . "\r\n");
			}
			if (!empty($extra['cookie'])) {
				fputs($sh, 'Cookie: ' . implode('; ', $extra['cookies']) . "\r\n");
			}

			if ($method == 'POST') {
				if (empty($content_type_set)) {
					fputs($sh, 'Content-type: application/x-www-form-urlencoded' ."\r\n");
				}

				fputs($sh, 'Content-Length: ' . strlen($data) ."\r\n");
				fputs($sh, "\r\n");
				fputs($sh, $data);
			} else {
				fputs($sh, "\r\n");
			}

			$content = '';
			while (!feof($sh)) {
				$content .= fread($sh, 65536);
			}
			fclose($sh);

			if (!empty($content)) {
				$content = self::parseContent($content);
			}
		} else {
			self::setError('socket', $error, $errno);
			$content = false;
		}

		return $content;
	}
}

?>