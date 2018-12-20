<?php

namespace App\Http\Controllers\GoogleApi;

use App\Http\Controllers\Controller;
use App\Models\GoogleDrafts;
use App\Models\GoogleMessages;
use App\Models\LogUsersActions;
use App\Models\PipedriveDeals;
use App\Models\UsersGoogleInfo;
use App\User;
use Google_Client;
use Google_Exception;
use Google_Service_Calendar;
use Google_Service_Gmail;
use Google_Service_Gmail_WatchRequest;
use Google_Service_Oauth2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


/**
 * Class GoogleController
 *
 * Auth config file location: config/google-auth-config.json (contains Client ID and Client secret)
 */
class GoogleController extends Controller
{
    protected $configFilePath = null;
    protected $redirectUri = null;
    protected $client = null;

    const ACCESS_TYPE_OFFLINE = 'offline';
    const AUTHORIZATION_URI = 'https://accounts.google.com/o/oauth2/v2/auth';
    const APPROVAL_PROMPT = 'force';

    const GOOGLE_MAIL_SCOPE = 'https://mail.google.com';
    const GOOGLE_AUTH_MODIFY_SCOPE = 'https://www.googleapis.com/auth/gmail.modify';
    const GOOGLE_AUTH__SCOPE = 'https://www.googleapis.com/auth/gmail.modify';
    const GOOGLE_CALENDAR_SCOPE = 'https://www.googleapis.com/auth/calendar';
    const GOOGLE_PROFILE = Google_Service_Oauth2::USERINFO_EMAIL;
    const CALENDAR = 'calendar';
    const MAIL = 'mail';

    public function __construct()
    {
        parent::__construct();

        $this->configFilePath = config_path('google-auth-config.json');
        $this->redirectUri = route('google.auth-response.process');
        $this->client = new Google_Client();
        try {
            $this->client->setAuthConfig($this->configFilePath);
            $this->client->setRedirectUri($this->redirectUri);
            $this->client->setAccessType(self::ACCESS_TYPE_OFFLINE);

            /* this setting needs for receiving refresh_token */
            $this->client->setApprovalPrompt(self::APPROVAL_PROMPT);

        } catch (Google_Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'code' => $e->getCode()]);
        }
        return response()->json(['status' => 'success', 'message' => 'Google client settings were successfully configured.', 'code' => 200]);
    }

    public function index()
    {
        $userGoogleAccount = Auth::user()->google;
        return view('google.index', compact('userGoogleAccount'));
    }

    public function signIn($scope)
    {
        switch ($scope) {
            case self::MAIL:
                $this->client->setScopes([self::GOOGLE_MAIL_SCOPE, self::GOOGLE_PROFILE]);
                break;
            case self::CALENDAR:
                $this->client->setScopes([self::GOOGLE_CALENDAR_SCOPE, self::GOOGLE_PROFILE]);
                break;
            default:
                $this->client->setScopes([self::GOOGLE_MAIL_SCOPE, self::GOOGLE_PROFILE]);
        }
        Session::put('scope_' . Auth::user()->id, $scope);
        $authUrl = $this->client->createAuthUrl();
        return redirect($authUrl);
    }

    public function processAuthResponse(Request $request)
    {
        if ($code = $request->get('code')) {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error']))
                return redirect()->route('google')->with('alert', json_encode(['message' => $token['error_description'], 'status' => 'danger']));

            $oauth2 = new Google_Service_Oauth2($this->client);
            $syncEmail = $oauth2->userinfo->get()->getEmail();
            $scope = Session::get('scope_' . Auth::user()->id);
            UsersGoogleInfo::createOrUpdateInfo($syncEmail, $token, $this->client);

            if ($scope == self::MAIL) {
                GoogleMessages::saveMessages($syncEmail, $this->client);
                $this->watch($syncEmail);
            }
            LogUsersActions::saveAction('User with ID ' . Auth::user()->id . ' has successfully synchronized ' . $scope . ' with google account ' . $syncEmail . '.');
            return redirect()->route('google')->with('alert', json_encode([
                'message' => 'You have successfully synchronized with Google '
                    . ucfirst($scope) . '.',
                'status'  => 'success',
            ]));
        }
        if ($error = $request->get('error')) {
            return redirect()->route('google')->with('alert', json_encode(['message' => 'Your sync was cancelled.', 'status' => 'danger']));
        }
        return redirect()->route('google');
    }

    protected function refreshTokenWhenExpired($email)
    {
        $token = UsersGoogleInfo::getToken($email);

        if (!$token)
            return false;

        /* Authorise with tokens from db */
        try {
            $this->client->setScopes([ self::GOOGLE_MAIL_SCOPE, self::GOOGLE_PROFILE, self::GOOGLE_CALENDAR_SCOPE]);
            $this->client->setAccessToken($token->token);

            if ($this->client->isAccessTokenExpired()) {
                /* if access_token has expired => get new one and save to db */

                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                UsersGoogleInfo::createOrUpdateInfo($email, $this->client->getAccessToken(), $this->client);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function watch($email)
    {
        try{
            if ($email) {
                $refreshTokenResult = $this->refreshTokenWhenExpired($email);

                if (!$refreshTokenResult)
                    return response()->json(['status' => 'error', 'message' => 'Refresh token for email ' . $email . ' failed. ']);
            }
            $this->client->setScopes([ self::GOOGLE_MAIL_SCOPE]);
            $watchRequest = new Google_Service_Gmail_WatchRequest();
            $watchRequest->setLabelIds(["INBOX"]);
            $watchRequest->setTopicName("projects/csapp-212712/topics/mail-csapp");

            $googleMailService = new Google_Service_Gmail($this->client);
            $result = $googleMailService->users->watch('me', $watchRequest);

            /* Successful result must have expiration and historyId */
            if ($result) {
                LogUsersActions::saveAction('Email ' . $email . ' started watch on Cloud Pub/Sub .');
                UsersGoogleInfo::changeWatchStatus($email, UsersGoogleInfo::SYNC_STATUS);
                return redirect()->back();
            } else {
                $response = ['status' => 'error', 'message' => 'Google watch for email ' . $email . ' failed. '];
                return response()->json($response);
            }
        } catch(\Exception $e) {
            return false;
        }
    }

    public function stopWatch($email)
    {
        try {
            $refreshTokenResult = $this->refreshTokenWhenExpired($email);

            if (!$refreshTokenResult)
                return response()->json(['status' => 'error', 'message' => 'Refresh token for email ' . $email . ' failed. ']);

            $this->client->setScopes([ self::GOOGLE_MAIL_SCOPE]);

            $googleMailService = new Google_Service_Gmail($this->client);
            $googleMailService->users->stop('me');
            UsersGoogleInfo::changeWatchStatus($email, UsersGoogleInfo::NOT_SYNC_STATUS, true);
            LogUsersActions::saveAction('Email ' . $email . ' stopped watch on Cloud Pub/Sub .');

            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }

    public function clearUserData($userId = null)
    {
        if (!$userId)
            $userId = Auth::user()->id;

        if(Auth::user()->google) {
            $this->stopWatch(Auth::user()->google->sync_email);
        }
        UsersGoogleInfo::where('user_id', $userId)->delete();
        GoogleDrafts::where('user_id', $userId)->delete();
        GoogleMessages::where('user_id', $userId)->delete();

        $alert = json_encode([
            'message' => 'Your Google data has been successfully cleared.',
            'status'  => 'success',
        ]);
        return redirect()->route('google')->with('alert', $alert);
    }
}
