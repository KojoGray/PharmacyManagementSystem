<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use App\Models\UserPasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderMail;
use App\Http\Requests\ResetEmailRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PassworReseteRequest;
use App\Http\Requests\UpdatePasswordRequest;
use \App\Events\Customers;
use App\Models\Notifications;
use App\Http\Requests\UserUpdateRequest;



class AuthController extends Controller
{


    protected $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'POST'
    ];



    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {

            return response()->json('invalid password or email', 201);
        }

        $user = Auth::user();
        $userToken = $user->createToken('loginToken')->plainTextToken;

        $user->online_status = "active";
        $user->save();
        //  if($user->role == "pharmacist"  ){

        //  }

        return response()->json([
            'user' => $user,
            'token' => $userToken
        ], 200, $this->headers);


    }



    public function resetPassword(ResetEmailRequest $request)
    {
        $data = $request->validated();
        $userEmail = $request["user_email"];
        $user = User::where('email', $userEmail)->first();
        if ($user === null) {
            return response()->json('invalid email', 409);
        }
        $userId = $user->id;
        $userName = $user->username;

        //generated a code send to the user
        $code = random_int(1000, 9999);
        $hashedCode = bcrypt($code);
        $this->insertCode($userId, $hashedCode);


        //send the code to the user here
        $this->sendCode($code, $userName, $userEmail);

        $userData = [
            'user_role' => $user->role,
            'user_id' => $user->id
        ];
        return response()->json($userData, 200);


    }


    private function insertCode($userId, $code)
    {
        $codeDetails = UserPasswordReset::create([
            'user_id' => $userId,
            'code' => $code
        ]);


        return $codeDetails;

    }


    private function sendCode($code, $userName, $email)
    {

        $data = [
            'code' => $code,
            'userName' => $userName,

        ];
        Mail::to($email)->send(new ReminderMail($data));

    }
    //send the code to the user here
    public function validateCode(Request $request)
    {
        $userId = $request->input('id');
        $verification_code = $request->input('code');
        $user = User::find($userId);

        // if($user->isEmpty()){
        //      return response()->json("email doesn't match any email in our database ", 409);
        // }



        $userRole = $user->role;
        $codeRow = UserPasswordReset::where('user_id', $user->id)->latest()->first();

        $hashedCode = $codeRow->code;

        if (Hash::check($verification_code, $hashedCode)) {
            $codeRow->delete();
            return response()->json("verified", 200);
        } else {
            return response()->json("invalid reset code", 409);
        }
    }


    public function changePassword(PassworReseteRequest $request, $userId)
    {
        $data = $request->validated();
        $user = User::find($userId);
        $hashedPassword = bcrypt($data["userpassword"]);


        $user->password = $hashedPassword;

        $user->save();


        return response()->json("password updated successfully", 200);





    }


    public function updatePassword(UpdatePasswordRequest $request, $userId)
    {
        $data = $request->validated();
        $oldPassword = $data["oldPassword"];
        $newPassword = $data["newPassword"];
        $user = User::find($userId);
        $userHashedPassword = $user->password;




        if (!Hash::check($oldPassword, $userHashedPassword)) {
            return response()->json("invalid password", 409);
        } else {
            $hashedNewPassword = bcrypt($newPassword);
            $user->password = $hashedNewPassword;
            $user->save();

            return response()->json("password updated successfully", 200);
        }
    }

    public function createUser()
    {
        User::create([

        ]);
    }



    public function updateUser(Request $request)
    {
        ///$data = $request->validated();
        return $request->file('avatar');

        // if ($data->hasFile('avatar')) {
        //     $path = $image = $data->file('avatar');
        //     $this->uploadImage($image);

        // }


        //     if ($request->hasFile('avatar')) {
        //         $image = $request->file('avatar');
        //         $this->uploadImage($image);
        //         $customerData->avatar = $image;
        //         $customerData->save();

        // }

    }

    private function uploadImage($image)
    {
        $path = $image->store('/App/images');

    }


    public function getUsers()
    {
        $customers = User::where("role", "customer")->get();
        $pharmacists = User::where("role", "pharmacist")->get();


        return response()->json([
            "customers" => $customers,
            "pharmacists" => $pharmacists

        ], 200, $this->headers);
    }
}
