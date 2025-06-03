<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Config;

class UserDetail extends Component
{
    public User $user;
    public bool $isAdminFromConfig = false;

    public function mount(User $user)
    {
        $this->user = $user->loadCount(['serviceRequests', 'assignedServices', 'skills']);
        $adminEmails = Config::get('app.admin_users', []);
        $this->isAdminFromConfig = in_array($this->user->email, $adminEmails);
    }

    public function render()
    {
        return view('livewire.admin.users.user-detail')
            ->layout('layouts.app'); // Assuming admin pages use the main app layout
    }
}
