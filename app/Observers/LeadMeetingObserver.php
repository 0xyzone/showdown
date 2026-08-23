<?php

namespace App\Observers;

use App\Models\LeadMeeting;
use App\Services\GoogleCalendarService;

class LeadMeetingObserver
{
    public function __construct(
        protected GoogleCalendarService $calendarService,
    ) {}

    /**
     * Handle the LeadMeeting "created" event.
     */
    public function created(LeadMeeting $leadMeeting): void
    {
        $this->calendarService->syncMeeting($leadMeeting);
    }

    /**
     * Handle the LeadMeeting "updated" event.
     */
    public function updated(LeadMeeting $leadMeeting): void
    {
        $this->calendarService->syncMeeting($leadMeeting);
    }

    /**
     * Handle the LeadMeeting "deleted" event.
     */
    public function deleted(LeadMeeting $leadMeeting): void
    {
        $this->calendarService->deleteMeeting($leadMeeting);
    }
}
