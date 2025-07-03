<?php

namespace Plugin\Saas\Http\Controllers\User;


use Exception;
use Carbon\Carbon;
use Core\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Core\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Core\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Core\Mail\EmailPasswordResetLink;
use Core\Repositories\UserRepository;
use Illuminate\Http\RedirectResponse;
use Core\Models\AdminLoginActivityLog;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Plugin\Saas\Services\SaasNotification;
use Plugin\Saas\Repositories\SubscriptionRepository;

class UserController extends Controller
{
    public function __construct(
        protected UserRepository $user_repository,
        protected SubscriptionRepository $repository,
    ) {}

    /**
     * will redirect to registration page
     * @return mixed
     */
    public function register()
    {
        if (Auth::user()) {
            return redirect()->route('plugin.saas.user.dashboard');
        } else {
            return view('plugin/saas::user.panel.auth.register');
        }
    }

    /**
     * Will store requested user information
     */
    public function storeUserDetails(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:250',
                'email' => 'required|unique:tl_users,email|max:250',
                'password' => 'required|min:6|confirmed|max:250'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();
            $user = new User();
            $user->uid = "SUBSCRIBER-" . time();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->status = 1;
            $user->user_type = config('saas.user_type.subscriber');
            $user->save();

            //Send notification to admin
            $notification_service = new SaasNotification();
            $notification_service->subscriberRegistrationNotificationToAdmin($user->id);

            DB::commit();
            toastNotification('success', translate('Registration Successful!'));
            return redirect()->route('subscriber.login');
        } catch (Exception $ex) {
            DB::rollBack();
            toastNotification('error', translate('Registration Unsuccessful!'));
            return back();
        }
    }

    /**
     * redirect to login page
     *
     * @return mixed
     */
    public function login()
    {
        if (isset(request()->message)) {
            toastNotification('error', request()->message);
        }
        if (Auth::user()) {
            if (Auth::user()->user_type == config('saas.user_type.subscriber')) {
                return redirect()->route('plugin.saas.user.dashboard');
            } else {
                return redirect()->route('admin.dashboard');
            }
        } else {
            return view('plugin/saas::user.panel.auth.login');
        }
    }


    /**
     * attempt to Login
     *
     * @param  mixed $request
     * @return mixed
     */
    public function attemptLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $this->setupLoginLogoutActivity(true);
            if (Auth::user()->status == config('settings.user_status.in_active')) {
                $this->logout();
                toastNotification('error', translate("Your status is currently inactive"));
                return redirect()->back();
            } elseif (Auth::user()->user_type == config('saas.user_type.subscriber')) {
                toastNotification('success', translate('Login successful'));
                //return redirect()->route('plugin.saas.user.dashboard');
                $default_path = route('plugin.saas.user.dashboard');
                return Redirect::intended($default_path);
            } else {
                $this->logout();
                toastNotification('error', translate("Login Credentials Does not Match"));
                return redirect()->back();
            }
        }
        toastNotification('error', translate("Login Credentials Does not Match"));
        return redirect()->back();
    }

    /**
     * Will redirect to profile page
     */
    public function profile(): View
    {
        try {
            $id = Auth::user()->id;
            $match_case = [
                ['tl_users.id', '=', $id]
            ];
            $data = [
                'tl_users.*',
                'tl_uploaded_files.path as pro_pic',
                'tl_uploaded_files.alt as pro_pic_alt',
                'tl_uploaded_files.id as pro_pic_id'
            ];
            $user = $this->user_repository->getUserProfileInfo($data, $match_case)->first();

            return view('plugin/saas::user.panel.auth.profile', ['user' => $user]);
        } catch (Exception $ex) {
            toastNotification('error', translate('Action failed'), 'Failed');
            return redirect()->back();
        }
    }
    /**
     * update user profile
     *
     * @param  UserRequest $request
     * @return mixed
     */
    public function updateProfile(UserRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::find($request['id']);
            if (request('old_password') != null) {
                if (!Hash::check($request['old_password'], $user->password)) {
                    return back()->withErrors([
                        'old_password' => 'Incorrect password'
                    ]);
                }
            }
            $user->name  = xss_clean($request['name']);
            $user->email = xss_clean($request['email']);
            if (request('password') != null && request('password_confirmation') != null && request('old_password') != null) {
                $user->password = Hash::make($request['password']);
            }

            if ($request->hasFile('image')) {
                $image = saveFileInStorage($request['image'], false);
                $user->image = $image;
            }

            $user->update();
            DB::commit();
            toastNotification('success', translate('Profile updated successfully'));
            return redirect()->route('subscriber.profile');
        } catch (Exception $e) {
            DB::rollBack();
            toastNotification('error', translate('Unable to update user profile'));
            return redirect()->route('subscriber.profile');
        }
    }

    /**
     * Attempt logout
     *
     * @return mixed
     */
    public function logout()
    {
        $this->setupLoginLogoutActivity(false);
        Auth::logout();
        return redirect()->route('subscriber.login');
    }

    /**
     * redirect to password reset page
     *
     * @return mixed
     */
    public function passwordResetLink()
    {
        if (Auth::user()) {
            if (Auth::user()->user_type == config('saas.user_type.subscriber')) {
                return redirect()->route('plugin.saas.user.dashboard');
            } else {
                return redirect()->route('admin.dashboard');
            }
        } else {
            return view('plugin/saas::user.panel.auth.password_reset_link');
        }
    }

    /**
     * will send password reset link to user email address
     *
     * @param  mixed $request
     * @return mixed
     */
    public function emailResetPasswordLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:tl_users',
        ]);
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
                'subject' => $template->subject,
                '_reset_password_link_' => route('subscriber.reset.password', $token)
            ];

            Mail::to($request->email)->send(new EmailPasswordResetLink($data));
            toastNotification('success', translate('We have e-mailed your password reset link'));
            return back();
        } catch (Exception $ex) {
            toastNotification('success', translate('Unable to send email !'));
            return back();
        }
    }

    /**
     * reset password
     *
     * @param  mixed $token
     * @return mixed
     */
    public function resetPassword($token)
    {
        return view('plugin/saas::user.panel.auth.reset_password', ['token' => $token]);
    }

    /**
     * reset password
     *
     * @param  mixed $request
     * @return mixed
     */
    public function resetPasswordPost(Request $request)
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
                toastNotification('error', translate('Invalid token'));
                return back();
            }

            $user_details = DB::table('tl_users')->where('email', '=', $request['email'])->first();
            $user = User::find($user_details->id);
            $user->password = Hash::make($request['password']);
            $user->update();

            DB::table('password_resets')->where(['email' => $request->email])->delete();

            toastNotification('success', translate('Your password has been reset'));
            return redirect()->route('subscriber.login');
        } catch (\Exception $th) {
            toastNotification('error', translate('Unable to reset password'));
            return back();
        }
    }

    /**
     * setup login logout activity
     *
     * @param  mixed $is_for_login
     * @return mixed
     */
    public function setupLoginLogoutActivity($is_for_login)
    {
        if ($is_for_login) {
            $user_ip_address = getUserIpAddr();
            $os = get_operating_system();
            $browser = get_browser_name();
            $user_id = Auth::user()->id;
            $user_name = Auth::user()->name;

            $login_activity = new AdminLoginActivityLog();
            $login_activity->user_id = $user_id;
            $login_activity->login_at = Carbon::now()->toDateTimeString();
            $login_activity->os = $os;
            $login_activity->browser = $browser;
            $login_activity->ip = $user_ip_address;
            $login_activity->saveOrFail();

            Session::put($user_name, $login_activity->id);
        } else {
            $user_name = Auth::user()->name;
            $login_activity_id = Session::get($user_name);
            $login_activity = AdminLoginActivityLog::find($login_activity_id);
            if ($login_activity != null) {
                $login_activity->logout_at = Carbon::now()->toDateTimeString();
                $login_activity->update();
            }
        }
    }
}
