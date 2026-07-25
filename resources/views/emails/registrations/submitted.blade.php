<x-mail::message>
# Tournament Registration Received

Dear {{ $registration->registeredBy?->name ?? 'Participant' }},

Your team **{{ $registration->team?->name }}** has successfully registered for **{{ $registration->tournament?->name }}** ({{ $registration->tournament?->season_version }}).

### Registration Summary:
- **Team Name:** {{ $registration->team?->name }} ({{ $registration->team?->tag }})
- **Tournament:** {{ $registration->tournament?->name }}
- **Status:** Pending Admin Verification

Our tournament ops team is verifying your team roster and payment receipt. You will receive an update once verified.

Thanks,<br>
**Outlaw Showdown 2026 Tournament Operations**
</x-mail::message>
