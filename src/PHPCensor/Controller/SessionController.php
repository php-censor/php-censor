<?php

namespace PHPCensor\Controller;

use b8;
use PHPCensor\Helper\Email;
use PHPCensor\Helper\Lang;
use PHPCensor\Controller;
use PHPCensor\Security\Authentication\Service;
use PHPCensor\Store\UserStore;

/**
 * Session Controller - Handles user login / logout.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class SessionController extends Controller
{
    /**
     * @var UserStore
     */
    protected $userStore;

    /**
     * @var Service
     */
    protected $authentication;

    /**
     * Initialise the controller, set up stores and services.
     */
    public function init()
    {
        $this->response->disableLayout();

        $this->userStore      = b8\Store\Factory::getStore('User');
        $this->authentication = Service::getInstance();
    }

    /**
     * Handles user login (form and processing)
     */
    public function login()
    {
        if (!empty($_COOKIE['remember_key'])) {
            $user = $this->userStore->getByRememberKey($_COOKIE['remember_key']);
            if ($user) {
                $_SESSION['php-censor-user-id'] = $user->getId();

                $response = new b8\Http\Response\RedirectResponse();
                $response->setHeader('Location', $this->getLoginRedirect());

                return $response;
            }
        }

        $isLoginFailure = false;

        if ($this->request->getMethod() == 'POST') {
            $token = $this->getParam('token');
            if (!isset($token, $_SESSION['login_token']) || $token !== $_SESSION['login_token']) {
                $isLoginFailure = true;
            } else {
                unset($_SESSION['login_token']);

                $email          = $this->getParam('email');
                $password       = $this->getParam('password', '');
                $rememberMe     = (bool)$this->getParam('remember_me', 0);
                $isLoginFailure = true;

                $user      = $this->userStore->getByEmailOrName($email);
                $providers = $this->authentication->getLoginPasswordProviders();

                if (null !== $user) {
                    // Delegate password verification to the user provider, if found
                    $key = $user->getProviderKey();
                    $isLoginFailure = !isset($providers[$key]) || !$providers[$key]->verifyPassword($user, $password);
                } else {
                    // Ask each providers to provision the user
                    foreach ($providers as $provider) {
                        $user = $provider->provisionUser($email);
                        if ($user && $provider->verifyPassword($user, $password)) {
                            $this->userStore->save($user);
                            $isLoginFailure = false;
                            break;
                        }
                    }
                }

                if (!$isLoginFailure) {
                    $_SESSION['php-censor-user-id'] = $user->getId();

                    if ($rememberMe) {
                        $rememberKey = md5(microtime(true));

                        $user->setRememberKey($rememberKey);
                        $this->userStore->save($user);

                        setcookie(
                            'remember_key',
                            $rememberKey,
                            (time() + 60 * 60 * 24 * 30),
                            null,
                            null,
                            null,
                            true
                        );
                    }

                    $response = new b8\Http\Response\RedirectResponse();
                    $response->setHeader('Location', $this->getLoginRedirect());

                    return $response;
                }
            }
        }

        $form = new b8\Form();
        $form->setMethod('POST');
        $form->setAction(APP_URL . 'session/login');

        $email = new b8\Form\Element\Text('email');
        $email->setLabel(Lang::get('login'));
        $email->setRequired(true);
        $email->setContainerClass('form-group');
        $email->setClass('form-control');
        $form->addField($email);

        $pwd = new b8\Form\Element\Password('password');
        $pwd->setLabel(Lang::get('password'));
        $pwd->setRequired(true);
        $pwd->setContainerClass('form-group');
        $pwd->setClass('form-control');
        $form->addField($pwd);

        $remember = b8\Form\Element\Checkbox::create('remember_me', Lang::get('remember_me'), false);
        $remember->setContainerClass('form-group');
        $remember->setCheckedValue(1);
        $remember->setValue(0);
        $form->addField($remember);

        $pwd = new b8\Form\Element\Submit();
        $pwd->setValue(Lang::get('log_in'));
        $pwd->setClass('btn-success');
        $form->addField($pwd);

        $tokenValue = $this->generateToken();
        $_SESSION['login_token'] = $tokenValue;
        $token = new b8\Form\Element\Hidden('token');
        $token->setValue($tokenValue);
        $form->addField($token);

        $this->view->form = $form->render();
        $this->view->failed = $isLoginFailure;

        return $this->view->render();
    }

    /**
    * Handles user logout.
    */
    public function logout()
    {
        unset($_SESSION['php-censor-user-id']);

        session_destroy();

        setcookie(
            'remember_key',
            null,
            (time() - 1),
            null,
            null,
            null,
            true
        );

        $response = new b8\Http\Response\RedirectResponse();
        $response->setHeader('Location', APP_URL);
        return $response;
    }

    /**
     * Allows the user to request a password reset email.
     * @return string
     */
    public function forgotPassword()
    {
        if ($this->request->getMethod() == 'POST') {
            $email = $this->getParam('email', null);
            $user = $this->userStore->getByEmail($email);

            if (empty($user)) {
                $this->view->error = Lang::get('reset_no_user_exists');
                return $this->view->render();
            }

            $key = md5(date('Y-m-d') . $user->getHash());
            $url = APP_URL;

            $message = Lang::get('reset_email_body', $user->getName(), $url, $user->getId(), $key);

            $email = new Email();
            $email->setEmailTo($user->getEmail(), $user->getName());
            $email->setSubject(Lang::get('reset_email_title', $user->getName()));
            $email->setBody($message);
            $email->send();

            $this->view->emailed = true;
        }

        return $this->view->render();
    }

    /**
     * Allows the user to change their password after a password reset email.
     * @param $userId
     * @param $key
     * @return string
     */
    public function resetPassword($userId, $key)
    {
        $user = $this->userStore->getById($userId);
        $userKey = md5(date('Y-m-d') . $user->getHash());

        if (empty($user) || $key != $userKey) {
            $this->view->error = Lang::get('reset_invalid');
            return $this->view->render();
        }

        if ($this->request->getMethod() == 'POST') {
            $hash = password_hash($this->getParam('password'), PASSWORD_DEFAULT);
            $user->setHash($hash);

            $this->userStore->save($user);

            $_SESSION['php-censor-user-id'] = $user->getId();

            $response = new b8\Http\Response\RedirectResponse();
            $response->setHeader('Location', APP_URL);
            return $response;
        }

        $this->view->id = $userId;
        $this->view->key = $key;

        return $this->view->render();
    }

    /**
     * Get the URL the user was trying to go to prior to being asked to log in.
     * @return string
     */
    protected function getLoginRedirect()
    {
        $rtn = APP_URL;

        if (!empty($_SESSION['php-censor-login-redirect'])) {
            $rtn .= $_SESSION['php-censor-login-redirect'];
            $_SESSION['php-censor-login-redirect'] = null;
        }

        return $rtn;
    }

    /** Generate a random token.
     *
     * @return string
     */
    protected function generateToken()
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes(16));
        }

        return sprintf("%04x", mt_rand(0, 0xFFFF))
            . sprintf("%04x", mt_rand(0, 0xFFFF))
            . sprintf("%04x", mt_rand(0, 0xFFFF))
            . sprintf("%04x", mt_rand(0, 0xFFFF));
    }
}
