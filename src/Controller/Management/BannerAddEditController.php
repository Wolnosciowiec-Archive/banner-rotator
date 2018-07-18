<?php declare(strict_types=1);

namespace App\Controller\Management;

use App\Controller\AbstractController;
use App\Domain\ActionHandler\BannerAddEditAction;
use App\Domain\Form\BannerForm;
use App\Domain\Entity\BannerElement;
use App\Domain\Exception\EntityNotFoundException;
use App\Infrastructure\Form\BannerFormType;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Response as SWGResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;

class BannerAddEditController extends AbstractController
{
    /**
     * @var BannerAddEditAction
     */
    private $handler;

    public function __construct(BannerAddEditAction $handler)
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
     *     @Model(type=App\Domain\Entity\BannerElement::class)
     * )
     *
     * @return Response
     * @throws \App\Domain\Exception\ManagerException
     * @throws EntityNotFoundException
     */
    public function createBannerAction(Request $request, string $groupName): Response
    {
        $form = new BannerForm();
        $infrastructureForm = $this->createPreparedForm($request, $form, BannerFormType::class);

        return $this->handleObjectSaveForm(
            $infrastructureForm,
            function () use ($form, $groupName) {
                return $this->handler->handleNew($form, $groupName);
            },
            true
        );
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
     *     description="A body of a new banner", in="body", schema=@BannerForm(), default=@BannerForm()
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
     * @throws \App\Domain\Exception\ManagerException
     */
    public function editBannerAction(Request $request, string $bannerId): Response
    {
        $form = new BannerForm();
        $infrastructureForm = $this->createPreparedForm($request, $form, BannerFormType::class);

        return $this->handleObjectSaveForm(
            $infrastructureForm,
            function () use ($form, $bannerId) {
                return $this->handler->handleEdit($form, $bannerId);
            },
            true
        );
    }
}
