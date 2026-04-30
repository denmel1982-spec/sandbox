<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Announcement;

class AnnouncementPolicy
{
    /**
     * Разрешить создание объявления любому авторизованному пользователю
     */
    public function create(User $user): bool
    {
        return $user->is_active && !$user->is_blocked;
    }

    /**
     * Разрешить обновление только владельцу или администратору
     */
    public function update(User $user, Announcement $announcement): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $announcement->user_id;
    }

    /**
     * Разрешить удаление только владельцу или администратору
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $announcement->user_id;
    }

    /**
     * Разрешить архивирование только владельцу или администратору
     */
    public function archive(User $user, Announcement $announcement): bool
    {
        return $this->update($user, $announcement);
    }

    /**
     * Администратор может модерировать любые объявления
     */
    public function moderate(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin();
    }
}
