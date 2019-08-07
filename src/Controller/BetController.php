<?php declare(strict_types = 1);

namespace App\Controller;

use App\Message\FinalizeBetMessage;
use App\Message\MakeBetMessage;
use App\Middleware\Stamp\CanBetStamp;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class BetController
{
    /**
     * @param Request             $request
     * @param MessageBusInterface $messageBus
     *
     * @return JsonResponse
     */
    public function make(Request $request, MessageBusInterface $messageBus): JsonResponse
    {
        $data = \json_decode($request->getContent(), true);
        $playerId = $data['player_id'] ?? null;
        $stakeAmount = (float)$data['stake_amount'] ?? null;
        $message = new MakeBetMessage(
            $playerId,
            $stakeAmount,
            $data['selections'] ?? null
        );

        $bet = $messageBus->dispatch($message, [new CanBetStamp($playerId, $stakeAmount)])
            ->last(HandledStamp::class)->getResult();
        $messageBus->dispatch(new FinalizeBetMessage($bet));

        return new JsonResponse([], Response::HTTP_CREATED);
    }
}
