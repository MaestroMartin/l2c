<?php

declare(strict_types=1);


namespace App\Presenters;

use Nette\Application\UI\Presenter;
use App\Model\PostManager;
use App\Model\CommentManager;
use Nette\Application\UI\Form;

class PostPresenter extends Presenter
{

    public function __construct(
        private PostManager $postManager,
        private CommentManager $commentManager,
    ) { }

    public function actionManipulate(int $postId = 0): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage('Pro tuto akci je nutné se přihlásit.', 'error');
            $this->redirect('Sign:in', $this->storeRequest());
        }

        if ($postId === 0) {
            return;
        }

        $post = $this->postManager->getById($postId);
        if (!$post) {
            $this->error('Omlouváme se, ale Vámi zvolený příspěvek neexistuje!!!', 404);
        }

        if ($post->authorId !== $this->getUser()->getId()){

            $this['postForm']->error('You dont have permision to edit ', 404);
        }


        $this['postForm']->setDefaults($post->toArray());
    }

    public function renderManipulate(int $postId = 0)
    {
        $this->template->postId = $postId;
    }

    public function renderShow(int $postId): void
    {
        $post = $this->postManager->getById($postId);

        if (!$post) {
            $this->error('Omlouváme se, ale Vámi zvolený příspěvek neexistuje!!!', 404);
        }

        $this->template->post = $post;
        $this->template->comments = $this->commentManager->getCommentsByPostId($postId);
    }

    protected function createComponentCommentForm(): Form
    {
        $form = new Form();

        $form->addText('name', 'Jméno:')
            ->setRequired();

        $form->addEmail('email', 'E-mail:');

        $form->addTextArea('content', 'Komentář:')
            ->setRequired();

        $form->addSubmit('send', 'Publikovat komentář');

        $form->onSuccess[] = [$this, 'commentFormSucceeded'];

        return $form;
    }

    public function commentFormSucceeded(Form $form, \stdClass $values): void
    {
        $postId = $this->getParameter('postId');

        $this->commentManager->insert([
            'post_id' => $postId,
            'name' => $values->name,
            'email' => $values->email,
            'content' => $values->content,
        ]);

        $this->flashMessage('Děkuji za komentář', 'success');
        $this->redirect('this');
    }

    protected function createComponentPostForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Titulek:')
            ->setRequired();
        $form->addTextArea('content', 'Obsah:')
            ->setRequired();

        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = [$this, 'postFormSucceeded'];

        return $form;
    }

    public function postFormSucceeded(Form $form, array $values): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->error('Pro vytvoření, nebo editování příspěvku se musíte přihlásit.');
        }

        $postId = $this->getParameter('postId');

        if ($postId) {
            $post = $this->postManager->getById($postId);
            $post->update($values);
        } else {
            if ($this->getUser()->isLoggedIn()) {
                $values['author_id'] = $this->getUser()->getId();
            }else{
                $values['author_id'] = null;
            }
            $post = $this->postManager->insert($values);
        }

        $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        $this->redirect('show', $post->id);
    }

}