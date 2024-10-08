<?php

declare(strict_types=1);

namespace Videohostingcz;

/**
 * This is the LinkGenerator class.
 *
 * Implements interface ILinkGenerator.
 */
class LinkGenerator implements ILinkGenerator
{
	/** @var array<string> Available parameters */
	private $availableParams = ['filename', 'expires', 'token', 'limitsize', 'limitid', 'rate', 'rateafter', 'ip', 'ipm', 'sparams'];


	/**
	 * @copydoc ILinkGenerator::generate
	 */
	public function generate($uri, $params = [], $secrecy = 'secretkey'): string
	{
		// append protocol if not specified
		if (strncmp($uri, 'http', 4) != 0) {
			$uri = 'https://' . $uri;
		}

		// check to use only available params
		foreach ($params as $key => $value) {
			if (!in_array($key, $this->availableParams, true)) {
				throw new LinkGeneratorException("Use undefined parameter '" . $key . "'!");
			}
		}

		// check value of parameter 'expires'
		if (isset($params['expires']) && !ctype_digit((string) $params['expires'])) {
			throw new LinkGeneratorException("Parameter 'expires' contains non number character!");
		}

		// check value of parameter 'limitsize'
		if (isset($params['limitsize']) && !preg_match('/^[1-9][0-9]*$/', (string) $params['limitsize'])) {
			throw new LinkGeneratorException("Parameter 'limitsize' contains invalid characters!");
		}

		// check value of parameter 'rate'
		if (isset($params['rate']) && !preg_match('/^[1-9][0-9]*[km]?$/', (string) $params['rate'])) {
			throw new LinkGeneratorException("Parameter 'rate' contains invalid characters!");
		}

		// check value of parameter 'rateafter'
		if (isset($params['rateafter']) && !preg_match('/^[0-9]+[kmg]$/', (string) $params['rateafter'])) {
			throw new LinkGeneratorException("Parameter 'rateafter' contains invalid characters!");
		}

		// check value of parameter 'ip'
		if (isset($params['ip']) && (filter_var($params['ip'], FILTER_VALIDATE_IP) === false)) {
			throw new LinkGeneratorException("Parameter 'ip' contains invalid IP address!");
		}

		// check value of parameter 'imp'
		if (isset($params['ipm']) &&
			(!isset($params['ip']) || !$this->isCorrectParameterIpm((string) $params['ip'], (int) $params['ipm']))
		) {
			throw new LinkGeneratorException("Parameter 'ipm' contains invalid number or missing parameter 'ip'!");
		}

		// check value of parameter 'sparams'
		if (isset($params['sparams']) && !preg_match('/^[a-z]+(,[a-z]+)*$/', (string) $params['sparams'])) {
			throw new LinkGeneratorException("Parameter 'sparams' contains invalid value!");
		}

		// compute value of parameter 'signature'
		if (isset($params['sparams'])) {
			$params['signature'] = $this->computeParameterSignature($uri, $params, $secrecy);
		}

		// return URL value
		if (count($params)) {
			$url = sprintf('%s?%s', $uri, http_build_query($params));
		} else {
			$url = $uri;
		}

		return $url;
	}


	/**
	 * Check if parametr ipm is valid.
	 *
	 * @param string $ip Value of IP
	 * @param int $mask Value of site mask
	 */
	private function isCorrectParameterIpm(string $ip, int $mask): bool
	{
		$maxRange = 32;

		if (filter_var($ip, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV4]) === false) {
			$maxRange = 128; // max value of mask for IPv6
		}

		$result = filter_var($mask, FILTER_VALIDATE_INT, [
			'options' => ['min_range' => 0, 'max_range' => $maxRange],
		]);
		return is_bool($result) === true ? $result : true; // PHPStan + Condig-standard hack
	}


	/**
	 * Compute value of parameter 'signature'.
	 *
	 * @param string $uri URI to file
	 * @param array<string, string|int> $params Parameters
	 * @param string $secrecy A hiden secret key
	 * @throws LinkGeneratorException
	 */
	private function computeParameterSignature($uri, $params, $secrecy): string
	{
		$array = [];
		$availableParams = array_merge($this->availableParams, ['path']);
		$list = explode(',', (string) $params['sparams']);

		foreach ($list as $key) {
			// check to use only available property key
			if (!in_array($key, $availableParams, true)) {
				throw new LinkGeneratorException(sprintf("Use undefined property key '%s' in parameter 'sparam'!", $key));
			}

			if ($key == 'path') {
				// insert special property 'path'
				$array['path'] = basename($uri);
				continue;
			}

			// check if exists property value
			if (!isset($params[$key])) {
				throw new LinkGeneratorException(sprintf("Use undefined property value of key '%s' in parameter 'sparam'!", $key));
			}

			// set value of key to signature
			$array[$key] = $params[$key];
		}

		return hash_hmac('sha1', http_build_query($array), $secrecy);
	}
}
