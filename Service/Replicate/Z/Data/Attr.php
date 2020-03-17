<?php
/**
 * Structure for attribute data used in the code subtree (...\Service\Replicate\...).
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Z\Data;

use Flancer32\VsfAdapter\Service\Replicate\Z\Data\Attr\Option as DAttrOption;

/**
 * Structure for attribute data used in the code subtree (...\Service\Replicate\...).
 */
class Attr
{
    /** @var string */
    public $code;
    /** @var int */
    public $id;
    /** @var string */
    public $inputType;
    /** @var bool */
    public $isComparable;
    /** @var bool */
    public $isUserDefined;
    /** @var bool */
    public $isVisibleOnFront;
    /** @var string */
    public $label;
    /** @var DAttrOption[] */
    public $options;
}
