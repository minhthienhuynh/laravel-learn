<?php

namespace App\Http\Livewire\LeftSidebar\Chats;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddContactModal extends Component
{
    public Collection $users;

    public function mount()
    {
        $this->users = $this->getUnconnectedUsers();
    }

    public function render()
    {
        return view('chat.partials.leftsidebar.chats.add-contact-modal');
    }

    public function selectUser(User $user)
    {
        if ($this->hasRoom($user)) {
            return;
        }

        $room = Room::create([
            'type' => Room::TYPE_USER,
        ]);

        $room->users()->sync([auth()->id(), $user->id]);

        $this->emit('userRoomStored', $room->id);
    }

    protected function getUnconnectedUsers()
    {
        return User::whereKeyNot(auth()->id())
            ->whereDoesntHave('rooms', function(Builder $query) {
                $query->whereIn('id', auth()->user()->rooms()
                    ->where('type', Room::TYPE_USER)
                    ->pluck('id'));
            })
            ->orderBy('name')
            ->select('*')
            ->addSelect(DB::raw("UPPER(LEFT(name, 1)) as upper_left_name_1"))
            ->get();
    }

    protected function hasRoom(User $user): bool
    {
        return Room::query()
            ->has('users', '=', 2)
            ->whereHas('users', function (Builder $query) {
                $query->where('id', auth()->id());
            })
            ->whereHas('users', function (Builder $query) use ($user) {
                $query->where('id', $user->id);
            })
            ->where('type', Room::TYPE_USER)
            ->exists();
    }
}
