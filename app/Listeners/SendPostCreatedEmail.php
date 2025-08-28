<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Services\UserService;
use App\Mail\NewPostNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPostCreatedEmail
{
   protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $users = $this->userService->getAllUsersList();
        foreach($users as $user){
            Mail::to($user->email)->queue(new NewPostNotification($event->post));
        }
    }
}
