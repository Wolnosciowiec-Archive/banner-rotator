<?php declare(strict_types = 1);

namespace App\Controller;

use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Exception\ManagerException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Validator\ConstraintViolation;

abstract class AbstractController extends Controller
{
    /**
     * Decode the submitted entity and pass-through to the
     * form for validation
     *
     * @param Request $request
     * @param mixed   $object
     * @param string  $formClassName
     *
     * @return FormInterface
     */
    protected function createPreparedForm(Request $request, $object, string $formClassName): FormInterface
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm($formClassName, $object);
        $form->submit($data);

        return $form;
    }

    /**
     * @param FormInterface $form
     * @param callable      $action
     * @param bool          $isCreationMode
     *
     * @return Response
     * @throws ManagerException
     */
    protected function handleObjectSaveForm(FormInterface $form, callable $action, bool $isCreationMode): Response
    {
        if (!$form->isValid()) {
            return $this->createApiResponse(
                [
                    'message' => 'Validation error',
                    'error' => $this->getFormErrors($form)
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        
        try {
            return new JsonResponse($action(), $isCreationMode ? Response::HTTP_CREATED : Response::HTTP_ACCEPTED);

        } catch (EntityNotFoundException $exception) {
            return $this->getEntityNotFoundResponse();

        } catch (ManagerException $exception) {

            if ($exception->getCode() === ManagerException::ENTITY_ALREADY_EXISTS) {
                return $this->createApiResponse(
                    [
                        'message' => 'Object already exists',
                        'code' => $exception->getCode()
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }

            throw $exception;
        }
    }

    /**
     * @return Response
     */
    protected function getEntityNotFoundResponse(): JsonResponse
    {
        return $this->createApiResponse(['message' => 'Object not found'], 404);
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
        $errors = [];

        // global
        foreach ($form->getErrors() as $error) {
            if ($error->getCause() instanceof ConstraintViolation) {
                $errors[(string) $error->getCause()->getPropertyPath()][] = $error->getMessage();
                continue;
            }

            $errors[$form->getName()][] = $error->getMessage();
        }

        // fields
        foreach ($form as $child /** @var Form $child */) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[(string) $child->getPropertyPath()][] = $error->getMessage();
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
    protected function createApiResponse($responseBody, int $code = 200, array $headers = [])
    {
        $headers = array_merge($headers, [
            'Access-Control-Allow-Origin'      => $_SERVER['HTTP_ORIGIN'] ?? '*',
            'Access-Control-Allow-Credentials' => false,
            'Access-Control-Max-Age'           => 3600,
        ]);

        return new JsonResponse(
            $this->getSerializer()->serialize($responseBody, 'json', $this->getSerializationContext()),
            $code,
            $headers,
            true
        );
    }

     /**
      * @param string $responseBody
      * @param int $code
      * @param array $headers
      *
      * @return Response
      */
    protected function createResponse(string $responseBody = '', int $code = 200, array $headers = [])
    {
         $headers = array_merge($headers, [
              'X-Frame-Options' => 'AllowAll',
         ]);

         return new Response($responseBody, $code, $headers);
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
        return (new SerializationContext())->setGroups(
            $this->isManagementContext() ? ['collective'] : ['public']
        );
    }

    protected function isManagementContext(): bool
    {
        return strpos(\get_class($this), 'App\\Controller\\Management\\') !== false;
    }
}
