<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Pesan;
use DB;
use illuminate\Support\Facades\Auth;
// use illuminate\Support\Facades\DB;
use Pusher\Pusher;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //select all users except logged in user
        // $users = User::where('id', '!=', Auth::id())->get();
        $users = DB::select("select users.id, users.name, users.avatar, users.email, count(is_read) as unread
        from users LEFT JOIN pesans ON users.id = pesans.from and is_read = 0 and pesans.to = " . Auth::id() . " 
        where users.id != ". Auth::id() . " 
        group by users.id, users.name, users.avatar, .users.email");
        return view('home', ['users' => $users]);
    }

    public function getPesan($user_id)
    {
        // return $user_id;
        $my_id = Auth::id();
        Pesan::where(['from' => $user_id, 'to' =>$my_id])->update(['is_read' => 1]);
        //getting all message for selected user
        //getting those message which id from = Auth::id() and to = user_id OR from = user_id and to = Auth::id();
        $pesan = Pesan::where(function ($query) use ($user_id, $my_id) {
            $query->where('from', $my_id)->where('to', $user_id);
        })->orWhere(function ($query) use ($user_id, $my_id){
            $query->where('from', $user_id)->where('to', $my_id);
        })->get();

        return view('pesan.index', ['pesan' => $pesan]);
    }

    public function sendPesan (Request $request)
    {
        $from = Auth::id();
        $to = $request->receiver_id;
        $pesan = $request->pesan;

        $data = new Pesan();
        $data->from = $from;
        $data->to = $to;
        $data->pesan = $pesan;
        $data->is_read = 0; //message will be unread when sending message
        $data->save();

        //pusher
        $options = array(
            'cluster' => 'ap1',
            'useTLS' => true
        );

        $pusher = new Pusher(
            env ('PUSHER_APP_KEY'),
            env ('PUSHER_APP_SECRET'),
            env ('PUSHER_APP_ID'),
            $options
        );

        $data = ['from' => $from, 'to' => $to];
        $pusher->trigger('my-channel', 'my-event', $data);
    }
}
