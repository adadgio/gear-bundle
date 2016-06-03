<?php

namespace Adadgio\GearBundle\Connector\NodeRed;

use Symfony\Component\HttpFoundation\Request;
use Adadgio\GearBundle\Component\Http\Curl;
use Adadgio\GearBundle\Exception\GearException;

class NodeRedConnector
{
    /**
     * @var string Node red server host.
     */
    protected $host;

    /**
     * @var string Node red server port.
     */
    protected $port;

    /**
     * @var string Node red server protocol.
     */
    protected $protocol;

    /**
     * @var array Node red http auth (user, pass).
     */
    protected $auth;

    /**
     * @var integer Response code
     */
    protected $code;

    /**
     * @var string Response data
     */
    protected $response;

    /**
     * Service constructor
     */
    public function __construct(array $config)
    {
        $this->auth = $config['http_auth'];
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->protocol = $config['protocol'];
    }

    /**
     * Get response.
     *
     * @return string Node red response
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get response.
     *
     * @return string Node red response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Create a new payload object from request. Quite usefull
     * when node red posts back data to our controllers and for
     * dispatching payload received event(s).
     *
     * @param  object \Request
     * @return object \PayloadInterface
     */
    public static function createPayloadFromRequest(Request $request)
    {
        switch ($request->getMethod()) {
            case 'GET':
                throw new \GearException('Cannot create \Payload from request, GET method is not yet supported');
            break;
            case 'POST':
                // then the data is in the request body
                $data = json_decode($request->getContent(), true);
            break;
            default:
                throw new \GearException('Cannot create \Payload from request, must be POST or GET, "%s" given', $method);
            break;
        }

        return new Payload($data);
    }

    /**
     * Send a the payload to a node red enpdoint.
     *
     * @param string Http method
     * @param string Relative uri
     */
    public function send($method, $uri, PayloadInterface $payload)
    {
        $curl = new Curl();
        $curl->setHeader('content-type', 'application/json');

        // optional basic auth
        if (null !== $this->auth) {
            $curl->setBasicAuthentication($this->auth['user'], $this->auth['pass']);
        }

        $endpoint = $this->getEndpoint($uri);

        if ($method === Curl::GET) {
            // perform a GET request
            $curl->get($endpoint, $payload->getParameters());

        } else if ($method === Curl::POST) {
            // else make a POST request
            $curl->post($endpoint, $payload->getParameters());

        } else {
            throw new GearException('Unsupported method "%s", must use GET or POST', $method);
        }

        $this->code = $curl->getCode();
        $this->response = $curl->getResponse();

        if ($this->code !== 200) {
            throw new GearException('Cant reach NodeRed as hosts "%s" (code %d: %s). Check your logs for details', array($this->host, $this->code, $this->response));
        }
        return $this;
    }

    /**
     * Get full url endpoint to send the payload to.
     *
     * @return string.
     */
    private function getEndpoint($uri)
    {
        return $this->protocol.trim($this->host, '/').':'.$this->port.'/'.ltrim($uri, '/');
    }
}
