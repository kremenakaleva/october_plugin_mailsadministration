<?php namespace Pensoft\Mailsadministration;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }


    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
        \Event::listen('backend.menu.extendItems', function($navigationManager) {
            $user = \BackendAuth::getUser(); // get the logged in user
            if(!$user->is_superuser){
                $navigationManager->removeMainMenuItem('October.System', 'system');
                $navigationManager->removeMainMenuItem('Pensoft.Cardprofiles', 'profile-cards');
                $navigationManager->removeMainMenuItem('Pensoft.Calendar', 'main-menu-item');
                $navigationManager->removeMainMenuItem('Pensoft.Accordions', 'main-menu-item');
                $navigationManager->removeMainMenuItem('Pensoft.Partners', 'main-menu-item');
                $navigationManager->removeMainMenuItem('Pensoft.Media', 'media-center');
                $navigationManager->removeMainMenuItem('Pensoft.Articles', 'main-menu-item');
                $navigationManager->removeMainMenuItem('Pensoft.Library', 'main-menu-item');
                $navigationManager->removeMainMenuItem('Pensoft.Jumbotron', 'main-menu-item');
                $navigationManager->removeMainMenuItem('Pensoft.Knowledgelibrary', 'main-menu-item');
                $navigationManager->removeMainMenuItem('Pensoft.Contactform', 'contactform');
            }
        });
    }
}
