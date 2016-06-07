<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Doctrine\ORM\EntityManager;

class MenuBuilder
{
    private $factory;
    private $em;

    /**
     * @param FactoryInterface $factory
     * @param EntityManager $em 
     */
    public function __construct(FactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    private function addMenuChilds($menu, $categories, $parent_path) {
        $childCategories = array_filter($categories,
            function ($cat) use ($parent_path) {
                return preg_match('/^\Q' . $parent_path . '\E\d+\.$/', $cat->getPath());
            });
        usort($childCategories, function ($a, $b) { return strcmp($a->getName(), $b->getName()); });
        if (count($childCategories) > 0) {
            $menu->setAttribute('class', 'dropdown-submenu');
            $menu->setChildrenAttribute('class', 'dropdown-menu');
        }
        foreach ($childCategories as $cat) {
            $menu->addChild($cat->getName(), array('route' => 'steamgames', 
                'routeParameters' => array('category' => $cat->getPath())));
            $this->addMenuChilds($menu[$cat->getName()], $categories, $cat->getPath());
        }
    }

    public function createShopMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $categories = $this->em->getRepository('AppBundle:GameCategory')->findAll();
        $this->addMenuChilds($menu, $categories, '.');
        return $menu;
    }
}