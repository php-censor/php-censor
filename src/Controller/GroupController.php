<?php

declare(strict_types=1);

namespace PHPCensor\Controller;

use DateTime;
use PHPCensor\Form;
use PHPCensor\Helper\Lang;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use PHPCensor\Model\ProjectGroup;
use PHPCensor\Model\User;
use PHPCensor\Store\ProjectGroupStore;
use PHPCensor\WebController;

/**
 * @package    PHP Censor
 * @subpackage Application
 *
 * @author Dan Cryer <dan@block8.co.uk>
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class GroupController extends WebController
{
    public string $layoutName = 'layout';

    protected ProjectGroupStore $groupStore;

    public function init(): void
    {
        parent::init();

        $this->groupStore = $this->storeRegistry->get('ProjectGroup');
    }

    /**
     * List project groups.
     */
    public function index(): void
    {
        $this->requireAdmin();

        $groups    = [];
        $groupList = $this->groupStore->getWhere([], 100, 0, ['title' => 'ASC']);

        foreach ($groupList['items'] as $group) {
            $thisGroup = [
                'title' => $group->getTitle(),
                'id'    => $group->getId(),
            ];
            $projectsActive   = $this->storeRegistry->get('Project')->getByGroupId($group->getId(), false);
            $projectsArchived = $this->storeRegistry->get('Project')->getByGroupId($group->getId(), true);

            $thisGroup['projects'] = \array_merge($projectsActive['items'], $projectsArchived['items']);
            $groups[]              = $thisGroup;
        }

        $this->layout->title = Lang::get('group_projects');
        $this->view->groups  = $groups;
        $this->view->user    = $this->getUser();
    }

    /**
     * Add or edit a project group.
     *
     * @return Response
     *
     * @throws \PHPCensor\Common\Exception\InvalidArgumentException
     * @throws \PHPCensor\Common\Exception\RuntimeException
     * @throws \PHPCensor\Exception\HttpException
     */
    public function edit(?int $groupId = null)
    {
        $this->requireAdmin();

        if (!\is_null($groupId)) {
            $group = $this->groupStore->getById($groupId);
        } else {
            $group = new ProjectGroup($this->storeRegistry);
        }

        if ($this->request->getMethod() === 'POST') {
            $group->setTitle($this->getParam('title'));
            if (\is_null($groupId)) {
                /** @var User $user */
                $user = $this->getUser();

                $group->setCreateDate(new DateTime());
                $group->setUserId($user->getId());
            }

            $this->groupStore->save($group);

            $response = new RedirectResponse(APP_URL.'group');

            return $response;
        }

        $form = new Form();

        $form->setMethod('POST');
        $form->setAction(APP_URL . 'group/edit' . (!\is_null($groupId) ? '/' . $groupId : ''));

        $form->addField(new Form\Element\Csrf('group_form'));

        $title = new Form\Element\Text('title');
        $title->setContainerClass('form-group');
        $title->setClass('form-control');
        $title->setLabel(Lang::get('group_title'));
        $title->setValue($group->getTitle());

        $submit = new Form\Element\Submit();
        $submit->setClass('btn btn-success');
        $submit->setValue(Lang::get('group_save'));

        $form->addField($title);
        $form->addField($submit);

        $this->view->form = $form;
    }

    /**
     * Delete a project group.
     *
     * @throws \PHPCensor\Common\Exception\Exception
     * @throws \PHPCensor\Common\Exception\InvalidArgumentException
     * @throws \PHPCensor\Exception\HttpException
     */
    public function delete(int $groupId): Response
    {
        $this->requireAdmin();
        $group = $this->groupStore->getById($groupId);

        $this->groupStore->delete($group);
        $response = new RedirectResponse(APP_URL.'group');

        return $response;
    }
}
