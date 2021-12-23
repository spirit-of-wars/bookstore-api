<?php

namespace App\EntitySupport\Pagination;

use App\Constants;
use App\EntitySupport\Common\BaseEntity;
use App\Interfaces\EntitySerializerInterface;
use App\Service\Serializer\EntitySerializer;

/**
 * Class EntitiesPage
 * @package App\EntitySupport\Pagination
 */
class EntitiesPage
{
    /** @var BaseEntity[] */
    private $pageContent;

    /** @var int */
    private $pagesCount;

    /** @var int */
    private $pageSize;

    /** @var int */
    private $page;

    /** @var int */
    private $itemsCount;

    /** @var EntitySerializerInterface|callable */
    private $serializer;

    /**
     * EntitiesPage constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->pageContent = $config['list'] ?? [];
        $this->pagesCount = $config['pagesCount'] ?? 0;
        $this->pageSize = $config['pageSize'] ?? Constants::PAGE_LIMIT;
        $this->page = $config['page'] ?? 1;
        $this->itemsCount = $config['itemsCount'] ?? 0;

        $this->serializer = new EntitySerializer();
    }

    /**
     * @param EntitySerializerInterface|callable $serializer
     */
    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $content = $this->pageContent;
        if ($this->serializer) {
            if ($this->serializer instanceof EntitySerializerInterface) {
                $content = $this->serializer->serializeList($content);
            } elseif (is_callable($this->serializer)) {
                $content = [];
                foreach ($this->pageContent as $entity) {
                    $content[] = call_user_func_array($this->serializer, [$entity]);
                }
            }
        }

        return [
            'paging' => [
                'pagesCount' => $this->pagesCount,
                'pageSize' => $this->pageSize,
                'page' => $this->page,
                'itemsCount' => $this->itemsCount,
            ],
            'list' => $content,
        ];
    }
}
