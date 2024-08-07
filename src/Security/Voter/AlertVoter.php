<?php

namespace OHMedia\AlertBundle\Security\Voter;

use OHMedia\AlertBundle\Entity\Alert;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class AlertVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::INDEX,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return Alert::class;
    }

    protected function canIndex(Alert $alert, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(Alert $alert, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(Alert $alert, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(Alert $alert, User $loggedIn): bool
    {
        return true;
    }
}
