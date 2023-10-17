<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Models\Events;
use App\Models\Event_User;
use App\Models\User;

class UserController extends Controller
{
    //
    public function index()
    {
        $data_event = Events::all();
        return view('user.index', ['data_event' => $data_event]);
    }

    public function show($eventID)
    {
        $event = Events::FindOrFail($eventID);
        return view('user.detail', ['data_event' => $event]);
    }

    public function joinEvent($eventID, $userID)
    {
        $event = Events::find($eventID);

        // Memeriksa apakah pengguna sudah terdaftar di event
        if ($event->users()->where('user_id', $userID)->exists()) {
            return redirect()->back()->with('error', 'You have already joined this event!');
        }

        $currentParticipants = $event->users->count();

        if ($currentParticipants < $event->participant_limit) {
            // Izinkan pengguna untuk bergabung dengan event
            $event->users()->attach($userID);

            // Atau, Anda juga dapat menambahkan ke tabel report langsung, jika Anda memiliki informasi tambahan untuk dimasukkan
            // Report::create([
            //     'user_id' => $userID,
            //     'event_id' => $eventID,
            //     // ... kolom lain jika ada
            // ]);

            return redirect()->back()->with('message', 'Successfully joined the event!');
        } else {
            return redirect()->back()->with('error', 'Sorry, this event is full!');
        }
    }

    public function participant($userId)
    {
        try {
            $id = Crypt::decrypt($userId);
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'Invalid data provided!');
        }

        $user = User::FindOrFail($id);
        $events = $user->events;

        return view('user.participant', ['events' => $events]);
    }

    public function edit($userid)
    {
        try {
            $id = Crypt::decrypt($userid);
        } catch (DecryptException $e) {
            return redirect()->back()->with('error', 'Invalid data provided!');
        }
        $user = User::find($id);
        return view('user.detail_user', ['User' => $user]);
    }

    public function update(Request $request, $id)
    {
        try {
            $userid = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return response()->json(['error' => 'Invalid data provided!'], 400);
        }

        $user = User::find($userid);

        if (!$user) {
            return response()->json(['error' => 'User not found!'], 404);
        }

        $data = $request->only(['username', 'new-password']);

        // Update the username
        if (isset($data['username'])) {
            $user->name = $data['username'];
        }

        // Update the password if provided
        if (isset($data['new-password'])) {
            $user->password = Hash::make($data['new-password']);
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully!']);
    }
}
