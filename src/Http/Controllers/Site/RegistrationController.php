<?php
namespace Avl\AdminZakup\Controllers\Site;

use App\Http\Controllers\Site\BaseController;
use App\Models\User;
use Avl\AdminZakup\Models\Contractor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Api;
use Mail;

class RegistrationController extends BaseController
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!empty($user) && !empty($user->contractor)) {
            return redirect("/");
        }

        return view(
            "adminzakup::site.zakup.registration"
        );
    }

    public function registration(Request $request)
    {
        $user = Auth::user();

        if (empty($user)) {
            $response = Api::request('POST', 'api/user', [
                "surname" => $request->input("surname"),
                "name" => $request->input("name"),
                "patronymic" => $request->input("patronymic"),
                "dob" => Carbon::parse($request->input("dob"))->format("Y-m-d"),
                "mobile" => preg_replace('~[^0-9]+~', '', $request->input('mobile')),
                "login" => $request->input("login"),
                "email" => $request->input("email"),
                "password" => $request->input("password"),
                "verify" => bcrypt(date('Ymdhis') . rand(1000000, 9999999))
            ]);

            if (isset($response->id)) {
                Mail::send('registrations::emails.registrations', ['response' => $response, 'section' => $this->section], function ($message) use ($response) {
                    $message->from(config('registrations.emailFrom'), config('registrations.emailFromName'));
                    $message->subject('Регистрация - Национальный Банк Республики Казахстан');
                    $message->to($response->email);
                });

                $user = User::query()->findOrFail($response->id);
            } else {
                return redirect()->back()->withInput()->with('errorRegistration', true);
            }
        }

        $response = Api::request('POST', 'api/contractor', [
            "name" => $request->input("contractor_name"),
            "contact_name" => $request->input("contractor_contact_name"),
            "phone" => $request->input("contractor_phone"),
            "bin" => $request->input("contractor_bin"),
            "user_id" => $user->id
        ]);


        if (isset($response->id)) {
            return redirect()->back()->with('successRegistration', true);
        }

        return redirect()->back()->withInput()->with('errorRegistration', true);
    }
}