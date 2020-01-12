<?php

namespace User\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Service\RbacManager;
use User\View\Helper\Access;

class AccessFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, 
        $requestedName, array $options = null)
    {   
        $rbacManager = $container->get(RbacManager::class);
        
        return new Access($rbacManager);
    }
}
