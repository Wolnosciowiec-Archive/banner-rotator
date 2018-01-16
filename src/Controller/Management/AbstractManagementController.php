<?php declare(strict_types = 1);

namespace App\Controller\Management;

use App\Controller\AbstractController;
use App\Exception\EntityNotFoundException;
use App\Exception\ManagerException;
use App\Exception\NotDeletableEntityException;
use App\Manager\ManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Common code for managing entities
 */
abstract class AbstractManagementController extends AbstractController
{
    /**
     * @return ManagerInterface
     */
    abstract protected function getManager(): ManagerInterface;

    /**
     * @return string
     */
    abstract protected function getFormName(): string;

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, string $id): JsonResponse
    {
        try {
            $this->getManager()->deleteById($id);

        } catch (NotDeletableEntityException $exception) {
            return new JsonResponse(['message' => 'This object cannot be deleted, existing elements are under it', 'code' => $exception->getCode()], 422);

        } catch (EntityNotFoundException $exception) {
            return $this->getEntityNotFoundResponse();
        }

        return new JsonResponse(['message' => 'OK']);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     *
     * @throws ManagerException
     */
    protected function modifyAction(Request $request, string $id = ''): Response
    {
        $object = $this->getManager()->findOrCreateNew($id, $this->hasRequestedObjectCreation($request));

        $form = $this->createPreparedForm($request, $object, $this->getFormName(), [
            'is_new' => $this->hasRequestedObjectCreation($request)
        ]);

        $onValidFormAction = function () use ($object) {
            $this->getManager()->save($object);
        };

        return $this->handleObjectSaveForm($form, $onValidFormAction, $object, $this->hasRequestedObjectCreation($request));
    }
}
