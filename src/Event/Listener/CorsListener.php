<?php declare(strict_types = 1);

namespace App\Event\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\{GetResponseEvent, FilterResponseEvent};


class CorsListener implements EventSubscriberInterface
{
    private $headers;

    public function __construct(
        string $allowOrigin,
        bool $allowCredentials,
        int $maxAge,
        string $allowMethods,
        string $allowHeaders
    )
    {
        $this->headers = [
            'Access-Control-Allow-Origin'      => $allowOrigin,
            'Access-Control-Allow-Credentials' => $allowCredentials,
            'Access-Control-Max-Age'           => $maxAge ?: 3600,
            'Access-Control-Allow-Methods'     => $allowMethods ?: 'GET, POST, OPTIONS, DELETE, PUT',
            'Access-Control-Allow-Headers'     => $allowHeaders,
        ];
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
            'kernel.response' => 'onKernelResponse'
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (in_array($request->getMethod(), ['OPTIONS', 'HEAD'], true)) {
            $event->setResponse(
                $this->withHeaders(new Response(''))
            );
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $event->setResponse(
            $this->withHeaders($event->getResponse())
        );
    }

    /**
     * Adds headers to the response
     *
     * @param Response $response
     * @return Response
     */
    private function withHeaders(Response $response)
    {
        $response->headers->set('Access-Control-Allow-Origin',      $this->headers['Access-Control-Allow-Origin'] ?: ($_SERVER['HTTP_ORIGIN'] ?? '*'));
        $response->headers->set('Access-Control-Allow-Credentials', $this->headers['Access-Control-Allow-Credentials']);
        $response->headers->set('Access-Control-Max-Age',           $this->headers['Access-Control-Max-Age']);
        $response->headers->set('Access-Control-Allow-Methods',     $this->headers['Access-Control-Allow-Methods']);
        $response->headers->set('Access-Control-Allow-Headers',     $this->headers['Access-Control-Allow-Headers'] ?: ($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? '*'));

        return $response;
    }
}
