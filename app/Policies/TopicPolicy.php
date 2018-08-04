<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Topic;

class TopicPolicy extends Policy
{
    // public function update(User $user, Topic $topic)
    // {
    //     return $topic->user_id == $user->id;
    // }

    // public function destroy(User $user, Topic $topic)
    // {
    //     return $topic->user_id == $user->id;
    // }
    // 上面一直在重复$topic->user_id == $user->id,接下来我们去修改User模型

    public function update(User $user, Topic $topic)
    {
        return $user->isAuthorOf($topic);
    }

    public function destroy(User $user, Topic $topic)
    {
        return $user->isAuthorOf($topic);
    }
}
