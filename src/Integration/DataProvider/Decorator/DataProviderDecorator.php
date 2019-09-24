<?php

namespace Integration\Decorator;

use Integration\DataProvider\DataProvider;
use Integration\ResponseInterface;

class DataProviderDecorator implements ResponseInterface
{
    /**
     * @var DataProvider
     */
    protected $_dataProvider;

    /**
     * @param DataProvider $dataProvider
     */
    public function __construct(DataProvider $dataProvider)
    {
        $this->_dataProvider = $dataProvider;
    }

    /**
     * @param array $input
     *
     * @return array
     */
    public function getResponse(array $input)
    {
        return $this->_dataProvider->getResponse($input);
    }
}
