<?php declare(strict_types=1);

namespace App\Controller\Management;

use App\Controller\AbstractController;
use App\Domain\ActionHandler\GroupAddEditAction;
use App\Domain\Entity\BannerGroup;
use App\Domain\Exception\ManagerException;
use App\Domain\Form\GroupForm;
use App\Infrastructure\Form\GroupFormType;
use App\Infrastructure\Form\NewBannerFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations\Response as SWGResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations\Parameter;

class GroupAddEditController extends AbstractController
{
    /**
     * @var GroupAddEditAction
     */
    private $handler;

    public function __construct(GroupAddEditAction $handler)
    {
        $this->handler = $handler;
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
     *     @Model(type=App\Domain\Entity\BannerGroup::class)
     * )
     *
     * @return Response
     * @throws ManagerException
     */
    public function handleGroupEditAction(Request $request, string $groupName): Response
    {
        $form = new GroupForm();
        $infrastructureForm = $this->createPreparedForm($request, $form, NewBannerFormType::class);

        return $this->handleObjectSaveForm(
            $infrastructureForm,
            function () use ($form, $groupName) {
                return $this->handler->handleGroupEdit($form, $groupName);
            },
            true
        );
    }

    /**
     * @param Request $request
     *
     * @Parameter(
     *     name="POST_body",
     *     description="Object body", in="body", schema=@BannerGroup(), default=@BannerGroup()
     * )
     *
     * @SWGResponse(
     *     response=201,
     *     description="Created group object",
     *     @Model(type=App\Domain\Entity\BannerGroup::class)
     * )
     *
     * @return Response
     * @throws ManagerException
     */
    public function handleGroupCreationAction(Request $request): Response
    {
        $json = json_decode($request->getContent(), true);

        if (!is_array($json)) {
            $json = [];
        }

        $groupName = (isset($json['id']) ? (string) $json['id'] : '');
        $form = new GroupForm();
        $infrastructureForm = $this->createPreparedForm($request, $form, GroupFormType::class);

        return $this->handleObjectSaveForm(
            $infrastructureForm,
            function () use ($form, $groupName) {
                return $this->handler->handleGroupCreation($form, $groupName);
            },
            true
        );
    }
}
