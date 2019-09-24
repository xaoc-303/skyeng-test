<?php

namespace Integration\Decorator;

use DateTime;
use Exception;
use Integration\DataProvider\DataProvider;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DataProviderCacheable
 */
class DataProviderCacheable extends DataProviderDecorator
{
    const CACHE_EXPIRES_AT = '+1 day';

    /**
     * @var CacheItemPoolInterface
     */
    private $_cache;

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @param DataProvider $dataProvider
     * @param CacheItemPoolInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(DataProvider $dataProvider, CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        parent::__construct($dataProvider);

        $this->_cache = $cache;
        $this->_logger = $logger;
    }

    /**
     * @param array $input
     *
     * @return array
     * @throws Exception
     */
    public function getResponse(array $input)
    {
        $cacheKey = $this->getCacheKey($input);
        $cacheItem = $this->getCacheItem($cacheKey);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        try {
            $result = $this->_dataProvider->getResponse($input);
        } catch (Exception $e) {
            $this->_logger->error('DataProviderCacheable - Error getting data from dataProvider');
            throw $e;
        }

        $cacheItem
            ->set($result)
            ->expiresAt(
                (new DateTime())->modify(self::CACHE_EXPIRES_AT)
            );

        if (!$this->_cache->save($cacheItem)) {
            $this->_logger->error('DataProviderCacheable - Caching fail');
        }

        return $result;
    }

    /**
     * @param array $input
     *
     * @return string
     */
    private function getCacheKey(array $input)
    {
        return md5((string)json_encode($input, JSON_NUMERIC_CHECK|JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param string $key
     *
     * @return \Psr\Cache\CacheItemInterface
     */
    private function getCacheItem(string $key)
    {
        // аргумент всегда не null
        return $this->_cache->getItem($this->getCacheKey($key));
    }
}
