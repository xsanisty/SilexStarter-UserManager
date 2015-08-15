<?php

namespace Xsanisty\UserManager\Controller;

use Exception;
use InvalidArgumentException;
use Xsanisty\Admin\DashboardModule;
use Xsanisty\UserManager\Repository\UserRepository;
use SilexStarter\Controller\DispatcherAwareController;

class AccountController extends DispatcherAwareController
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function myAccount()
    {
        $this->getDispatcher()->dispatch(DashboardModule::INIT);
        $user = $this->userRepository->getCurrentUser();

        return Response::view(
            '@silexstarter-usermanager/account/my_account',
            [
                'userdata' => $user,
                'groups' => $user->getGroups(),
                'permissions' => $user->getMergedPermissions(),
                'title' => 'My Account',
                'page_title' => 'My Account',
                'success' => Session::getFlash('success'),
                'error' => Session::getFlash('error')
            ]
        );
    }

    public function updateAccount()
    {
        try {
            $user       = $this->userRepository->getCurrentUser();
            $password   = Request::get('password');

            if ($password && $password != Request::get('confirm_password')) {
                throw new InvalidArgumentException("Password and confirmation password not match", 1);
            }

            if (trim($password)) {
                $user->password = $password;
            }

            $user->first_name   = Request::get('first_name');
            $user->last_name    = Request::get('last_name');

            $user->save();

            Session::flash('success', 'Your information is updated!');

        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        return Response::redirect(Url::to('usermanager.my_account'));
    }
}
