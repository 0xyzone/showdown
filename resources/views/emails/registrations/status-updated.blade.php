<x-mail::message>
# Tournament Registration Status Update

Dear {{ $registration->registeredBy?->name ?? 'Participant' }},

The registration status for your team **{{ $registration->team?->name }}** in **{{ $registration->tournament?->name }}** has been updated to:

<x-mail::button :url="url('/mukhyadwar')">
Status: {{ strtoupper($registration->status) }}
</x-mail::button>

@if($reasonNotes)
### Note from Tournament Administration:
{{ $reasonNotes }}
@endif

Log in to **Mukhyadwar** to view your tournament match schedule and bracket placement.

Thanks,<br>
**Outlaw Showdown 2026 Tournament Operations**
</x-mail::message>
