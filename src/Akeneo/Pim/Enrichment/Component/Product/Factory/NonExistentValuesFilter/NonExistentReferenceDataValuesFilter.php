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

        var_dump('values', $referenceDataValues);

        if (empty($referenceDataValues)) {
            return $onGoingFilteredRawValues;
        }

        $formattedOptions = $this->formatReferenceDataOptions($referenceDataValues);
        $existingCodes = $this->getExistingCodes($formattedOptions);

        var_dump($existingCodes);

        return $onGoingFilteredRawValues->addFilteredValuesIndexedByType([]);
    }

    // Need the name of the reference data
    private function getExistingCodes(array $formattedOptions): array
    {
        $existingOptionCodes = $this->getExistingReferenceDataCodes->fromReferenceDataNameAndCodes();
    }

    private function formatReferenceDataOptions(array $referenceDataValues): array
    {
        $optionsByCode = [];

        foreach ($referenceDataValues as $attributeCode => $valueCollection) {
            foreach ($valueCollection as $values) {
                foreach ($values['values'] as $channel => $channelValues) {
                    foreach ($channelValues as $locale => $value) {
                        if (is_array($value)) {
                            foreach ($value as $optionCode) {
                                $optionsByCode[$attributeCode][] = strtolower($optionCode);
                            }
                        } else {
                            $optionsByCode[$attributeCode][] = strtolower($value);
                        }
                    }
                }
            }
        }

        $uniqueOptionCodes = [];

        foreach ($optionsByCode as $attributeCode => $optionCodeForThisAttribute) {
            $uniqueOptionCodes[$attributeCode] = array_unique($optionCodeForThisAttribute);
        }

        return $uniqueOptionCodes;
    }
}
