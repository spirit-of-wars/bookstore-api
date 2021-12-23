<?php

namespace App\Service;

use App\Service\Entity\Product\CommonService as ProductCommonService;
use App\Service\Entity\Data1CService;

/**
 * Class Data1CSynchronizationService
 * @package App\Service
 *
 * @property-read ProductCommonService ProductCommonService
 * @property-read Data1CService Data1CService
 */
class Data1CSynchronizationService extends Service
{
    /**
     * @return array
     */
    protected static function subscribedServicesMap()
    {
        return [
            'ProductCommonService' => ProductCommonService::class,
            'Data1CService' => Data1CService::class,
        ];
    }

    /**
     * @param array $dataRequestArray
     * @return string
     */
    public function synchronize(array $dataRequestArray)
    {
        $productService = $this->ProductCommonService;
        $data1CService = $this->Data1CService;

        foreach ($dataRequestArray as $data1CAttributes) {
            $update = false;
            $product = $productService->getByFullNameAndIsbn($data1CAttributes["Product"], $data1CAttributes['isbn']);
            if ($product) {
                $update = true;
            } else {
                $product = $productService->getByFullName($data1CAttributes["Product"]);
            }

            $currentData1C = $product ? $data1CService->getData1CByProduct($product) : null;
            if ($update) {
                $data1CService->updateData1C($currentData1C, $data1CAttributes);
            } else {
                $data1CService->createNextData1C($currentData1C, $data1CAttributes);
            }
        }

        return $data1CService->saveData1CArray();
    }
}
