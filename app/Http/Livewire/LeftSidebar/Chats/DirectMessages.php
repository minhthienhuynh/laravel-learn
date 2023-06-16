<?php

namespace App\Http\Livewire\LeftSidebar\Chats;

use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class DirectMessages extends Component
{
    public Collection|array $rooms;

    protected $listeners = [
        'userRoomStored' => 'refreshRooms',
        'favoriteUpdated' => 'refreshRooms2',
        'messageReceived' => 'refreshRooms2',
        'needRerender' => 'refreshRooms2',
    ];

    public function mount()
    {
        $this->rooms = $this->getRooms();
    }

    public function render()
    {
        return view('chat.partials.leftsidebar.chats.direct-messages');
    }

    public function refreshRooms(Room $room)
    {
        $this->rooms->push($room);
    }

    public function refreshRooms2()
    {
        $this->rooms = $this->getRooms();
    }

    protected function getRooms(): Collection|array
    {
        return Room::query()
            ->whereHas('users', function (Builder $query) {
                $query->where('id', auth()->id());
            })
            ->whereIn('id', auth()->user()->options['room-favorites'], 'and', true)
            ->ofType(Room::TYPE_USER)
            ->with('other_users')
            ->get();
    }
}
