<?php

namespace Adadgio\GearBundle\Component\Api;

/**
 * The api request object is injected into the controller by the
 * api kernel event listener when an annotation "[at]Api is found
 * and the method argument is correctly type hinted with \ApiRequest
 */
class ApiRequest
{
    /**
     * @var string Request method
     */
    protected $method;

    /**
     * @var string Request method
     */
    protected $headers;

    /**
     * @var string Request content
     */
    protected $content;

    /**
     * Get request method.
     *
     * @return string Http method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set request method.
     *
     * @param string Http method
     * @return \ApiRequest
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get request headers.
     *
     * @return array Http headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set request headers.
     *
     * @param  array  Http headers
     * @return object \ApiRequest
     */
    public function setHeaders(array $headers = array())
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Get request content.
     *
     * @return strong Request content
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Get request content.
     *
     * @param  string
     * @return object \ApiRequest
     */
    public function setContent(array $content = array())
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Gheck if the headers contain json application type.
     *
     * @todo I thing contentType(s) is an array and must be looped! Warning here
     * @return boolean
     */
    public function isJson()
    {
        // $contentType = isset($this->headers['content-type']) ? $this->headers['content-type'] : null;
        //
        // return (strpos('application/json', $contentType[0]) > -1) ? true : false;
        return true;
    }

    /**
     * Get content parameter.
     *
     * @param  string Parameter key
     * @return mixed  Content parameter value.
     */
    public function get($key)
    {
        return isset($this->content[$key]) ? $this->content[$key] : null;
    }

    /**
     * Has content parameter.
     *
     * @return boolean
     */
    public function has($key)
    {
        return isset($this->content[$key]);
    }
}
