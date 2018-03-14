<?php
namespace Videohostingcz;

/**
 * This is the LinkGenerator class.
 *
 * Implements interface ILinkGenerator.
 */
class LinkGenerator implements ILinkGenerator
{

    /**
     * @var array Available parameters
     */
    private $availableParams = ['filename', 'expires', 'token', 'expires', 'limitsize', 'limitid', 'rate', 'ip', 'ipm', 'sparams'];

    /**
     * @copydoc ILinkGenerator::generate
     */
    public function generate($uri, $params = [], $secrecy = 'secretkey')
    {
        // append protocol if not specified
        if (strncmp($uri, "http", 4) != 0) {
            $uri = 'https://' . $uri;
        }

        // check to use only available params
        foreach ($params as $key => $value) {
            if (!in_array($key, $this->availableParams)) {
                throw new LinkGeneratorException("Use undefined parameter '" . $key . "'!");
            }
        }

        // check value of parameter 'expires'
        if (isset($params['expires']) && !ctype_digit($params['expires'])) {
            throw new LinkGeneratorException("Parameter 'expires' contains non number character!");
        }

        // check value of parameter 'limitsize'
        if (isset($params['limitsize']) && !preg_match("/^[1-9][0-9]*$/", $params['limitsize'])) {
            throw new LinkGeneratorException("Parameter 'limitsize' contains invalid characters!");
        }

        // check value of parameter 'rate'
        if (isset($params['rate']) && !preg_match("/^[1-9][0-9]*[k]?$/", $params['rate'])) {
            throw new LinkGeneratorException("Parameter 'rate' contains invalid characters!");
        }

        // check value of parameter 'ip'
        if (isset($params['ip']) && (filter_var($params['ip'], FILTER_VALIDATE_IP) === false)) {
            throw new LinkGeneratorException("Parameter 'ip' contains invalid IP address!");
        }

        // check value of parameter 'imp'
        if (isset($params['ipm']) && (!isset($params['ip']) || !$this->isCorrectParameterIpm($params['ip'], $params['ipm']))) {
            throw new LinkGeneratorException("Parameter 'ipm' contains invalid number or missing parameter 'ip'!");
        }

        // check value of parameter 'sparams'
        if (isset($params['sparams']) && !preg_match("/^[a-z]+(,[a-z]+)*$/", $params['sparams'])) {
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
     * @param string $mask Value of site mask
     * @return bool
     */
    private function isCorrectParameterIpm($ip, $mask)
    {
        $maxRange = 32;

        if (filter_var($ip, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV4]) === false) {
            $maxRange = 128; // max value of mask for IPv6
        }

        return (filter_var($mask, FILTER_VALIDATE_INT, [
                'options' => [
                    'min_range' => 0,
                    'max_range' => $maxRange]]) === false) ? false : true;
    }

    /**
     * Compute value of parameter 'signature'.
     *
     * @param string $uri URI to file
     * @param array $params Parameters
     * @param string $secrecy A hiden secret key
     * @return type
     * @throws LinkGeneratorException
     */
    private function computeParameterSignature($uri, $params, $secrecy)
    {
        $array = [];
        $availableParams = array_merge($this->availableParams, ['path']);
        $list = explode(",", $params['sparams']);

        foreach ($list as $key) {
            // check to use only available property key
            if (!in_array($key, $availableParams)) {
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
