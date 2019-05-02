<?php
declare(strict_types=1);

namespace Specification\Akeneo\Pim\Enrichment\Component\Product\Factory\NonExistentValuesFilter;

use Akeneo\Pim\Enrichment\Component\Product\Query\GetExistingReferenceDataCodes;
use Akeneo\Pim\Enrichment\Component\Product\Factory\NonExistentValuesFilter\NonExistentReferenceDataValuesFilter;
use Akeneo\Pim\Structure\Component\AttributeTypes;
use Akeneo\Pim\Enrichment\Component\Product\Factory\NonExistentValuesFilter\OnGoingFilteredRawValues;
use PhpSpec\ObjectBehavior;

/**
 * @author    Tamara Robichet <tamara.robichet@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
final class NonExistentReferenceDataValuesFilterSpec extends ObjectBehavior
{
    public function let(GetExistingReferenceDataCodes $getExistingReferenceDataCodes) {
        $this->beConstructedWith($getExistingReferenceDataCodes);
    }

    public function it_has_a_type()
    {
        $this->shouldHaveType(NonExistentReferenceDataValuesFilter::class);
    }

    public function it_filters_reference_data_values(GetExistingReferenceDataCodes $getExistingReferenceDataCodes)
    {
        $ongoingFilteredRawValues = OnGoingFilteredRawValues::fromNonFilteredValuesCollectionIndexedByType(
            [
                AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT => [
                    'a_select' => [
                        [
                            'identifier' => 'product_A',
                            'values' => [
                                '<all_channels>' => [
                                    '<all_locales>' => 'option_toto'
                                ],
                            ]
                        ],
                        [
                            'identifier' => 'product_B',
                            'values' => [
                                'ecommerce' => [
                                    'en_US' => 'option_tata'
                                ],
                            ]
                        ]
                    ]
                ],
                AttributeTypes::REFERENCE_DATA_MULTI_SELECT => [
                    'a_multi_select' => [
                        [
                            'identifier' => 'product_A',
                            'values' => [
                                'ecommerce' => [
                                    'en_US' => ['MiChel', 'sardou'],
                                ],
                                'tablet' => [
                                    'en_US' => ['jean', 'claude', 'van', 'damm'],
                                    'fr_FR' => ['des', 'fraises'],

                                ],
                            ]
                        ]
                    ]
                ],
                AttributeTypes::TEXTAREA => [
                    'a_description' => [
                        [
                            'identifier' => 'product_B',
                            'values' => [
                                '<all_channels>' => [
                                    '<all_locales>' => 'plop'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $optionCodes = [
            'a_select' => [
                'option_toto',
                'option_tata',
            ],
            'a_multi_select' => [
                'michel',
                'sardou',
                'jean',
                'claude',
                'van',
                'damm',
                'des',
                'fraises'
            ]
        ];

        $getExistingReferenceDataCodes->fromReferenceDataNameAndCodes('reference_data_1', $optionCodes)->willReturn(
            [
                'a_select' => ['option_toto'],
                'a_multi_select' => ['michel', 'fraises']
            ]
        );

        /** @var OnGoingFilteredRawValues $filteredCollection */
        $filteredCollection = $this->filter($ongoingFilteredRawValues);
        $filteredCollection->filteredRawValuesCollectionIndexedByType()->shouldBeLike(
            [
                AttributeTypes::REFERENCE_DATA_MULTI_SELECT => [
                    'a_select' => [
                        [
                            'identifier' => 'product_A',
                            'values' => [
                                '<all_channels>' => [
                                    '<all_locales>' => 'option_toto'
                                ],
                            ]
                        ],
                        [
                            'identifier' => 'product_B',
                            'values' => [
                                'ecommerce' => [
                                    'en_US' => ''
                                ],
                            ]
                        ]
                    ]
                ],
                AttributeTypes::REFERENCE_DATA_SIMPLE_SELECT => [
                    'a_multi_select' => [
                        [
                            'identifier' => 'product_A',
                            'values' => [
                                'ecommerce' => [
                                    'en_US' => ['michel'],
                                ],
                                'tablet' => [
                                    'en_US' => [],
                                    'fr_FR' => [1 => 'fraises'],

                                ],
                            ]
                        ]
                    ]
                ],
            ]
        );
    }
}
