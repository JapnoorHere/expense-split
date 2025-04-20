<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    /**
     * Display a listing of the user's friends.
     */
    public function index()
    {
        $user = User::user();
        $friends = User::friends();
        $pendingRequests = $user->pendingReceivedFriendRequests()->with('user')->get();
        $sentRequests = $user->pendingSentFriendRequests()->with('friend')->get();
        
        return view('friends.index', compact('friends', 'pendingRequests', 'sentRequests'));
    }
    
    /**
     * Show form to search for friends
     */
    public function search()
    {
        return view('friends.search');
    }
    
    /**
     * Search for users by email
     */
    public function searchResults(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $email = $request->input('email');
        $currentUser = User::user();
        
        // Find user by email, excluding current user
        $user = User::where('email', $email)
            ->where('id', '!=', $currentUser->id)
            ->first();
            
        $friendshipStatus = null;
        
        if ($user) {
            if ($currentUser->isFriendsWith($user)) {
                $friendshipStatus = 'friends';
            } elseif ($currentUser->hasPendingFriendRequestTo($user)) {
                $friendshipStatus = 'pending_sent';
            } elseif ($currentUser->hasPendingFriendRequestFrom($user)) {
                $friendshipStatus = 'pending_received';
            } else {
                $friendshipStatus = 'none';
            }
        }
        
        return view('friends.search', compact('user', 'friendshipStatus'));
    }
    
    /**
     * Send a friend request to another user
     */
    public function sendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);
        
        $userId = Auth::id();
        $friendId = $request->input('friend_id');
        
        // Make sure users aren't already friends
        $user = User::user();
        $friend = User::findOrFail($friendId);
        
        if ($userId == $friendId) {
            return back()->with('error', 'You cannot send a friend request to yourself.');
        }
        
        if ($user->isFriendsWith($friend)) {
            return back()->with('error', 'You are already friends with this user.');
        }
        
        if ($user->hasPendingFriendRequestTo($friend)) {
            return back()->with('error', 'You have already sent a friend request to this user.');
        }
        
        if ($user->hasPendingFriendRequestFrom($friend)) {
            return back()->with('error', 'This user has already sent you a friend request. Check your pending requests.');
        }
        
        // Create the friendship
        Friendship::create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => 'pending'
        ]);
        
        return back()->with('success', 'Friend request sent successfully.');
    }
    
    /**
     * Accept a friend request
     */
    public function acceptRequest(Request $request)
    {
        $request->validate([
            'friendship_id' => 'required|exists:friendships,id',
        ]);
        
        $friendshipId = $request->input('friendship_id');
        $userId = Auth::id();
        
        $friendship = Friendship::findOrFail($friendshipId);
        
        // Make sure the current user is the recipient of the request
        if ($friendship->friend_id != $userId) {
            return back()->with('error', 'You are not authorized to accept this friend request.');
        }
        
        // Update the friendship status
        $friendship->status = 'accepted';
        $friendship->save();
        
        return back()->with('success', 'Friend request accepted.');
    }
    
    /**
     * Reject a friend request
     */
    public function rejectRequest(Request $request)
    {
        $request->validate([
            'friendship_id' => 'required|exists:friendships,id',
        ]);
        
        $friendshipId = $request->input('friendship_id');
        $userId = Auth::id();
        
        $friendship = Friendship::findOrFail($friendshipId);
        
        // Make sure the current user is the recipient of the request
        if ($friendship->friend_id != $userId) {
            return back()->with('error', 'You are not authorized to reject this friend request.');
        }
        
        // Update the friendship status
        $friendship->status = 'rejected';
        $friendship->save();
        
        return back()->with('success', 'Friend request rejected.');
    }
    
    /**
     * Remove a friend (cancel friendship)
     */
    public function removeFriend(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);
        
        $userId = Auth::id();
        $friendId = $request->input('friend_id');
        
        // Delete the friendship in both directions
        Friendship::where(function($query) use ($userId, $friendId) {
                $query->where('user_id', $userId)
                      ->where('friend_id', $friendId);
            })
            ->orWhere(function($query) use ($userId, $friendId) {
                $query->where('user_id', $friendId)
                      ->where('friend_id', $userId);
            })
            ->delete();
            
        return back()->with('success', 'Friend removed successfully.');
    }
    
    /**
     * Cancel a sent friend request
     */
    public function cancelRequest(Request $request)
    {
        $request->validate([
            'friendship_id' => 'required|exists:friendships,id',
        ]);
        
        $friendshipId = $request->input('friendship_id');
        $userId = Auth::id();
        
        $friendship = Friendship::findOrFail($friendshipId);
        
        // Make sure the current user is the sender of the request
        if ($friendship->user_id != $userId) {
            return back()->with('error', 'You are not authorized to cancel this friend request.');
        }
        
        // Delete the friendship
        $friendship->delete();
        
        return back()->with('success', 'Friend request cancelled.');
    }
}
