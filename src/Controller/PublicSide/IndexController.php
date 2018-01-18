<?php declare(strict_types = 1);

namespace App\Controller\PublicSide;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return new Response('Banner Rotator application, ' .
                            'check out <a href="https://github.com/Wolnosciowiec/banner-rotator">project page</a> ' .
                            'for more information. Documentation is available at <a href="/public/doc">/public/doc</a>. ' .
                            'Happy using, Vivir la anarquía.');
    }
}
