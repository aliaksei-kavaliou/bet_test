<?php declare(strict_types = 1);

namespace App\Tests\unit\EventListener;

use App\EventListener\ErrorSubscriber;
use App\Exception\CanBetException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ErrorSubscriberTest extends TestCase
{
    /** @dataProvider dataProvider */
    public function testOnError($exception, $expectedResponseType, $expectedContent): void
    {
        $subscriber = new ErrorSubscriber();
        $event = new ExceptionEvent(
            $this->prophesize(HttpKernelInterface::class)->reveal(),
            new Request(),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );
        $subscriber->onError($event);

        if (null === $expectedResponseType) {
            $this->assertNull($event->getResponse());
        } else {
            $this->assertInstanceOf($expectedResponseType, $event->getResponse());
            $this->assertContains($expectedContent, $event->getResponse()->getContent());
        }
    }

    public function dataProvider(): array
    {
        return [
            [
                'exception' => new \Exception(),
                'expectedResponseType' => null,
                'expectedContent' => null,
            ],
            [
                'exception' => $this->getValidatorException(),
                'expectedResponse' => JsonResponse::class,
                'expectedContent' => 'Minimum stake amount is 1',
            ],
            [
                'exception' => new CanBetException('Your previous action is not finished yet', 10),
                'expectedResponse' => JsonResponse::class,
                'expectedContent' => 'Your previous action is not finished yet',
            ],
        ];
    }

    private function getValidatorException(): ValidationFailedException
    {
        $violation = $this->prophesize(ConstraintViolation::class);
        $constraint = new Range(['min' => 1]);
        $constraint->payload = ['level' => 'global'];
        $violation->getConstraint()->willReturn($constraint);
        $violation->getMessage()->willReturn('Minimum stake amount is {{ limit }}');
        $violation->getParameters()->willReturn(['{{ limit }}' => 1]);

        $exception = $this->prophesize(ValidationFailedException::class);
        $exception->getViolations()->willReturn(new ConstraintViolationList([$violation->reveal()]));

        return $exception->reveal();
    }
}
