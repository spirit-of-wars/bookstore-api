<?php

namespace App\EntitySupport\Pagination;

use App\Constants;
use App\Service\Entity\EntityService;

/**
 * Class EntitiesPageLoader
 * @package App\EntitySupport\Pagination
 */
class EntitiesPageLoader
{
    /** @var EntityService */
    private $entityService;

    /** @var array */
    private $filters;

    /** @var array */
    private $options;

    /**
     * EntitiesPageLoader constructor.
     * @param EntityService $entityService
     */
    public function __construct($entityService)
    {
        $this->entityService = $entityService;
        $this->filters = [];
        $this->options = [];
    }

    /**
     * @param array $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return EntitiesPage
     */
    public function loadPage()
    {
        $repository = $this->entityService->getRepository();
        $report = $repository->findByAttributeFilters($this->filters, $this->options);
        $totalCount = $report['count'];
        $list = $report['list'];
        $limit = (integer)$this->options['limit'] ?? Constants::PAGE_LIMIT;
        $pagesCount = ceil($totalCount / $limit);
        $pageNum = (integer)$this->options['page'] ?? 0;

        return new EntitiesPage([
            'list' => $list,
            'pagesCount' => $pagesCount,
            'pageSize' => $limit,
            'page' => $pageNum,
            'itemsCount' => $totalCount,
        ]);
    }
}
