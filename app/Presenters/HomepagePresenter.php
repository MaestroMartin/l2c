<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\PostFacade;
use Nette;
use App\Model\PostManager;
use Nette\Application\UI\Presenter;

final class HomepagePresenter extends Presenter
{
    public function __construct(
        private PostFacade $postManager,
    ) { }

    public function renderDefault()
    {
        $this->template->posts = $this->postManager->getPublicPosts(5);
    }

}
