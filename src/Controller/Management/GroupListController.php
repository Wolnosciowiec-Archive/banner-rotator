<?php declare(strict_types=1);

namespace App\Controller\Management;

use App\Controller\AbstractController;
use App\Domain\ActionHandler\GroupListAction;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations\Response as SWGResponse;
use Swagger\Annotations\Schema as SWGSchema;
use Nelmio\ApiDocBundle\Annotation\Model;

class GroupListController extends AbstractController
{
    /**
     * @var GroupListAction
     */
    private $handler;

    public function __construct(GroupListAction $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @SWGResponse(
     *     response=200,
     *     description="List of banner groups to manage",
     *     schema=@SWGSchema(
     *         @Model(type=App\Domain\Entity\BannerGroup::class)
     *     )
     * )
     *
     * @return Response
     */
    public function handleGroupsListingAction(): Response
    {
        return $this->createApiResponse($this->handler->handle());
    }
}
