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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use APY\DataGridBundle\Grid\Source\Entity;
use AppBundle\Entity\GameCategory;
use AppBundle\Entity\User;
use AppBundle\Entity\Orders;

class ShopController extends Controller
{

	private function getBaseParameters(Request $request)
	{
		$base = array(
			'cart' => (object)array('items' => array()),
			'cartInfo' => (object)array(
				'count' => 0,
				'cost' => 0.00
				)
			);

		if (!isset($_COOKIE['cart']))
		{
			setcookie('cart', json_encode($base['cart']), time() + (86400 * 60));
		}
		else
		{
			$base['cart'] = json_decode($_COOKIE['cart']);
			foreach ($base['cart']->items as $item) {
				if (!isset($item->count))
					$item->count = 1;
				if (!isset($item->cost))
					$item->cost = $this->getDoctrine()->getRepository('AppBundle:SteamGame')
						->findOneById($item->id)->getPrice();
			 	$base['cartInfo']->count += $item->count;
			 	$base['cartInfo']->cost += $item->count * $item->cost;
			} 
		}

		return $base;
	}
	
	/**
	 * @Route("/shop", name="steamgames")
	 */
	public function indexAction(Request $request)
	{
		$category = $request->query->get('category', '.');
		$page = $request->query->get('page', 1);
		
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

		$maxItemsOnPage = 9;
		$visiblePages = 5;
		$totalPages = intval(count($games)/$maxItemsOnPage) + 1;
		$offset = ($page-1)*$maxItemsOnPage;
		$games = array_slice($games, $offset, min(count($games) - $offset, $maxItemsOnPage));
		$pageInfo = (object) array('total'=>$totalPages, 'visible'=>$visiblePages, 'current'=>$page);

		$allcat = new GameCategory();
		$allcat->setName('Все');
		$allcat->setPath('.');
		$path = array_merge(array($allcat), $path);

		return $this->render('shop/gamespage.html.twig', 
			array_merge($this->getBaseParameters($request),
				array('games' => $games,
				  	'path' => $path,
				  	'page' => $pageInfo)));		
	}

	/**
	 * @Route("/shop/login", name="shopLogin")
	 */
	public function loginAction(Request $request)
	{
		$authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    $loginFormData = array('username' => $lastUsername);
	    $form = $this->createFormBuilder($loginFormData)
            ->add('username', TextType::class, 
            	array('label' => 'Имя пользователя'))
            ->add('password', PasswordType::class, 
            	array('label' => 'Пароль'))
            ->add('submit', SubmitType::class, 
            	array('label' => 'Войти'))
            ->getForm();

	    return $this->render(
	        'shop/login.html.twig',
	        array_merge($this->getBaseParameters($request),
		        array('loginForm' => $form->createView(),
		            'error' => $error)));
	}

	private function authenticateUser(User $user)
	{
	    $providerKey = 'main';
	    $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

	    $this->container->get('security.token_storage')->setToken($token);
	}

	/**
     * @Route("/shop/register", name="shopRegistration")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, 
            	array('label' => 'Имя пользователя'))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'Пароли не совпадают!',
                'first_options'  => array('label' => 'Пароль'),
                'second_options' => array('label' => 'Повторите пароль')))
            ->add('submit', SubmitType::class, 
            	array('label' => 'Зарегистрироваться'))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        	$password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->authenticateUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($request->query->get('_back','/shop'));
        }

        return $this->render(
            'shop/register.html.twig',
            array_merge($this->getBaseParameters($request), 
            	array('regForm' => $form->createView())));
    }

    /**
     * @Route("/shop/cart", name="shopCart")
     */
    public function cartAction(Request $request)
    {
    	$baseParameters = $this->getBaseParameters($request);
    	$cartItems = array();

    	$itemsRepo = $this->getDoctrine()->getRepository('AppBundle:SteamGame');
    	foreach ($baseParameters['cart']->items as $item) 
    	{
    		$itemInfo = $itemsRepo->findOneById($item->id);
    		$cartItems[] = (object)array(
    			'id' => $item->id,
    			'name' => $itemInfo->getName(),
    			'cost' => $item->cost,
    			'count' => $item->count,
    			);
    	}

    	return $this->render(
    		'shop/cart.html.twig', 
    		array_merge($baseParameters,
    			array('cartItems' => $cartItems)));
    }

    /**
     * @Route("/shop/cart/success", name="buySucsess")
     */
    public function cartSuccessAction(Request $request)
    {
    	return $this->render(
    		'shop/info.html.twig', 
    		array_merge($this->getBaseParameters($request),
    			array('message' => 'Поздравляем! Ваш заказ успешно оформлен.')));
    }

    /**
     * @Route("/shop/admin", name="shopAdmin")
     */
    public function adminAction(Request $request)
    {
    	$userSource = new Entity('AppBundle:User');
        $usersTable = $this->get('grid');
        $usersTable->setSource($userSource);
        $usersTable->hideColumns('id');
        $usersTable->getColumn('username')->setTitle('Имя пользователя');
        $usersTable->getColumn('roles')->setTitle('Роли в системе');
        $usersTable->isReadyForRedirect();

		$gameSource = new Entity('AppBundle:SteamGame');
        $gamesTable = $this->get('grid');
        $gamesTable->setSource($gameSource);
        $gamesTable->hideColumns('id');
        $gamesTable->getColumn('name')->setTitle('Название');
        $gamesTable->getColumn('image')->setTitle('Ссылка на картинку');
        $gamesTable->getColumn('developer')->setTitle('Разработчик');
        $gamesTable->isReadyForRedirect();

        $categorySource = new Entity('AppBundle:GameCategory');
        $categoryTable = $this->get('grid');
        $categoryTable->setSource($categorySource);
        $categoryTable->hideColumns('id');
        $categoryTable->getColumn('name')->setTitle('Название');
        $categoryTable->getColumn('path')->setTitle('Путь в иерархии');
        $categoryTable->isReadyForRedirect();

        $ordersSource = new Entity('AppBundle:Orders');
        $ordersTable = $this->get('grid');
        $ordersTable->setSource($ordersSource);
        $ordersTable->hideColumns('id');
        $ordersTable->getColumn('user')->setTitle('Пользователь');
        $ordersTable->getColumn('itemsJson')->setTitle('Данные заказа');
        $ordersTable->getColumn('datetime')->setTitle('Дата');
        $ordersTable->getColumn('success')->setTitle('Выполнен');
        $ordersTable->isReadyForRedirect();

    	return $this->render(
    		'shop/admin.html.twig', 
    		array_merge($this->getBaseParameters($request),
    			array('usersTable' => $usersTable,
    				'gamesTable' => $gamesTable,
    				'categoryTable' => $categoryTable,
    				'ordersTable' => $ordersTable)));
    }

	/**
	 * @Route("/shop/api/v1", name="shopApiV1")
	 */
	public function apiV1(Request $request)
	{
		$cmd = $request->query->get('cmd','');
		if ($cmd=='') return $this->createApiErrorResponse('Необходима команда');

		switch ($cmd) 
		{
			case 'buy':
				if (false === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) 
				{
			        return $this->createApiErrorResponse('Необходима авторизация');
			    }
			    $order = new Orders();
				$baseParameters = $this->getBaseParameters($request);
				if (count($baseParameters['cart']->items) < 1)
					return $this->createApiErrorResponse('Корзина пуста');
				$order->setUser($this->getUser()->getUsername());
				$order->setItemsJson(json_encode($baseParameters['cart']->items));
				$order->setDatetime(date("Y-m-d H:i:s"));
				$order->setSuccess(false);

				$em = $this->getDoctrine()->getManager();
	            $em->persist($order);
	            $em->flush();

	            return $this->createApiResponse(array('order_time' => $order->getDatetime()));
				break;			
			default:
				break;
		}
	}

	private function createApiErrorResponse($message)
	{
		return new Response(json_encode(
			(object)array(
				'success'=>false,
				'error'=>$message),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}

	private function createApiResponse($params = array())
	{
		return new Response(json_encode(
			(object)array_merge(array('success'=>true), $params),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}


}