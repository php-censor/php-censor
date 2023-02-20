<?php

declare(strict_types=1);

namespace PHPCensor\Controller;

use DateTime;
use PHPCensor\Form;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\Secret;
use PHPCensor\Store\SecretStore;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PHPCensor\Model\User;
use PHPCensor\WebController;
use PHPCensor\Form\Element\Csrf;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class SecretController extends WebController
{
    public string $layoutName = 'layout';

    protected SecretStore $secretStore;

    public function init(): void
    {
        parent::init();

        $this->secretStore = $this->storeRegistry->get('Secret');
    }

    public function index(): void
    {
        $this->requireAdmin();

        $secrets    = [];
        $secretList = $this->secretStore->getWhere([], 100, 0, ['name' => 'ASC']);

        foreach ($secretList['items'] as $secret) {
            $thisSecret = [
                'name' => $secret->getName(),
                'id'    => $secret->getId(),
            ];
            $secrets[] = $thisSecret;
        }

        $this->layout->title = Lang::get('secrets');
        $this->view->secrets = $secrets;
        $this->view->user    = $this->getUser();
    }

    /**
     * @return Response
     *
     * @throws \PHPCensor\Common\Exception\InvalidArgumentException
     * @throws \PHPCensor\Common\Exception\RuntimeException
     * @throws \PHPCensor\Exception\HttpException
     */
    public function edit(?int $secretId = null)
    {
        $this->requireAdmin();

        if (!\is_null($secretId)) {
            $secret = $this->secretStore->getById($secretId);
        } else {
            $secret = new Secret($this->storeRegistry);
        }

        if ($this->request->getMethod() === 'POST') {
            $secret->setName($this->getParam('name'));
            $secret->setValue($this->getParam('value'));
            if (\is_null($secretId)) {
                /** @var User $user */
                $user = $this->getUser();

                $secret->setCreateDate(new DateTime());
                $secret->setUserId($user->getId());
            }

            $this->secretStore->save($secret);

            $response = new RedirectResponse(APP_URL . 'secret');

            return $response;
        }

        $form = new Form();

        $form->setMethod('POST');
        $form->setAction(APP_URL . 'secret/edit' . (!\is_null($secretId) ? '/' . $secretId : ''));

        $form->addField(new Csrf($this->session, 'secret_form'));

        $field = Form\Element\Text::create('name', Lang::get('secret_name'), true);
        $field
            ->setClass('form-control')
            ->setContainerClass('form-group')
            ->setPattern(Secret::SECRET_NAME_PATTERN)
            ->setValue($secret->getName());
        $form->addField($field);

        $field = Form\Element\TextArea::create('value', Lang::get('secret_value'), true);
        $field
            ->setClass('form-control')
            ->setContainerClass('form-group')
            ->setRows(8)
            ->setValue($secret->getValue());
        $form->addField($field);

        $submit = new Form\Element\Submit();
        $submit->setClass('btn btn-success');
        $submit->setValue(Lang::get('secret_save'));
        $form->addField($submit);

        $this->view->form = $form;
    }

    /**
     * @throws \PHPCensor\Common\Exception\Exception
     * @throws \PHPCensor\Common\Exception\InvalidArgumentException
     * @throws \PHPCensor\Exception\HttpException
     */
    public function delete(int $secretId): Response
    {
        $this->requireAdmin();

        $group = $this->secretStore->getById($secretId);

        $this->secretStore->delete($group);

        return new RedirectResponse(APP_URL . 'secret');
    }
}
