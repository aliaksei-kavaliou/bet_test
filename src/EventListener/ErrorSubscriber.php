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
                $this->processValidationError($exception, $errors);
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

    /**
     * @param       $exception
     * @param array $errors
     */
    private function processValidationError($exception, array &$errors): void
    {
        foreach ($exception->getViolations() as $violation) {
            $message = $violation->getMessage();
            $code = is_numeric($message) ? $message : Errors::UNKNOWN_ERROR;
            $level = $violation->getConstraint()->payload['level'];
            $error = [
                'code' => $code,
                'message' => str_replace(
                    \array_keys($violation->getParameters()),
                    \array_values($violation->getParameters()),
                    (Errors::$errorMessages[$message] ?? $message)
                ),
            ];

            if ('selection' === $level) {
                $selections = $violation->getRoot()->getSelections();
                $matches = [];
                \preg_match("/\[(\d+)\]/", $violation->getPropertyPath(), $matches);
                $key = $matches[1] ?? null;

                if (null !== $key && \array_key_exists($key, $selections)) {
                    $this->composeSelectionErrors($errors, $key, $selections[$key], $error);
                    continue;
                }

                foreach ($selections as $key => $selection) {
                    $this->composeSelectionErrors($errors, $key, $selection, $error);
                }

                continue;
            }

            $errors[$level][] = $error;
        }
    }

    /**
     * @param array $errors
     * @param       $key
     * @param       $selection
     * @param array $error
     */
    private function composeSelectionErrors(array &$errors, $key, $selection, array $error): void
    {
        $errors['selection'][$key] = $errors['selection'][$key] ?? [
                'id' => $selection->getId(),
                'odds' => $selection->getOdds()
            ];
        $errors['selection'][$key]['errors'][] = $error;
    }
}
