<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Plugin\Magento\Eav\Model;

class AttributeRepository
{

    /**
     * Add filter to exclude Temando Shipping attributes.
     *
     * @param \Magento\Eav\Model\AttributeRepository $subject
     * @param $entityTypeCode
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return array
     */
    public function beforeGetList(
        \Magento\Eav\Model\AttributeRepository $subject,
        $entityTypeCode,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        // create filter "(`main_table`.attribute_code NOT LIKE "ts_%")"
        $filter = new \Magento\Framework\Api\Filter();
        $filter->setConditionType('nlike');
        $filter->setField('main_table.attribute_code');
        $filter->setValue('ts_%');
        // create empty filters group and add new filter into
        $groupNew = new \Magento\Framework\Api\Search\FilterGroup();
        $groupNew->setFilters([$filter]);
        // add new group into existing filters groups
        $groups = $searchCriteria->getFilterGroups();
        $groups[] = $groupNew;
        $searchCriteria->setFilterGroups($groups);

        return [$entityTypeCode, $searchCriteria];
    }
}