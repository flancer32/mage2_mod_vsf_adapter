<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Category\A;

use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category as ECat;

/**
 * Populate `children_data` attribute with hierarchical data.
 */
class Indexer
{
    /**
     * Populate `children_data` attribute with hierarchical data.
     *
     * @param \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category[] $esCats
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category[]
     */
    public function exec($esCats)
    {
        $result = [];
        [$mapById, $mapByLevel, $mapByTeam, $maxLevel] = $this->mapByIdLevel($esCats);
        for ($i = $maxLevel; $i > 0; $i--) {
            if (isset($mapByLevel[$i])) {
                $line = $mapByLevel[$i];
                foreach ($line as $catId) {
                    /** @var ECat $cat */
                    $cat = $mapById[$catId];
                    $id = $cat->id;
                    $childrenCount = 0;
                    if (isset($mapByTeam[$id])) {
                        $children = '';
                        $childrenData = [];
                        $childrenCount++;
                        foreach ($mapByTeam[$id] as $teamCatId) {
                            /** @var ECat $teamCat */
                            $teamCat = $mapById[$teamCatId];
                            $childrenCount += $teamCat->children_count;
                            $children .= "$teamCatId,";
                            $childrenData[] = $teamCat;
                        }
                        $cat->children = substr($children, 0, -1); // cut the last ','
                        $cat->children_data = $childrenData;
                    }
                    $cat->children_count = $childrenCount;
                }
            }
        }
        return $mapById;
    }

    /**
     * Index categories by level and by id.
     *
     * @param \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category[] $esCats
     * @return array
     */
    private function mapByIdLevel($esCats)
    {
        $byId = [];
        $byLevel = [];
        $byTeam = [];
        $maxLevel = 0;
        foreach ($esCats as $one) {
            // by id
            $id = $one->id;
            $byId[$id] = $one;
            // by level
            $level = $one->level;
            $maxLevel = ($level > $maxLevel) ? $level : $maxLevel;
            if (!isset($byLevel[$level])) {
                $byLevel[$level] = [];
            }
            $byLevel[$level][] = $id;
            // by team
            $parentId = $one->parent_id;
            if (!isset($byTeam[$parentId])) {
                $byTeam[$parentId] = [];
            }
            $byTeam[$parentId][] = $id;
        }
        return [$byId, $byLevel, $byTeam, $maxLevel];
    }
}
