<?php
// api/src/Controller/UsersMe.php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UsersMe extends AbstractController
{
    public function __construct(
        private User $user
    ) {
    }

    public function __invoke(): User
    {
        return $this->user;
    }
}