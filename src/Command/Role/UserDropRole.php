<?php

namespace App\Command\Role;

use App\Command\BaseCommand;
use App\Service\Entity\UserService;
use App\Service\Entity\RoleService;
use App\Util\Common\UpdateUserRole;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserDropRole extends BaseCommand
{
    private UserService $userService;
    private RoleService $roleService;

    public function __construct(
        ContainerInterface $container,
        UserService $commonUserService,
        RoleService $roleService,
        string $name = null)
    {
        $this->userService = $commonUserService;
        $this->roleService = $roleService;
        parent::__construct($container, $name);
    }

    protected function configure()
    {
        $this
            ->setName('user-drop-role')
            ->addArgument('email', InputArgument::REQUIRED, 'user email.')
            ->addArgument('role', InputArgument::REQUIRED, 'user role.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dropRole = false;
        $role = $this->roleService->getByName($input->getArgument('role'));

        if (is_null($role)) {
            $output->writeln('role not found');
            return 1;
        }

        $user = $this->userService->getByEmail($input->getArgument('email'));

        if (is_null($user)) {
            $output->writeln('user not found');
            return 1;
        }

        foreach ($user->getRoles() as $roleUser) {
            if ($roleUser->getName() === $role->getName()) {
                $this->userService->removeRoles($user, $role);
                $dropRole = true;
                break;
            }
        }

        if ($dropRole) {
            $output->writeln('role drop to user');
            return 1;
        } else {
            $output->writeln('the user does not have such a role
');
            return 1;
        }
    }
}
