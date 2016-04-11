<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LoginForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function indexAction()
    {
        $form = $this->createFormBuilder(new LoginForm())
            ->add('login', TextType::class)
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class, array ('label' => 'Sign in'))
            ->getForm();

        return $this->render('login/login.html.twig', array (
            'form' => $form->createView()
        ));
    }
}