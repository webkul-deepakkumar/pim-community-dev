<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Component\Product\Factory\EmptyValuesCleaner;

/**
 * @author    Anael Chardan <anael.chardan@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ChainedEmptyValuesCleanerInterface
{
    public function cleanAll(OnGoingCleanedRawValues $onGoingCleanedRawValues): OnGoingCleanedRawValues;
}
