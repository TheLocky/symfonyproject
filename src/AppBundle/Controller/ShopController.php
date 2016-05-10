<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Entity\GameCategory;

class ShopController extends Controller
{
	
	/**
	 * @Route("/shop", name="steamgames")
	 */
	public function indexAction(Request $request)
	{
		$category = $request->query->get('category', '.');
		
		$games = $this->getDoctrine()->getRepository('AppBundle:SteamGame')
			->createQueryBuilder('g')
			->join('g.categories', 'c')		
			->where('c.path LIKE :category')
			->setParameter('category', $category . '%')
			->getQuery()
			->getResult();

		$path = $this->getDoctrine()->getRepository('AppBundle:GameCategory')
			->createQueryBuilder('c')
			->where(':category LIKE CONCAT(c.path, \'%\')')
			->setParameter('category', $category)
			->getQuery()
			->getResult();

		$allcat = new GameCategory();
		$allcat->setName('Все');
		$allcat->setPath('.');
		$path = array_merge(array($allcat), $path);

		return $this->render('shop/index.html.twig', 
			array('games' => $games,
				  'path' => $path));		
	}
}