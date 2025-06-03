<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Config;

class UserList extends Component
{
    use WithPagination;

    public $search = '';
    protected $queryString = ['search' => ['except' => '']];

    public function viewUser(int $userId)
    {
        return redirect()->route('admin.users.show', ['user' => $userId]);
    }

    public function render()
    {
        $adminEmails = Config::get('app.admin_users', []);

        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id')
            ->paginate(10);

        // Augment users with admin status based on config
        $users->getCollection()->transform(function ($user) use ($adminEmails) {
            $user->is_admin_from_config = in_array($user->email, $adminEmails);
            return $user;
        });

        return view('livewire.admin.users.user-list', [
            'users' => $users,
        ])->layout('layouts.app'); // Assuming you want to use the main app layout for admin pages
    }
}
