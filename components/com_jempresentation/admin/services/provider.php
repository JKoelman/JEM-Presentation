<?php
defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory as ComponentDispatcherFactoryServiceProvider;
use Joomla\CMS\Extension\Service\Provider\MVCFactory as MVCFactoryServiceProvider;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->registerServiceProvider(
            new MVCFactoryServiceProvider('\\KoelmanLabs\\Component\\JemPresentation')
        );

        $container->registerServiceProvider(
            new ComponentDispatcherFactoryServiceProvider('\\KoelmanLabs\\Component\\JemPresentation')
        );

        $container->set(
            ComponentInterface::class,
            static function (Container $container): ComponentInterface {
                $component = new MVCComponent(
                    $container->get(ComponentDispatcherFactoryInterface::class)
                );

                $component->setMVCFactory(
                    $container->get(MVCFactoryInterface::class)
                );

                return $component;
            }
        );
    }
};
