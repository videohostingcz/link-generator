<?php
namespace Videohostingcz;

/**
 * This is the LinkGenerator interface.
 */
interface ILinkGenerator
{

    /**
     * Generate URL for file.
     *
     * Available parameters:
     *  - filename - Specifies the name of the downloaded file. Used to download the file.
     *  - token    - Token is a random string embedded in a link to improve signature security in combination
     *               with parameters 'signature' and 'sparams'.
     *  - expires  - Link expiration time. After this time, access to the file is no longer allowed.
     *  - size     - File size limitation. Used to play part of a file. The value is the required size in bytes,
     *               kilobytes (k or K suffix), megabytes (suffix m or M), or in percent (suffix p or P).
     *  - ip       - Restrict file access to only specific parts of the network. Used in conjunction with the 'ipm'
     *               parameter. The value can be specific IPv4 or IPv6.
     *  - ipm      - Specifies the network mask. It is used in conjunction with the ip parameter. The default value
     *               for IPv4 is 24 bits, for IPv6 it is 64 bits.
     *  - sparams  - Specifies the list and sequence of parameters used to generate the signature. Allowed parameters
     *               are 'path', 'filename', 'token', 'expires', 'size', 'ip' and 'ipm'. The individual parameters are
     *               separated by a comma.
     *
     * @param string $uri URI to file returned by API (call GET /api/v2/files/:id)
     * @param array $params Array with parameters
     * @param string $secrecy A hidden secret key
     * @return return string URL value
     */
    public function generate($uri, $params = [], $secrecy = 'secretkey');
}
