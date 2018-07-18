<?php declare(strict_types=1);

namespace App\Controller\Management;

use App\Controller\AbstractController;
use App\Domain\ActionHandler\GroupDeleteAction;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Exception\NotDeletableEntityException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations\Response as SWGResponse;
use Swagger\Annotations\Parameter;

class GroupDeleteController extends AbstractController
{
    /**
     * @var GroupDeleteAction
     */
    private $handler;

    public function __construct(GroupDeleteAction $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @Parameter(
     *     name="groupName",
     *     in="path",
     *     type="string",
     *     description="Banner group name"
     * )
     *
     * @SWGResponse(
     *     response=200,
     *     description="",
     *     examples={
     *         {
     *             "message": "OK"
     *         }
     *     }
     * )
     *
     * @param string $groupId
     *
     * @return Response
     */
    public function handleDeleteAction(string $groupName): Response
    {
        try {
            return $this->createApiResponse($this->handler->handle($groupName), Response::HTTP_OK);

        } catch (NotDeletableEntityException $exception) {
            return new JsonResponse(
                [
                    'message' => 'This object cannot be deleted, existing elements are under it',
                    'code' => $exception->getCode()
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

        } catch (EntityNotFoundException $exception) {
            return $this->getEntityNotFoundResponse();
        }
    }
}
