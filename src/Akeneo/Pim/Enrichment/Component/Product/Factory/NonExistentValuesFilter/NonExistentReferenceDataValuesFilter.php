<?php


namespace Akeneo\Pim\Enrichment\Component\Product\Factory\NonExistentValuesFilter;


use Akeneo\Pim\Enrichment\Bundle\Product\Query\Sql\GetExistingReferenceDataCodes;
use Akeneo\Pim\Structure\Component\AttributeTypes;

class NonExistentReferenceDataValuesFilter implements NonExistentValuesFilter
{
    /** @var GetExistingReferenceDataCodes */
    private $getExistingReferenceDataCodes;

    public function __construct(GetExistingReferenceDataCodes $getExistingReferenceDataCodes)
    {
        $this->getExistingReferenceDataCodes = $getExistingReferenceDataCodes;
    }

    public function filter(OnGoingFilteredRawValues $onGoingFilteredRawValues): OnGoingFilteredRawValues
    {
        $referenceDataValues = $onGoingFilteredRawValues->notFilteredValuesOfTypes(
            AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT,
            AttributeTypes::REFERENCE_DATA_MULTI_SELECT
        );

        if (empty($referenceDataValues)) {
            return $onGoingFilteredRawValues;
        }

    }
}
