<?php

namespace App\Http\Controllers\GoogleApi;

use App\Helpers\GoogleApiHelper;
use App\Models\Activities;
use App\Models\LogUsersActions;
use App\Models\Schedulers;
use App\Models\WorkHours;
use App\User;
use DateInterval;
use DateTime;
use DateTimeZone;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventReminders;
use Illuminate\Support\Facades\Auth;

class GoogleCalendarController extends GoogleController
{
    private $googleCalendarService = null;

    const CALENDAR_ID = 'primary';

    const EVENT_STATUS_CANCELLED = 'cancelled';
    const EVENT_STATUS_CONFIRMED = 'confirmed';
    const EVENT_STATUS_TENTATIVE = 'tentative';

    public function __construct()
    {
        parent::__construct();
        $this->googleCalendarService = new Google_Service_Calendar($this->client);
    }

    public function getEventTimesByDate(DateTime $date, User $agent): array
    {
        if (!$agent->google)
            return [];
        $syncEmail = $agent->google->sync_email;
        $this->refreshTokenWhenExpired($syncEmail);

        $dateFormatted = $date->format('Y-m-d');
        $timezonesList = WorkHours::timeZoneList();
        $offset = $timezonesList[$agent->timezone]['offset'];
        $options = ['timeMin' => $dateFormatted . "T00:00:00" . $offset, 'timeMax' => $dateFormatted . "T23:59:59" . $offset];

        $events = $this->googleCalendarService->events->listEvents('primary', $options);
        $googleEventsTimes = WorkHours::getTimesFromGoogleEvents($events, $agent);
        return is_array($googleEventsTimes) ? $googleEventsTimes : [];
    }

    public function saveEvent(array $data, $dealId, User $agent)
    {
        $account = $agent->google;
        if (!$account)
            return null;
        $syncEmail = $agent->google->sync_email;

        try {
            $this->refreshTokenWhenExpired($syncEmail);
            $event = new Google_Service_Calendar_Event();
            $this->setEventData($event, $data, $dealId);
            $result = $this->googleCalendarService->events->insert(self::CALENDAR_ID, $event);
            LogUsersActions::saveAction("Event with ID " . $result->getId() . " was successfully saved to Google Calendar.");

            return $result->getId();
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

    public function updateEvent(array $data, $dealId, User $agent)
    {
        $account = $agent->google;
        if (!$account)
            return null;
        $syncEmail = $agent->google->sync_email;

        if (!$data['eventId'])
            return false;

        try {
            $this->refreshTokenWhenExpired($syncEmail);
            $event = $this->googleCalendarService->events->get(self::CALENDAR_ID, $data['eventId']);
            $this->setEventData($event, $data, $dealId);
            $this->googleCalendarService->events->update(self::CALENDAR_ID, $event->getId(), $event);
            LogUsersActions::saveAction("Event with ID " . $data['eventId'] . " was successfully updated in Google Calendar.");
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'code' => $e->getCode()]);
        }
        return true;
    }

    public function deleteEvent($eventId, User $agent)
    {
        $account = $agent->google;
        if (!$account)
            return null;
        $syncEmail = $agent->google->sync_email;

        try {
            $this->refreshTokenWhenExpired($syncEmail);
            $this->googleCalendarService->events->delete('primary', $eventId);
            LogUsersActions::saveAction("Event with ID " . $eventId . " was successfully deleted from Google Calendar.");
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'code' => $e->getCode()]);
        }
        return true;
    }

    public function restoreEvent($eventId, $data, $dealId, User $agent)
    {
        $account = $agent->google;
        if (!$account)
            return null;
        $syncEmail = $agent->google->sync_email;

        try {
            $this->refreshTokenWhenExpired($syncEmail);
            $event = $this->googleCalendarService->events->get(self::CALENDAR_ID, $eventId);

            if ($event->getStatus() == self::EVENT_STATUS_CANCELLED) {
                $event->setStatus(self::EVENT_STATUS_CONFIRMED);
                $this->googleCalendarService->events->update(self::CALENDAR_ID, $event->getId(), $event);
            }
        } catch (\Exception $e) {
            $this->saveEvent($data, $dealId, $agent);
        }
    }

    private function setEventData(Google_Service_Calendar_Event &$event, array $data, $dealId)
    {
        if ($data['eventType'] == Schedulers::TYPE_ACTIVITY) {
            $description = "Activity\n\n" . "<a href='" . route('home',
                    ['caseId' => $dealId]) . "'>" . $data['name'] . "</a>" . "\n" . 'Type: ' . $data['type'] . "\n" . 'Note: ' . $data['note'];
        } else {
            $description = "<a href='" . route('home', ['caseId' => $dealId]) . "'>" . $data['name'] . "</a>" . "\n";
        }
        $event->setSummary($data['name']);
        $event->setDescription($description);
        $event->setAttendees(['email' => $data['email']]);

        if ($data['type'] == Activities::TYPE_CALL || $data['type'] == Activities::TYPE_GENERAL) {
            $start = $data['date'];
            $startDatetime = $start->format('Y-m-d') . 'T' . $start->format('H:i:s');

            $end = $start->add(new DateInterval('PT45M'));
            $endDatetime = $end->format('Y-m-d') . 'T' . $end->format('H:i:s');

            $event->setStart(GoogleApiHelper::getCalendarDate($startDatetime, $data['date']->getTimezone()->getName(), true));
            $event->setEnd(GoogleApiHelper::getCalendarDate($endDatetime, $data['date']->getTimezone()->getName(), true));

            $reminders = new Google_Service_Calendar_EventReminders();
            $reminders->setUseDefault("false");
            $reminders->setOverrides([
                ["method" => "email", "minutes" => 15],
                ["method" => "popup", "minutes" => 15],
            ]);
            $event->setReminders($reminders);
        } else {
            $reminders = new Google_Service_Calendar_EventReminders();
            $reminders->setUseDefault("false");
            $event->setReminders($reminders);

            $googleDate = GoogleApiHelper::getCalendarDate($data['date'], $data['date']->getTimezone()->getName());
            $event->setStart($googleDate);
            $event->setEnd($googleDate);
        }
    }
