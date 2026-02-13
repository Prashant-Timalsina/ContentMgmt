<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Content;

class ContentPolicy
{
    /**
     * View any articles (dashboard listing)
     */
    public function viewAny(User $user)
    {
        return $user->can('create_articles');
    }

    /**
     * View single article
     */
    public function view(?User $user, Content $content)
    {
        // Published articles are viewable by anyone (including guests)
        if ($content->status === Content::STATUS_PUBLISHED) {
            return true;
        }

        // For non-published articles, user must be authenticated
        if (!$user) {
            return false;
        }

        return $user->id === $content->author_id
            || $user->can('publish_articles');
    }

    /**
     * Create article
     */
    public function create(User $user)
    {
        return $user->can('create_articles');
    }

    /**
     * Update article
     */
    public function update(User $user, Content $content)
    {
        return $user->id === $content->author_id
            || $user->can('publish_articles');
    }

    /**
     * Delete article
     */
    public function delete(User $user, Content $content)
    {
        return $user->id === $content->author_id
            || $user->can('manage_users');
    }

    /**
     * Submit article
     */
    public function submit(User $user, Content $content)
    {
        return $user->id === $content->author_id;
    }

    /**
     * Publish article
     */
    public function publish(User $user, Content $content)
    {
        return $user->can('publish_articles');
    }
}
