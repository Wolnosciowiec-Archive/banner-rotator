<?php declare(strict_types = 1);

namespace App\Controller\Management;

use App\ActionHandler\BannerElement\ListBannersAction;
use App\Entity\BannerElement;
use App\Exception\EntityNotFoundException;
use App\Form\BannerElementForm;
use App\Manager\BannerManager;
use App\Manager\ManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Response as SWGResponse;
use Swagger\Annotations\Schema as SWGSchema;
use Nelmio\ApiDocBundle\Annotation\Model;

class ManageBannersController extends AbstractManagementController
{
    /**
     * @var ListBannersAction $listBannersActionHandler
     */
    private $listBannersActionHandler;

    /**
     * @var BannerManager $manager
     */
    private $manager;

    public function __construct(
        ListBannersAction $listBannersAction,
        BannerManager $manager
    ) {
        $this->listBannersActionHandler  = $listBannersAction;
        $this->manager                   = $manager;
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
     * @SWGResponse(
     *     response=200,
     *     description="List of all elements that are inside of a specified group",
     *     schema=@SWGSchema(
     *         @Model(type=App\Entity\BannerElement::class)
     *     )
     * )
     *
     * @return Response
     */
    public function listBannersAction(Request $request, string $groupName): Response
    {
        try {
            return $this->createApiResponse($this->listBannersActionHandler->getListOfBanners($groupName));

        } catch (EntityNotFoundException $exception) {
            return $this->getEntityNotFoundResponse();
        }
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
     * @param Request $request
     * @param string  $bannerId
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, string $bannerId): JsonResponse
    {
        return parent::deleteAction($request, $bannerId);
    }

    /**
     * @param Request $request
     * @param string $groupName
     *
     * @Parameter(
     *     name="groupName",
     *     in="path",
     *     type="string",
     *     description="Element identifier",
     *     required=false
     * )
     *
     * @Parameter(
     *     name="POST_body",
     *     description="A body of a new banner", in="body", schema=@BannerElement(), default=@BannerElement()
     * )
     *
     * @SWGResponse(
     *     response=200,
     *     description="Created banner object",
     *     @Model(type=App\Entity\BannerElement::class)
     * )
     *
     * @return Response
     * @throws \App\Exception\ManagerException
     * @throws EntityNotFoundException
     */
    public function createBannerAction(Request $request, string $groupName): Response
    {
        $object = $this->getManager()->createForGroup($groupName);
        $form = $this->createPreparedForm($request, $object, $this->getFormName());

        $onValidFormAction = function () use ($object) {
            $this->getManager()->save($object);
        };

        return $this->handleObjectSaveForm($form, $onValidFormAction, $object, true);
    }

    /**
     * Edit an existing banner action
     *
     * @Parameter(
     *     name="bannerId",
     *     in="path",
     *     type="string",
     *     description="Element identifier",
     *     required=false
     * )
     *
     * @Parameter(
     *     name="POST_body",
     *     description="A body of a new banner", in="body", schema=@BannerElement(), default=@BannerElement()
     * )
     *
     * @SWGResponse(
     *     response=200,
     *     description="Created banner object",
     *     @Model(type=App\Entity\BannerElement::class)
     * )
     *
     * @param Request $request
     * @param string $bannerId
     *
     * @return Response
     * @throws \App\Exception\ManagerException
     */
    public function editBannerAction(Request $request, string $bannerId): Response
    {
        $object = $this->getManager()->findOrCreateNew($bannerId, false);
        $form = $this->createPreparedForm($request, $object, $this->getFormName());

        $onValidFormAction = function () use ($object) {
            $this->getManager()->save($object);
        };

        return $this->handleObjectSaveForm($form, $onValidFormAction, $object, true);
    }

    /**
     * @return ManagerInterface|BannerManager
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
        return BannerElementForm::class;
    }
}
