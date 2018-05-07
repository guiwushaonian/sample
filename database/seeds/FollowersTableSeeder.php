<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. 用户1号关注除自己外的所有人
        // 2. 其他所有的人，关注1号用户

        $users = User::all();
        $user = $users->first();
        $user_id = $user->id;

        $followers = $users->slice(1);
        $follower_ids = $followers->pluck('id')->toArray();

        $user->follow($follower_ids);
        foreach ($followers as $follower) {
            $follower->follow($user_id);
        }
    }
}
