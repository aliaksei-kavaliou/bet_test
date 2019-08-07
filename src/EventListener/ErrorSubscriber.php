<?php declare(strict_types = 1);

namespace App\EventListener;

use App\Exception\Errors;
use App\Exception\HandledExceptionInterface;
use App\Model\BadResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\ValidationFailedException;

class ErrorSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onError', 0],
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onError(ExceptionEvent $event): void
    {
        $exception = $event->getException();
        $errors = ['global' => [], 'selection' => []];

        switch (true) {
            case $exception instanceof ValidationFailedException:
                foreach ($exception->getViolations() as $violation) {
                    $message = $violation->getMessage();
                    $code = is_numeric($message) ? $message : Errors::UNKNOWN_ERROR;
                    $errors[$violation->getConstraint()->payload['level']][] = [
                        'code' => $code,
                        'message' => str_replace(
                            \array_keys($violation->getParameters()),
                            \array_values($violation->getParameters()),
                            (Errors::$errorMessages[$message] ?? $message)
                        ),
                    ];
                }
                break;

            case $exception instanceof HandledExceptionInterface:
                $errors['global'][] = ['code' => $exception->getCode(), 'messge' => $exception->getMessage()];
                break;

            default:
                return;
        }

        $content = \json_decode($event->getRequest()->getContent(), true);
        $badResponse = [
            'player_id' => $content['player_id'] ?? null,
            'stake_amount' => $content['stake_amount'] ?? null,
            'errors' => $errors['global'],
            'selections' => $errors['selection'],
        ];

        $response = new JsonResponse($badResponse, Response::HTTP_BAD_REQUEST);

        $event->setResponse($response);
    }
}
