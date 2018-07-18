<?php declare(strict_types=1);

namespace App\Controller\Management;

use App\Controller\AbstractController;
use App\Domain\ActionHandler\BannerListAction;
use App\Domain\Exception\EntityNotFoundException;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Response as SWGResponse;
use Swagger\Annotations\Schema as SWGSchema;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;

class BannerListController extends AbstractController
{
    /**
     * @var BannerListAction
     */
    private $handler;

    public function __construct(BannerListAction $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param string $groupName
     *
     * @Parameter(
     *     name="groupName",
     *     in="path",
     *     type="string",
     *     description="Banner group name",
     *     required=false
     * )
     *
     * @SWGResponse(
     *     response=200,
     *     description="List of all elements that are inside of a specified group",
     *     schema=@SWGSchema(
     *         @Model(type=App\Domain\Entity\BannerElement::class)
     *     )
     * )
     *
     * @throws EntityNotFoundException
     *
     * @return Response
     */
    public function listBannersAction(string $groupName): Response
    {
        try {
            return $this->createApiResponse($this->handler->handle($groupName));

        } catch (EntityNotFoundException $exception) {
            return $this->getEntityNotFoundResponse();
        }
    }
}