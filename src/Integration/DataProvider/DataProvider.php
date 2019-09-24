<?php

namespace Integration\DataProvider;

use Integration\ResponseInterface;

/**
 * Class DataProvider
 *
 * @link https://www.dataprovider.com/documentation/api/
 */
class DataProvider implements ResponseInterface
{
    /**
     * @var array
     */
    private $_config;

    /**
     * DataProvider constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * Returns a response from external service
     *
     * @param array $request
     *
     * @return array
     */
    public function getResponse(array $request)
    {
        // returns a response from external service

        return ['example'];
    }
}
