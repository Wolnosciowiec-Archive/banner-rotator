<?php declare(strict_types = 1);

namespace App\Controller\PublicSide;

use App\Manager\BannerManager;
use App\Manager\GroupManager;
use App\ValueObject\Annotation\ObjectsList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Swagger\Annotations\{Parameter, Response as SWGResponse};
use App\Entity\{BannerGroup, BannerElement};

/**
 * Responsible for serving the list of banners to the public
 */
class BrowseController extends Controller
{
    /**
     * @var BannerManager $bannerManager
     */
    protected $bannerManager;

    /**
     * @var GroupManager $groupManager
     */
    protected $groupManager;

    public function __construct(BannerManager $bannerManager, GroupManager $groupManager)
    {
        $this->bannerManager = $bannerManager;
        $this->groupManager  = $groupManager;
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
        $data = $this->findData($groupName, $request);

        if ($data === null) {
            return new JsonResponse(['error' => 'Banner group not found'], 404);
        }

        return new JsonResponse($data, 200);
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
        $data = $this->findData($groupName, $request);

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

        return $this->render('Browse/BannerGroupElementsView.html.twig', $data);
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

    /**
     * @param string $groupName
     * @param Request $request
     *
     * @return array|null
     */
    protected function findData(string $groupName, Request $request): ?array
    {
        $bannerGroup = $this->groupManager->getRepository()->find($groupName);

        if (!$bannerGroup) {
            return null;
        }

        $elements = $this->bannerManager->getRepository()->findPublishedBanners(
            $bannerGroup,
            $this->getIntegerInRange($request->get('limit', 50), 1, 100, 50)
        );

        if ($request->get('randomize') === 'true') {
            shuffle($elements);
        }

        return [
            'group'    => $bannerGroup,
            'elements' => $elements,
        ];
    }
}
