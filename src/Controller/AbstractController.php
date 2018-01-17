<?php declare(strict_types = 1);

namespace App\Controller;

use App\Exception\ManagerException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};

abstract class AbstractController extends Controller
{
    /**
     * Decode the submitted entity and pass-through to the
     * form for validation
     *
     * @param Request $request
     * @param mixed   $object
     * @param string  $formClassName
     * @param array   $options
     *
     * @return FormInterface
     */
    protected function createPreparedForm(Request $request, $object, string $formClassName, array $options = []): FormInterface
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm($formClassName, $object, [
            'trait_choices' => $options,
        ]);
        $form->submit($data);

        return $form;
    }

    /**
     * @param FormInterface $form
     * @param callable      $action
     * @param mixed         $validResponseData
     * @param bool          $isCreationMode
     *
     * @return JsonResponse
     * @throws ManagerException
     */
    protected function handleObjectSaveForm(FormInterface $form, callable $action, $validResponseData, bool $isCreationMode)
    {
        if (!$form->isValid()) {
            return $this->createResponse(['message' => 'Validation error', 'error' => $this->getFormErrors($form)], 400);
        }
        
        try {
            $action();

        } catch (ManagerException $exception) {

            if ($exception->getCode() === ManagerException::ENTITY_ALREADY_EXISTS) {
                return $this->createResponse(['message' => 'Object already exists', 'code' => $exception->getCode()], 400);
            }

            throw $exception;
        }

        return new JsonResponse($validResponseData, $isCreationMode ? 201 : 202);
    }

    /**
     * @return Response
     */
    protected function getEntityNotFoundResponse(): JsonResponse
    {
        return $this->createResponse(['message' => 'Object not found'], 404);
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function hasRequestedObjectCreation(Request $request): bool
    {
        return $request->getMethod() === 'POST';
    }

    /**
     * List all errors of a given bound form.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    protected function getFormErrors(FormInterface $form)
    {
        $errors = array();

        // Global
        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        // Fields
        foreach ($form as $child /** @var Form $child */) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }

    /**
     * @param mixed|\JsonSerializable|array|string $responseBody
     * @param int $code
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function createResponse($responseBody, int $code = 200, array $headers = [])
    {
        return new JsonResponse(
            $this->getSerializer()->serialize($responseBody, 'json', $this->getSerializationContext()),
            $code,
            $headers,
            true
        );
    }

    /**
     * @return Serializer
     */
    protected function getSerializer(): Serializer
    {
        return $this->get('jms_serializer');
    }

    /**
     * @return SerializationContext
     */
    protected function getSerializationContext(): SerializationContext
    {
        return (new SerializationContext())->setGroups(['public']);
    }
}
