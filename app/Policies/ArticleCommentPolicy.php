<?php

namespace App\Policies;

use App\Models\ArticleComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticleCommentPolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, ArticleComment $articleComment)
    {
        return $user->id === $articleComment->user_id;
    }
}
