<?php

namespace Akeneo\Pim\Structure\Component\Model;

use Akeneo\Tool\Component\Localization\Model\TranslationInterface;

/**
 * Association type translation interface
 *
 * @author    Marie Bochu <marie.bochu@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface AssociationTypeTranslationInterface extends TranslationInterface
{
    /**
     * Set label
     *
     * @param string $label
     *
     * @return AssociationTypeTranslationInterface
     */
    public function setLabel($label);

    /**
     * Get the label
     *
     * @return string
     */
    public function getLabel();
}
