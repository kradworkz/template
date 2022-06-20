<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Jobs\EmailNewAccount;

class UserController extends Controller
{
    public function index(Request $request)
    {   
        $data = UserResource::collection(
            User::query()
            ->with('profile')
            ->when($request->keyword, function ($query, $keyword) {
                $query->whereHas('profile',function ($query) use ($keyword) {
                    $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', "%{$keyword}%")
                    ->orWhere(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', "%{$keyword}%");
                })->orWhere(function ($query) use ($keyword) {
                    $query->where('email', 'LIKE', "%{$keyword}%");
                });
            })
            ->whereNotIn('role',['Scholar','Super Administrator'])
            ->orderBy('created_at','DESC')
            ->paginate($request->count)
            ->withQueryString()
        );

        if($request->search){
            return $data;
        }else{
            return inertia('Users/Index');
        }
        
    }

    public function store(UserRequest $request)
    { 
        $data = \DB::transaction(function () use ($request){
            return $data = User::new($request->all());
        });

        return back()->with([
            'message' => 'User created successfully. Thanks',
            'data' => new UserResource($data),
            'type' => 'bxs-check-circle'
        ]); 
    }

    public function update(UserRequest $request)
    {   
        $data = \DB::transaction(function () use ($request){
            if($request->editable === 'verify'){
                if(EmailNewAccount::dispatch($request->id)->delay(now()->addSeconds(10))){
                    return [
                        'data' => '',
                        'message' => 'User verification successfully send. Thanks',
                        'type' => 'bx-mail-send'
                    ];
                }
            }else{
                $data = User::findOrFail($request->id);
                $data->update($request->except('img','editable'));
                $profile = UserProfile::where('user_id',$request->id)->first();
                $profile->update($request->except('email','role','is_active','img','editable'));
                ($request->img != '') ? $data = $data->image($request->all()) : '';
                $data = User::findOrFail($request->id);
                return [
                    'data' => $data,
                    'message' => 'User updated successfully. Thanks',
                    'type' => 'bxs-check-circle'
                ];
            }
        });
        
        if($request->editable){
            return back()->with([
                'message' => $data['message'],
                'data' => ($data['data'] != '') ? new UserResource($data['data']) : '',
                'type' => $data['type']
            ]);
        }else{
            return new UserResource($data['data']);
        }
    }

}
