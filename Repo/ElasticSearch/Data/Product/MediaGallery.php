<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product;

/**
 * Data object to represent data structure for product image info in ElasticSearch index.
 */
class MediaGallery
{
    /** @var string path to image ("/b/l/black300.jpg") */
    public $image;
}
