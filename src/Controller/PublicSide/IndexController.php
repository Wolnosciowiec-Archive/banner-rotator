<?php declare(strict_types=1);

namespace App\Controller\PublicSide;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 */
class IndexController extends Controller
{
    public function indexAction(Request $request): Response
    {
        if ($request->query->has('vv')) {
            return new Response('Do you want to know something?');
        }

        if ($request->query->has('vvv')) {
            return new Response(file_get_contents(__DIR__ . '/../../../ee-img.jpg'), 200, [
                'Content-Type' => 'image/jpeg'
            ]);
        }

        return new Response('Banner Rotator application - a social project, ' .
                            'check out <a href="https://github.com/Wolnosciowiec/banner-rotator">project page</a> ' .
                            'for more information. Documentation is available at <a href="/public/doc">/public/doc</a>. ' .
                            'Happy using, Vivir la anarquía.');
    }
}
