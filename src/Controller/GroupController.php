<?php

namespace Xsanisty\UserManager\Controller;

use Xsanisty\Admin\DashboardModule;
use SilexStarter\Controller\DispatcherAwareController;
use Xsanisty\UserManager\Repository\GroupRepository;
use Xsanisty\UserManager\Repository\UserRepository;

class GroupController extends DispatcherAwareController
{
    protected $groupRepo;
    protected $userRepo;

    public function __construct(GroupRepository $groupRepo, UserRepository $userRepo)
    {
        $this->groupRepo = $groupRepo;
        $this->userRepo  = $userRepo;
    }

    public function index()
    {
        $this->getDispatcher()->dispatch(DashboardModule::INIT);
        Menu::get('admin_sidebar')->setActive('user-manager.manage-group');

        return View::make(
            '@silexstarter-usermanager/group/index',
            [
                'user' => $this->userRepo->getCurrentUser()
            ]
        );
    }

    public function create()
    {

    }

    public function store()
    {

    }

    public function edit($id)
    {

    }

    public function update($id)
    {

    }

    public function destory($id)
    {

    }
}
