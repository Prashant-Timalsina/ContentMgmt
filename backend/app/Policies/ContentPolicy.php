<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Content;

class ContentPolicy
{
    /**
     * Author Articles
     */
    public function viewOwn(User $user)
    {
        return $user->can('create_articles');
    }
    
    /**
     * Admin Review list of articles
     */
    public function viewAll(User $user)
    {
        return $user->can('publish_articles');
    }

    /**
     * Public Viewing of articles
     */
    public function view(?User $user, Content $content)
    {
        // Published articles are viewable by anyone (including guests)
        if ($content->status === Content::STATUS_PUBLISHED) {
            return true;
        }

        // For non-published articles, user must be authenticated
        if (!$user) return false;

        //Author can see own
        if( $user->id === $content->author_id) return true;

        // Admon sees non draft
        return $user->can('publish_articles');
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
        if( $user->id === $content->author_id) return true;
        
        return $user->can('publish_articles');
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

    /**
     * Reject article (admin only)
     */
    public function reject(User $user, Content $content)
    {
        return $user->can('publish_articles');
    }
}
