<?php


namespace Akeneo\Pim\Enrichment\Component\Product\Factory\EmptyValuesCleaner;


use Akeneo\Pim\Structure\Component\AttributeTypes;

class EmptyReferenceDataValuesCleaner implements EmptyValuesCleaner
{
    public function clean(OnGoingCleanedRawValues $onGoingCleanedRawValues): OnGoingCleanedRawValues
    {
        $referenceDataValues = $onGoingCleanedRawValues->nonCleanedValuesOfTypes(AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT, AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT);

        return $onGoingCleanedRawValues->addCleanedValuesIndexedByType([]);
    }
}
