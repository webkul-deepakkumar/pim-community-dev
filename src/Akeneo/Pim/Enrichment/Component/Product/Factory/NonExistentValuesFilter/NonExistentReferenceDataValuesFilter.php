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

        $indexedByCode = $this->formatReferenceDataOptionsByCode($referenceDataValues);
        $filteredCodes = $this->filterByExistingCodes($indexedByCode);
        $indexedByType = $this->formatReferenceDataOptionsByType($filteredCodes);

        return $onGoingFilteredRawValues->addFilteredValuesIndexedByType($indexedByType);
    }

    private function filterByExistingCodes(array $formattedOptions): array
    {
        $existingCodes = [];

        foreach($formattedOptions as $attributeCode => $option)
        {
            $existingOptionCodes = $this->getExistingReferenceDataCodes->fromReferenceDataNameAndCodes(
                $option['reference_data_name'],
                $option['codes']
            );

            $existingCodes[$attributeCode]['codes'] = array_map('strtolower', $existingOptionCodes);
        }

        return $existingCodes;
    }

    private function formatReferenceDataOptionsByCode(array $referenceDataValues): array
    {
        $optionsByCode = [];

        foreach ($referenceDataValues as $attributeCode => $valueCollection) {
            foreach ($valueCollection as $values) {
                $referenceDataName = $values['properties']['reference_data_name'];
                $optionsByCode[$attributeCode]['reference_data_name'] = $referenceDataName;
                foreach ($values['values'] as $channel => $channelValues) {
                    foreach ($channelValues as $locale => $value) {
                        if (is_array($value)) {
                            foreach ($value as $optionCode) {
                                $optionsByCode[$attributeCode]['codes'][] = strtolower($optionCode);
                            }
                        } else {
                            $optionsByCode[$attributeCode]['codes'][] = strtolower($value);
                        }
                    }
                }
            }
        }

        return $optionsByCode;
    }

    private function formatReferenceDataOptionsByType(array $indexedByCode): array
    {
        $optionsByType = [
            AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT => [],
            AttributeTypes::REFERENCE_DATA_MULTI_SELECT => []
        ];

        return $optionsByType;
    }
}
