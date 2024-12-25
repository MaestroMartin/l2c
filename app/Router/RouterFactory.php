<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
        $router->addRoute('prihlaseni/', 'Sign:in')
            ->addRoute('editacePrispevku/<postId>', 'Post:manipulate')
            ->addRoute('vytvoreniPrispevku/', 'Post:manipulate', Nette\Application\Routers\Route::ONE_WAY)
            ->addRoute('vytvoreni/prispevku/', 'Post:manipulate')
            ->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}
}
