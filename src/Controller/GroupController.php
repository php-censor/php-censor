<?php

namespace PHPCensor\Controller;

use PHPCensor\Form;
use PHPCensor\WebController;
use PHPCensor\Http\Response\RedirectResponse;
use PHPCensor\Model\ProjectGroup;
use PHPCensor\Helper\Lang;
use PHPCensor\Model\User;
use PHPCensor\Store\Factory;

/**
 * Project Controller - Allows users to create, edit and view projects.
 *
 * @author Dan Cryer <dan@block8.co.uk>
 */
class GroupController extends WebController
{
    /**
     * @var string
     */
    public $layoutName = 'layout';

    /**
     * @var \PHPCensor\Store\ProjectGroupStore
     */
    protected $groupStore;

    public function init()
    {
        parent::init();

        $this->groupStore = Factory::getStore('ProjectGroup');
    }

    /**
     * List project groups.
     */
    public function index()
    {
        $this->requireAdmin();

        $groups    = [];
        $groupList = $this->groupStore->getWhere([], 100, 0, ['title' => 'ASC']);

        foreach ($groupList['items'] as $group) {
            $thisGroup = [
                'title' => $group->getTitle(),
                'id'    => $group->getId(),
            ];
            $projectsActive   = Factory::getStore('Project')->getByGroupId($group->getId(), false);
            $projectsArchived = Factory::getStore('Project')->getByGroupId($group->getId(), true);

            $thisGroup['projects'] = array_merge($projectsActive['items'], $projectsArchived['items']);
            $groups[]              = $thisGroup;
        }

        $this->layout->title = Lang::get('group_projects');
        $this->view->groups  = $groups;
    }

    /**
     * Add or edit a project group.
     *
     * @param null $groupId
     *
     * @return RedirectResponse
     */
    public function edit($groupId = null)
    {
        $this->requireAdmin();

        if (!is_null($groupId)) {
            $group = $this->groupStore->getById($groupId);
        } else {
            $group = new ProjectGroup();
        }

        if ($this->request->getMethod() == 'POST') {
            $group->setTitle($this->getParam('title'));
            if (is_null($groupId)) {
                /** @var User $user */
                $user = $this->getUser();

                $group->setCreateDate(new \DateTime());
                $group->setUserId($user->getId());
            }

            $this->groupStore->save($group);

            $response = new RedirectResponse();
            $response->setHeader('Location', APP_URL.'group');

            return $response;
        }

        $form = new Form();

        $form->setMethod('POST');
        $form->setAction(APP_URL . 'group/edit' . (!is_null($groupId) ? '/' . $groupId : ''));

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
     * @param $groupId
     * @return RedirectResponse
     */
    public function delete($groupId)
    {
        $this->requireAdmin();
        $group = $this->groupStore->getById($groupId);

        $this->groupStore->delete($group);
        $response = new RedirectResponse();
        $response->setHeader('Location', APP_URL.'group');
        return $response;
    }
}
