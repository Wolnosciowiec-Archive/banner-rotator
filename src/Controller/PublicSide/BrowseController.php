<?php declare(strict_types = 1);

namespace App\Controller\PublicSide;

use App\Controller\AbstractController;
use App\Domain\ActionHandler\BrowseAction;
use App\Domain\Entity\BannerElement;
use App\Domain\ValueObject\Annotation\ObjectsList;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Swagger\Annotations\{Parameter, Response as SWGResponse};
use App\Domain\Entity\{BannerGroup};

/**
 * Responsible for serving the list of banners to the public
 */
class BrowseController extends AbstractController
{
    /**
     * @var BrowseAction
     */
    protected $handler;

    public function __construct(BrowseAction $handler)
    {
        $this->handler = $handler;
    }

    /**
     * API call
     *
     * @Parameter(
     *     name="groupName",
     *     in="path",
     *     type="string",
     *     description="Banner group name"
     * )
     *
     * @Parameter(
     *     name="randomize",
     *     in="query",
     *     type="boolean",
     *     description="Display in random order"
     * )
     *
     * @Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="Limit the number of returned results",
     *     minimum="1",
     *     maximum="100",
     *     default="50"
     * )
     *
     * @SWGResponse(
     *     response=200,
     *     description="A list of banner elements that belongs to a banner group",
     *     examples={
     *         {
     *             "group": @BannerGroup(),
     *             "elements": @ObjectsList(object=@BannerElement())
     *         }
     *     }
     * )
     *
     * @param Request $request
     * @param string $groupName
     *
     * @return JsonResponse
     */
    public function browseJsonAction(Request $request, string $groupName): JsonResponse
    {
        $data = $this->handler->handle(
            $groupName,
            $this->getIntegerInRange($request->get('limit', 50), 1, 100, 50),
            $request->get('randomize') === 'true'
        );

        if ($data === null) {
            return $this->createApiResponse(['error' => 'Banner group not found'], 404);
        }

        return $this->createApiResponse($data, 200);
    }

    /**
     * Renders a basic HTML with the list of banners
     *
     * @Parameter(
     *     name="groupName",
     *     in="path",
     *     type="string",
     *     description="Banner group name",
     *     required=true
     * )
     *
     * @Parameter(
     *     name="randomize",
     *     in="query",
     *     type="boolean",
     *     description="Display in random order"
     * )
     *
     * @Parameter(
     *     name="max_width",
     *     in="query",
     *     type="integer",
     *     description="Maximum width in CSS for images",
     *     default="235",
     *     minimum="10",
     *     maximum="1000"
     * )
     *
     * @Parameter(
     *     name="custom_css_url",
     *     in="query",
     *     type="string",
     *     description="URL address to an optional stylesheet",
     *     default=""
     * )
     *
     * @Parameter(
     *     name="complete_html_document",
     *     in="query",
     *     type="boolean",
     *     description="Render a valid HTML document with <html>, <header> and <body>"
     * )
     *
     * @SWGResponse(
     *     response=200,
     *     description="Basic HTML code with the list of banners"
     * )
     *
     * @param Request $request
     * @param string $groupName Banner group name
     *
     * @return Response
     */
    public function browseRenderedAction(Request $request, string $groupName): Response
    {
        $data = $this->handler->handle(
            $groupName,
            $this->getIntegerInRange($request->get('limit', 50), 1, 100, 50),
            $request->get('randomize') === 'true'
        );

        if ($data === null) {
            return $this->render('Browse/NoSuchGroup.html.twig');
        }

        $data['options'] = [
            'max_width' =>
                $this->getIntegerInRange($request->get('max_width'), 10, 1000, 235),
            'custom_css_url' =>
                filter_var($request->get('custom_css_url'), FILTER_VALIDATE_URL) ? $request->get('custom_css_url') : '',
            'complete_html_document' => 
                $request->get('complete_html_document') ?? false,
        ];

        return $this->render('Browse/BannerGroupElementsView.html.twig', $data, $this->createResponse());
    }

    /**
     * @param $requestValue
     * @param int $starts
     * @param int $ends
     * @param int $default
     *
     * @return int
     */
    protected function getIntegerInRange($requestValue, int $starts, int $ends, int $default)
    {
        $requestValue = (int) $requestValue;
        
        if ($requestValue < $starts || $requestValue > $ends) {
            return $default;
        }
        
        return $requestValue;
    }
}
