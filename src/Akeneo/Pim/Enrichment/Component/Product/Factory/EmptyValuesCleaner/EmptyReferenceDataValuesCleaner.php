<?php


namespace Akeneo\Pim\Enrichment\Component\Product\Factory\EmptyValuesCleaner;


use Akeneo\Pim\Structure\Component\AttributeTypes;

class EmptyReferenceDataValuesCleaner implements EmptyValuesCleaner
{
    public function clean(OnGoingCleanedRawValues $onGoingCleanedRawValues): OnGoingCleanedRawValues
    {
        $referenceDataValues = $onGoingCleanedRawValues->nonCleanedValuesOfTypes(AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT, AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT);

        if (empty($referenceDataValues)) {
            return $onGoingCleanedRawValues;
        }

        $cleanedValues = [];

        foreach ($referenceDataValues as $attributeCode => $valueCollection) {
            foreach ($valueCollection as $values) {
                $multiSelectValues = [];
                $simpleSelectValues = [];

                foreach ($values['values'] as $channel => $channelValues) {
                    foreach ($channelValues as $locale => $value) {
                        if (is_array($value)) {
                            if (!empty($value)) {
                                $multiSelectValues[$channel][$locale] = $value;
                            }
                        } else {
                            if (trim($value) !== '') {
                                $simpleSelectValues[$channel][$locale] = $value;
                            }
                        }
                    }
                }

                if ($multiSelectValues !== []) {
                    $cleanedValues[AttributeTypes::REFERENCE_DATA_MULTI_SELECT][$attributeCode][] = [
                        'identifier' => $values['identifier'],
                        'values' => $multiSelectValues,
                    ];
                }
                if ($simpleSelectValues !== []) {
                    $cleanedValues[AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT][$attributeCode][] = [
                        'identifier' => $values['identifier'],
                        'values' => $simpleSelectValues,
                    ];
                }
            }
        }

        return $onGoingCleanedRawValues->addCleanedValuesIndexedByType($cleanedValues);
    }
}
