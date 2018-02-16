<?php

namespace PHPCensor\Controller;

use b8;
use b8\Exception\HttpException\NotFoundException;
use b8\Form;
use PHPCensor\Controller;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\User;
use PHPCensor\Service\UserService;
use PHPCensor\View;

/**
 * User Controller - Allows an administrator to view, add, edit and delete users.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class UserController extends Controller
{
    /**
     * @var \PHPCensor\Store\UserStore
     */
    protected $userStore;

    /**
     * @var \PHPCensor\Service\UserService
     */
    protected $userService;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->userStore = b8\Store\Factory::getStore('User');
        $this->userService = new UserService($this->userStore);
    }

    /**
    * View user list.
    */
    public function index()
    {
        $users               = $this->userStore->getWhere([], 1000, 0, [], ['email' => 'ASC']);
        $this->view->users   = $users;
        $this->layout->title = Lang::get('manage_users');

        return $this->view->render();
    }

    /**
     * Allows the user to edit their profile.
     * @return string
     */
    public function profile()
    {
        /** @var User $user */
        $user = $_SESSION['php-censor-user'];

        if ($this->request->getMethod() == 'POST') {
            $name     = $this->getParam('name', null);
            $email    = $this->getParam('email', null);
            $password = $this->getParam('password', null);

            $language = $this->getParam('language', null);
            if (!$language) {
                $language = null;
            }

            $perPage  = $this->getParam('per_page', null);
            if (!$perPage) {
                $perPage = null;
            }

            $_SESSION['php-censor-user'] = $this->userService->updateUser($user, $name, $email, $password, null, $language, $perPage);
            $user                        = $_SESSION['php-censor-user'];

            $this->view->updated = 1;
        }

        $this->layout->title    = $user->getName();
        $this->layout->subtitle = Lang::get('edit_profile');

        $form = new Form();
        $form->setAction(APP_URL.'user/profile');
        $form->setMethod('POST');

        $name = new Form\Element\Text('name');
        $name->setClass('form-control');
        $name->setContainerClass('form-group');
        $name->setLabel(Lang::get('name'));
        $name->setRequired(true);
        $name->setValue($user->getName());
        $form->addField($name);

        $email = new Form\Element\Email('email');
        $email->setClass('form-control');
        $email->setContainerClass('form-group');
        $email->setLabel(Lang::get('email_address'));
        $email->setRequired(true);
        $email->setValue($user->getEmail());
        $form->addField($email);

        $password = new Form\Element\Password('password');
        $password->setClass('form-control');
        $password->setContainerClass('form-group');
        $password->setLabel(Lang::get('password_change'));
        $password->setRequired(false);
        $password->setValue(null);
        $form->addField($password);

        $language = new Form\Element\Select('language');
        $language->setClass('form-control');
        $language->setContainerClass('form-group');
        $language->setLabel(Lang::get('language'));
        $language->setRequired(true);
        $language->setOptions(array_merge(
            [null => Lang::get('default') . ' (' . b8\Config::getInstance()->get('php-censor.language') .  ')'],
            Lang::getLanguageOptions())
        );
        $language->setValue($user->getLanguage());
        $form->addField($language);

        $perPage = new Form\Element\Select('per_page');
        $perPage->setClass('form-control');
        $perPage->setContainerClass('form-group');
        $perPage->setLabel(Lang::get('per_page'));
        $perPage->setRequired(true);
        $perPage->setOptions([
            null => Lang::get('default') . ' (' . b8\Config::getInstance()->get('php-censor.per_page') .  ')',
            10    => 10,
            25    => 25,
            50    => 50,
            100   => 100,
        ]);
        $perPage->setValue($user->getPerPage());
        $form->addField($perPage);

        $submit = new Form\Element\Submit();
        $submit->setClass('btn btn-success');
        $submit->setValue(Lang::get('save'));
        $form->addField($submit);

        $this->view->form = $form;

        return $this->view->render();
    }

    /**
    * Add a user - handles both form and processing.
    */
    public function add()
    {
        $this->requireAdmin();

        $this->layout->title = Lang::get('add_user');

        $method = $this->request->getMethod();

        if ($method == 'POST') {
            $values = $this->getParams();
        } else {
            $values = [];
        }

        $form   = $this->userForm($values);

        if ($method != 'POST' || ($method == 'POST' && !$form->validate())) {
            $view       = new View('User/edit');
            $view->type = 'add';
            $view->user = null;
            $view->form = $form;

            return $view->render();
        }


        $name     = $this->getParam('name', null);
        $email    = $this->getParam('email', null);
        $password = $this->getParam('password', null);
        $isAdmin  = (int)$this->getParam('is_admin', 0);

        $this->userService->createUser($name, $email, 'internal', json_encode(['type' => 'internal']), $password, $isAdmin);

        $response = new b8\Http\Response\RedirectResponse();
        $response->setHeader('Location', APP_URL . 'user');
        return $response;
    }

    /**
    * Edit a user - handles both form and processing.
    */
    public function edit($userId)
    {
        $this->requireAdmin();

        $method = $this->request->getMethod();
        $user = $this->userStore->getById($userId);

        if (empty($user)) {
            throw new NotFoundException(Lang::get('user_n_not_found', $userId));
        }

        $this->layout->title = $user->getName();
        $this->layout->subtitle = Lang::get('edit_user');

        $values = array_merge($user->getDataArray(), $this->getParams());
        $form = $this->userForm($values, 'edit/' . $userId);

        if ($method != 'POST' || ($method == 'POST' && !$form->validate())) {
            $view = new View('User/edit');
            $view->type = 'edit';
            $view->user = $user;
            $view->form = $form;

            return $view->render();
        }

        $name     = $this->getParam('name', null);
        $email    = $this->getParam('email', null);
        $password = $this->getParam('password', null);
        $isAdmin  = (int)$this->getParam('is_admin', 0);

        $this->userService->updateUser($user, $name, $email, $password, $isAdmin);

        $response = new b8\Http\Response\RedirectResponse();
        $response->setHeader('Location', APP_URL . 'user');
        return $response;
    }

    /**
    * Create user add / edit form.
    */
    protected function userForm($values, $type = 'add')
    {
        $form = new Form();
        $form->setMethod('POST');
        $form->setAction(APP_URL.'user/' . $type);
        $form->addField(new Form\Element\Csrf('csrf'));

        $field = new Form\Element\Email('email');
        $field->setRequired(true);
        $field->setLabel(Lang::get('email_address'));
        $field->setClass('form-control');
        $field->setContainerClass('form-group');
        $form->addField($field);

        $field = new Form\Element\Text('name');
        $field->setRequired(true);
        $field->setLabel(Lang::get('name'));
        $field->setClass('form-control');
        $field->setContainerClass('form-group');
        $form->addField($field);

        $field = new Form\Element\Password('password');

        if ($type == 'add') {
            $field->setRequired(true);
            $field->setLabel(Lang::get('password'));
        } else {
            $field->setRequired(false);
            $field->setLabel(Lang::get('password_change'));
        }

        $field->setClass('form-control');
        $field->setContainerClass('form-group');
        $form->addField($field);

        $field = new Form\Element\Checkbox('is_admin');
        $field->setRequired(false);
        $field->setCheckedValue(1);
        $field->setLabel(Lang::get('is_user_admin'));
        $field->setContainerClass('form-group');
        $form->addField($field);

        $field = new Form\Element\Submit();
        $field->setValue(Lang::get('save_user'));
        $field->setClass('btn-success');
        $form->addField($field);

        $form->setValues($values);
        return $form;
    }

    /**
    * Delete a user.
    */
    public function delete($userId)
    {
        $this->requireAdmin();

        $user   = $this->userStore->getById($userId);

        if (empty($user)) {
            throw new NotFoundException(Lang::get('user_n_not_found', $userId));
        }

        $this->userService->deleteUser($user);

        $response = new b8\Http\Response\RedirectResponse();
        $response->setHeader('Location', APP_URL . 'user');
        return $response;
    }
}
