<?php
/**
 * @copyright Copyright (c) 2013 Tomasz Kuter <evolic_at_interia_dot_pl>
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EvlCalendar\View\Helper;

use Zend\I18n\View\Helper\AbstractTranslatorHelper as BaseAbstractHelper;
use EvlCalendar\Entity\Holiday;

/**
 * Return string representation for major's type given as number
 */
class HolidayType extends BaseAbstractHelper
{
    public function __invoke($type)
    {
        switch ($type) {
            case Holiday::TYPE_NATIONAL:
                return 'national';
            case Holiday::TYPE_ADDITIONAL:
                return 'additional';
            default:
                throw new \Exception(sprintf("Provided unknown holiday type `%d`"), $type);
        }
    }
}
