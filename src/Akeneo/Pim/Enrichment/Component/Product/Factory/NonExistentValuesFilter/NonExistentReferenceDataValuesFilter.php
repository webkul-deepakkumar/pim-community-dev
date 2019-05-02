<?php


namespace Akeneo\Pim\Enrichment\Component\Product\Factory\NonExistentValuesFilter;


use Akeneo\Pim\Enrichment\Component\Product\Query\GetExistingReferenceDataCodes;
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

        $optionCodes = [];

        foreach ($referenceDataValues as $attributeCode => $valueCollection) {
            foreach ($valueCollection as $values) {
                foreach ($values['values'] as $channel => $channelValues) {
                    foreach ($channelValues as $locale => $value) {
                        if (is_array($value)) {
                            foreach ($value as $optionCode) {
                                $optionCodes[$attributeCode][] = strtolower($optionCode);
                            }
                        } else {
                            $optionCodes[$attributeCode][] = strtolower($value);
                        }
                    }
                }
            }
        }

        $uniqueOptionCodes = [];
        foreach ($optionCodes as $attributeCode => $optionCodeForThisAttribute) {
            $uniqueOptionCodes[$attributeCode] = array_unique($optionCodeForThisAttribute);
        }

        return $onGoingFilteredRawValues->addFilteredValuesIndexedByType([]);
    }
}
