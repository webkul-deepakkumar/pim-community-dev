<?php

namespace Specification\Akeneo\Pim\Enrichment\Component\Product\Validator\Constraints;

use Akeneo\Tool\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Channel\Component\Model\ChannelInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ValueInterface;
use Akeneo\Pim\Enrichment\Component\Product\Validator\Constraints\ScopableValue;
use Prophecy\Argument;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ScopableValueValidatorSpec extends ObjectBehavior
{
    function let(IdentifiableObjectRepositoryInterface $channelRepository, ExecutionContextInterface $context)
    {
        $this->beConstructedWith($channelRepository);
        $this->initialize($context);
    }

    function it_does_not_validate_if_object_is_not_a_product_value(
        $context,
        ScopableValue $constraint
    ) {
        $object = new \stdClass();
        $context->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate($object, $constraint);
    }

    function it_does_not_add_violations_if_value_is_scopable_and_has_an_existing_scope(
        $context,
        $channelRepository,
        ValueInterface $value,
        ChannelInterface $existingChannel,
        ScopableValue $constraint
    ) {
        $value->isScopable()->willReturn(true);
        $value->getScopeCode()->willReturn('mobile');
        $channelRepository->findOneByIdentifier('mobile')->willReturn($existingChannel);
        $context->buildViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violations_if_value_is_scopable_and_does_not_have_scope(
        $context,
        ValueInterface $value,
        ScopableValue $constraint,
        ConstraintViolationBuilderInterface $violation
    ) {
        $value->isScopable()->willReturn(true);
        $value->getScopeCode()->willReturn(null);
        $value->getAttributeCode()->willReturn('attributeCode');

        $violationData = [
            '%attribute%' => 'attributeCode'
        ];
        $context->buildViolation($constraint->expectedScopeMessage, $violationData)
            ->shouldBeCalled()
            ->willReturn($violation);

        $this->validate($value, $constraint);
    }

    function it_adds_violations_if_value_is_not_scopable_and_a_scope_is_provided(
        $context,
        ValueInterface $value,
        ScopableValue $constraint,
        ConstraintViolationBuilderInterface $violation
    ) {
        $value->isScopable()->willReturn(false);
        $value->getScopeCode()->willReturn('aScope');
        $value->getAttributeCode()->willReturn('attributeCode');

        $violationData = [
            '%attribute%' => 'attributeCode'
        ];
        $context->buildViolation($constraint->unexpectedScopeMessage, $violationData)
            ->shouldBeCalled()
            ->willReturn($violation);

        $this->validate($value, $constraint);
    }

    function it_adds_violations_if_value_is_scopable_and_its_scope_does_not_exist(
        $context,
        $channelRepository,
        ValueInterface $value,
        ScopableValue $constraint,
        ConstraintViolationBuilderInterface $violation
    ) {
        $value->isScopable()->willReturn(true);
        $value->getScopeCode()->willReturn('inexistingChannel');
        $value->getAttributeCode()->willReturn('attributeCode');
        $channelRepository->findOneByIdentifier('inexistingChannel')->willReturn(null);

        $violationData = [
            '%attribute%' => 'attributeCode',
            '%channel%'   => 'inexistingChannel'
        ];
        $context->buildViolation($constraint->inexistingScopeMessage, $violationData)
            ->shouldBeCalled()
            ->willReturn($violation);

        $this->validate($value, $constraint);
    }
}
