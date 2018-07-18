<?php declare(strict_types=1);

namespace App\Controller\Management;

use App\Controller\AbstractController;
use App\Domain\ActionHandler\BannerDeleteAction;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Exception\NotDeletableEntityException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Response as SWGResponse;

class BannerDeleteController extends AbstractController
{
    /**
     * @var BannerDeleteAction
     */
    private $handler;

    public function __construct(BannerDeleteAction $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @Parameter(
     *     name="bannerId",
     *     in="path",
     *     type="string",
     *     description="Element id"
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
     * @param string $bannerId
     *
     * @return JsonResponse
     */
    public function handleDeleteAction(string $bannerId): Response
    {
        try {
            return $this->createApiResponse($this->handler->handle($bannerId),  Response::HTTP_OK);

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
