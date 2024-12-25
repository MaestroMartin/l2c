<?php


namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;

class SignPresenter extends Presenter
{

    private string $storeRequestId = '';

    public function actionIn(string $storeRequestId = '')
    {
        $this->storeRequestId = $storeRequestId;
    }

    public function actionOut()
    {
        $this->user->logout(true);
        $this->flashMessage('Odhlášení proběhlo úspěšně', 'success');
        $this->redirect('Homepage:');
    }

    protected function createComponentSignInForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Prosím vyplňte své uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte své heslo.');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $form;
    }

    public function signInFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $this->user->login($values->username, $values->password);
            $this->flashMessage('Úspěšně přihlášeno.', 'success');
            $this->restoreRequest($this->storeRequestId);
            $this->redirect('Homepage:');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
        }
    }

}
