<?php

namespace App\Http\Controllers\GoogleApi;

use App\Helpers\GoogleApiHelper;
use App\Http\Requests\EmailSendRequest;
use App\Models\GoogleDrafts;
use App\Models\GoogleMessages;
use App\Models\LogUsersActions;
use App\Models\PipedriveDeals;
use App\Models\UsersGoogleInfo;
use Google_Service_Gmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class GoogleMailController extends GoogleController
{
    private $googleMailService = null;

    public function __construct()
    {
        parent::__construct();
        $this->googleMailService = new Google_Service_Gmail($this->client);
    }

    public function inbox(Request $request)
    {
        if (!UsersGoogleInfo::checkEmailSync())
            return redirect()->route('google');
        $syncEmail = Auth::user()->google->sync_email;

        $this->refreshTokenWhenExpired($syncEmail);
        $withAttachments = $request->get('withAttachments');
        $unread = $request->get('unread');
        $messages = GoogleMessages::getInboxFilteredMessages($syncEmail, $withAttachments, $unread);
        $messageType = GoogleMessages::MESSAGE_INBOX;
        if ($request->ajax()) {
            $view = view('google.messages-content', compact('syncEmail', 'messages', 'messageType', 'withAttachments', 'unread'))->render();
            return response()->json(['messages' => $view]);
        } else {
            return view('google.inbox', compact('syncEmail', 'messages', 'messageType'));
        }
    }

    public function sent(Request $request)
    {
        if (!UsersGoogleInfo::checkEmailSync())
            return redirect()->route('google');

        $syncEmail = Auth::user()->google->sync_email;
        $messages = GoogleMessages::getEmailSentMessages($syncEmail);
        $messageType = GoogleMessages::MESSAGE_SENT;
        if ($request->ajax()) {
            $view = view('google.messages-content', compact('syncEmail', 'messages', 'messageType'))->render();
            return response()->json(['messages' => $view]);
        } else {
            return view('google.inbox', compact('syncEmail', 'messages', 'messageType'));
        }
    }

    public function drafts(Request $request)
    {
        if (!UsersGoogleInfo::checkEmailSync())
            return redirect()->route('google');
        $authUser = Auth::user();

        $drafts = GoogleDrafts::getUserDrafts($authUser->id);
        if ($request->ajax()) {
            $view = view('google.drafts-content', compact('drafts'))->render();
            return response()->json(['drafts' => $view]);
        } else {
            return view('google.drafts', compact('authUser', 'drafts'));
        }
    }

    public function saveDraft(Request $request)
    {
        if(!Auth::user())
            return response(null, Response::HTTP_UNAUTHORIZED);

        $draftId = $request->get('draftId');
        $to = $request->get('to');
        $subject = $request->get('subject');
        $message = $request->get('message');
        $withMessage = $request->get('withMessage');

        if (!$to && !$subject && !$message)
            return response()->json(['status' => 'success', 'message' => 'Empty data.']);

        $userId = Auth::user()->id;
        $draftExists = GoogleDrafts::checkIfDraftExists($to, $subject, $message, $userId);
        if (!$draftExists->isEmpty()) {
            if ($withMessage)
                return response()->json(['status' => 'danger', 'withMessage' => true, 'message' => 'A draft with such combination of receiver and subject already exists.']);
            else
                return response()->json(['status' => 'danger', 'withMessage' => false, 'message' => 'A draft with such combination of receiver and subject already exists.']);
        }

        $date = GoogleApiHelper::convertDateToUTC('now');
        $draftData = [
            'to'      => $to,
            'subject' => $subject,
            'message' => $message,
            'date'    => $date,
            'user_id' => $userId,
        ];
        GoogleDrafts::updateOrCreate(['id' => $draftId], $draftData);
        if ($draftId)
            return response()->json(['status' => 'success', 'withMessage' => true, 'message' => 'Draft was successfully updated.']);
        if ($withMessage)
            return response()->json(['status' => 'success', 'withMessage' => true, 'message' => 'Draft was successfully created.']);
        else
            return response()->json(['status' => 'success', 'withMessage' => false]);
    }

    public function writeEmail(Request $request)
    {
        if (!UsersGoogleInfo::checkEmailSync())
            return redirect()->route('google');

        $reply = $request->get('reply');

        if ($reply) {
            $replyMessageId = $request->get('replyMessageId');
            $subject = $request->get('subject');
            $to = $request->get('to');
            $dealId = $request->get('dealId');
            $threadId = $request->get('threadId');
            $messages = GoogleMessages::getThreadMessages($replyMessageId);
            return view('google.write-email-form', compact('messages', 'reply', 'subject', 'to', 'replyMessageId', 'dealId', 'threadId'));
        } else
            return view('google.write-email');
    }

    public function getDealHistory(PipedriveDeals $deal)
    {
        $messages = GoogleMessages::getDealHistory($deal->id);
        return view('google.history', compact('messages', 'deal'));
    }

    public function getMessage(Request $request, $messageId)
    {
        $messageInfo = GoogleMessages::select('sync_email')->where('message_id', $messageId)->first();
        $this->refreshTokenWhenExpired($messageInfo->sync_email);
        $message = $this->googleMailService->users_messages->get('me', $messageId);

        GoogleMessages::changeMessageStatus($messageId);

        if ($request->ajax()) {
            return response()->json(['message' => $message]);
        }
        $message = GoogleMessages::where('message_id', $messageId)->first();
        return view('google.message', compact('message', 'messageId'));
    }

    public function getDraftMessage($id)
    {
        $message = GoogleDrafts::getById($id);
        $to = $message->to;
        $draftSubject = $message->subject;
        $body = $message->message;
        return view('google.write-email', compact('id', 'to', 'draftSubject', 'body'));
    }

    public function getMessageView($messageId)
    {
        $message = GoogleMessages::where('message_id', $messageId)->first();
        if ($message)
            $messageOwnerId = $message->user_id;
        else
            $messageOwnerId = null;

        $this->refreshTokenWhenExpired($messageOwnerId);
        return response()->json(['view' => view('google.message-iframe', compact('message', 'messageId'))->render()]);
    }

    public function delete(Request $request)
    {
        $messageIds = $request->get('messageIds');
        if (!empty($messageIds)) {
            GoogleMessages::whereIn('message_id', $messageIds)->delete();
            LogUsersActions::saveAction("Agent " . Auth::user()->name . " removed google inbox messages with IDs: " . implode(', ', $messageIds));
        }
        $messageType = $request->get('messageType');
        return $messageType == GoogleMessages::MESSAGE_INBOX ? $this->inbox($request) : $this->sent($request);
    }

    public function deleteDrafts(Request $request)
    {
        $draftIds = $request->get('messageIds');
        if (!empty($draftIds)) {
            GoogleDrafts::whereIn('id', $draftIds)->delete();
            LogUsersActions::saveAction("Agent " . Auth::user()->name . " removed google draft messages with IDs: " . implode(', ', $draftIds));
        }
        $drafts = GoogleDrafts::getUserDrafts(Auth::user()->id);
        $view = view('google.drafts-content', compact('drafts'))->render();
        return response()->json(['drafts' => $view]);
    }

    public function saveNotification(Request $request)
    {
        $data = $request->all();
        GoogleApiHelper::writeGoogleLog($data);

        if (isset($data['message'])) {
            $userData = json_decode(base64_decode($data['message']['data']));

            if (!$userData) return;

            $historyId = $userData->{'historyId'};
            $email = $userData->{'emailAddress'};
            $this->parseHistory($historyId, $email);
        }
    }

    private function parseHistory($historyId, $email)
    {
        try {
            $account = UsersGoogleInfo::where('sync_email', $email)->first();

            if (!$account)
                return response()->json(['code' => 500, 'message' => "Email $email wasn't found."]);

            if($account->refresh_watch_after_stop) {/* If sync was stopped => renew sync without getting messages */
                $account->refresh_watch_after_stop = false;
                $account->save();
                return true;
            }

            $refreshTokenResult = $this->refreshTokenWhenExpired($email);

            if (!$refreshTokenResult)
                return response()->json(['code' => 500, 'message' => "Refresh token failed."]);

            $currentUserHistoryId = $account->history_id ? $account->history_id : $historyId;
            $options = ['startHistoryId' => $currentUserHistoryId, 'historyTypes' => 'messageAdded'];
            $pageToken = null;
            $histories = [];

            $historyResponse = $this->googleMailService->users_history->listUsersHistory('me', $options);

            if ($historyResponse->getHistory())
                $histories = array_merge($histories, $historyResponse->getHistory());

            /* Save most recent message from history */
            $historyCount = count($histories);

            if ($currentUserHistoryId && $historyCount > 0 && isset($histories[$historyCount - 1]) && $currentUserHistoryId < $histories[$historyCount - 1]->getId()) {

                $messages = GoogleMessages::getMessages($histories[$historyCount - 1]->getMessages(), $email, $this->googleMailService);

                GoogleMessages::insert($messages);
                LogUsersActions::saveAction("Inbox message with ID" . $messages[0]->message_id . " was successfully imported for email " . $email);
            }
            return true;
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => "History parse failed.", 'error' => $e]);
        }
    }

    public function sendEmail(EmailSendRequest $request)
    {
        if (!UsersGoogleInfo::checkEmailSync())
            return redirect()->route('google');

        try {
            $syncEmail = Auth::user()->google->sync_email;
            $this->watch($syncEmail);

            $mime = GoogleMessages::generateMessageHttpRequest($syncEmail);
            $mailMessage = new \Google_Service_Gmail_Message();
            $mailMessage->setRaw($mime);
            $mailMessage->setThreadId($request->get('threadId'));
            $dealId = $request->get('dealId');

            $messageInfo = $this->googleMailService->users_messages->send("me", $mailMessage);
            $message = $this->googleMailService->users_messages->get("me", $messageInfo->getId());
            GoogleMessages::saveSentMessage($message, $this->googleMailService, $syncEmail, $dealId);

            if ($files = $request->files->get('files')) {
                foreach ($files as $file) {
                    GoogleMessages::saveFile($file->getClientOriginalName(), file_get_contents($file), $message->getId());
                }
            }
            return response()->json(['status' => 'success', 'message' => 'Your message was successfully delivered.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'danger', 'message' => 'Your message was not successfully delivered. Something went wrong.', 'error' => $e]);
        }
    }
}
