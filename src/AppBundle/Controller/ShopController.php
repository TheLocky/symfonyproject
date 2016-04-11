<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ShopController extends Controller
{
	/**
	 * @Route("/shop", name="steamgames")
	 */
	public function indexAction()
	{
		return $this->render('shop/index.html.twig');
	}

	/**
	 * @Route("/shop/data", name="steamgamesdata")
	 */
	public function dataAction()
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:SteamGame');
		$jsonEncode = new JsonEncode(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		$serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder($jsonEncode)));
		$games = $repository->findAll();
		$json = $serializer->serialize($games, 'json');

		return new Response($json);
	}
}
