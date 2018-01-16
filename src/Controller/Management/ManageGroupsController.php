<?php declare(strict_types = 1);

namespace App\Controller\Management;

use App\Controller\AbstractController;
use App\Entity\BannerGroup;
use App\Exception\EntityNotFoundException;
use App\Exception\ManagerException;
use App\Exception\NotDeletableEntityException;
use App\Form\BannerGroupForm;
use App\Manager\BannerManager;
use App\Manager\GroupManager;
use App\Manager\ManagerInterface;
use Swagger\Annotations\Parameter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations\Response as SWGResponse;
use Swagger\Annotations\Schema as SWGSchema;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides interface to manage banner groups
 */
class ManageGroupsController extends AbstractManagementController
{
    /**
     * @var GroupManager $manager
     */
    protected $manager;

    public function __construct(GroupManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @SWGResponse(
     *     response=200,
     *     description="List of banner groups to manage",
     *     schema=@SWGSchema(
     *         @Model(type=App\Entity\BannerGroup::class)
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function listGroupsAction(): JsonResponse
    {
        return new JsonResponse($this->manager->getRepository()->findAll(), 200);
    }

    /**
     * @param Request $request
     * @param string $groupName
     *
     * @Parameter(
     *     name="POST_body",
     *     description="Object body", in="body", schema=@BannerGroup(), default=@BannerGroup()
     * )
     *
     * @SWGResponse(
     *     response=200,
     *     description="Created group object",
     *     @Model(type=App\Entity\BannerGroup::class)
     * )
     *
     * @return Response
     *
     * @throws ManagerException
     */
    public function createGroupAction(Request $request, string $groupName = ''): Response
    {
        return parent::modifyAction($request, $groupName);
    }

    /**
     * @param Request $request
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
     * @Parameter(
     *     name="POST_body",
     *     description="Group object body", in="body", schema=@BannerGroup(), default=@BannerGroup()
     * )
     *
     * @SWGResponse(
     *     response=200,
     *     description="Modified group object",
     *     @Model(type=App\Entity\BannerGroup::class)
     * )
     *
     * @return Response
     *
     * @throws ManagerException
     */
    public function editGroupAction(Request $request, string $groupName = ''): Response
    {
        return parent::modifyAction($request, $groupName);
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
     * @param Request $request
     * @param string $groupName
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, string $groupName): JsonResponse
    {
        return parent::deleteAction($request, $groupName);
    }

    /**
     * @inheritdoc
     */
    protected function getManager(): ManagerInterface
    {
        return $this->manager;
    }

    /**
     * @inheritdoc
     */
    protected function getFormName(): string
    {
        return BannerGroupForm::class;
    }
}
