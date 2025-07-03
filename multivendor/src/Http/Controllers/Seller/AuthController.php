<?php

namespace Plugin\Multivendor\Http\Controllers\Seller;

use Carbon\Carbon;
use Core\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Core\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Core\Mail\EmailPasswordResetLink;
use Exception;
use Illuminate\Validation\ValidationException;
use Plugin\Multivendor\Http\Requests\SellerLoginRequest;
use Plugin\Multivendor\Models\SellerShop;

class AuthController extends Controller
{


    /**
     * Will return seller login page
     *
     */
    public function login()
    {
        if (auth()->user() != null && auth()->user()->user_type == config('tlecommercecore.user_type.seller')) {
            return to_route('plugin.multivendor.seller.dashboard');
        }

        return view('plugin/multivendor::seller.auth.login');
    }
    /**
     * Seller login
     *
     * @param SellerLoginRequest $request
     */
    public function loginAttempt(SellerLoginRequest $request)
    {
        $seller_info = User::where('email', $request['email'])
            ->where('user_type', config('tlecommercecore.user_type.seller'))
            ->first();

        if ($seller_info == null) {
            throw ValidationException::withMessages(
                [
                    'login_error' => [translate('No Account found associate this email')]
                ]
            );
        }

        if ($seller_info != null && $seller_info->status != config('settings.general_status.active')) {
            throw ValidationException::withMessages(
                [
                    'login_error' => [translate('Your account is not active. Please contact with administration')]
                ]
            );
        }


        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            toastNotification('success', translate('Login successful'));
            return redirect()->route('plugin.multivendor.seller.dashboard');
        }

        throw ValidationException::withMessages(
            [
                'login_error' => [translate('Login Credentials Does not Match')]
            ]
        );
    }
    /**
     * Seller logout
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('plugin.multivendor.seller.login.page');
    }

    /**
     * Redirect to seller reset password form
     */
    public function passwordResetLinkForm()
    {
        return view('plugin/multivendor::seller.auth.reset_password_form');
    }
    /**
     * Will send password reset list to seller
     *
     * @param \Illuminate\Http\Request $request
     */
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $seller_info = User::where('email', $request['email'])
            ->where('user_type', config('tlecommercecore.user_type.seller'))
            ->first();

        if ($seller_info == null) {
            throw ValidationException::withMessages(
                [
                    'email' => [translate('No Account found associate this email')]
                ]
            );
        }
        try {
            $token = Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            $template = DB::table('tl_email_template_properties')
                ->where('email_type', config('settings.email_template.reset_user_password'))
                ->select([
                    'subject'
                ])->first();

            $data = [
                'template_id' => config('settings.email_template.reset_user_password'),
                'keywords' => getEmailTemplateVariables(config('settings.email_template.reset_user_password'), true),
                'subject' => "Password Reset",
                '_reset_password_link_' => route('plugin.multivendor.seller.password.reset', $token)
            ];

            Mail::to($request->email)->send(new EmailPasswordResetLink($data));
            toastNotification('success', translate('We have e-mailed your password reset link'));
            return back();
        } catch (Exception $ex) {
            toastNotification('error', 'Unable to send email !');
            return back();
        }
    }

    /**
     * Will redirect new password form
     */
    public function resetPassword($token)
    {
        return view('plugin/multivendor::seller.auth.reset_password', ['token' => $token]);
    }
    /**
     * Will reset seller password
     *
     * @param \Illuminate\Http\Request $request
     */
    public function storeNewPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:tl_users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        try {
            $updatePassword = DB::table('password_resets')
                ->where([
                    'email' => $request->email,
                    'token' => $request->token
                ])->first();

            if (!$updatePassword) {
                toastNotification('error', 'Invalid token');
                return back();
            }

            $user_details = DB::table('tl_users')->where('email', '=', $request['email'])->first();
            $user = User::find($user_details->id);
            $user->password = Hash::make($request['password']);
            $user->update();

            DB::table('password_resets')->where(['email' => $request->email])->delete();

            toastNotification('success', translate('Your password has been reset'));
            return redirect()->route('plugin.multivendor.seller.login.page');
        } catch (\Throwable $th) {
            toastNotification('error', translate('Unable to reset password'));
            return back();
        }
    }

    /**
     * Redirect seller profile page
     */
    public function profile()
    {
        $user = auth()->user();
        $phone = DB::table('tl_com_seller_shop')->where('seller_id', $user->id)->value('seller_phone');
        return view('plugin/multivendor::seller.auth.profile', ['user' => $user, 'phone' => $phone]);
    }

    /**
     * update user profile
     *
     * @param  UserRequest $request
     * @return mixed
     */
    public function profileUpdate(UserRequest $request)
    {
        try {
            $request->validate([
                'phone' => 'required',
            ]);
            DB::beginTransaction();
            $user = User::find($request['id']);
            if (request('old_password') != null) {
                if (!Hash::check($request['old_password'], $user->password)) {
                    return back()->withErrors([
                        'old_password' => 'Incorrect password'
                    ]);
                }
            }
            $user->name = $request['name'];
            $user->email = $request['email'];
            if (request('password') != null && request('password_confirmation') != null && request('old_password') != null) {
                $user->password = Hash::make($request['password']);
            }

            $user->image = $request['pro_pic'];
            $user->update();
            $shop = SellerShop::where('seller_id', $user->id)->first();
            $shop->seller_phone = $request['phone'];
            $shop->update();

            DB::commit();
            toastNotification('success', translate('Profile updated successfully'));
            return redirect()->route('plugin.multivendor.seller.profile.page');
        } catch (Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Unable to update user profile'));
            return redirect()->route('plugin.multivendor.seller.profile.page');
        }
    }
}
