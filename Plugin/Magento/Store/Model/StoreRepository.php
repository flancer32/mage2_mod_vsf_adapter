<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Plugin\Magento\Store\Model;

class StoreRepository
{
    /**
     * Replace 'rest' with 'ru' to process REST requests from VSF API.
     *
     * @param \Magento\Store\Model\StoreRepository $subject
     * @param $code
     * @return array
     */
    public function beforeGetActiveStoreByCode(
        \Magento\Store\Model\StoreRepository $subject,
        $code
    ) {
        if ($code == 'rest') {
            $code = 'ru';
        }
        return [$code];
    }
}