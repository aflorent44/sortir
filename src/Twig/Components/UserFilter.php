<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('filter-users', template: 'components/UserFilter.html.twig')]
final class UserFilter
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?string $query = "";

    public function __construct(private readonly UserRepository $ur)
    {
    }

    public function getUsers(): array
    {
        return $this->ur->findByName($this->query);
    }

}