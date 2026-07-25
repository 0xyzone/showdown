<x-mail::message>
# Match Fixture Scheduled

Dear Team Leader,

A new match fixture has been scheduled for your team in **{{ $series->stage?->tournament?->name }}**.

### Fixture Details:
- **Match:** {{ $series->teamA?->name }} vs {{ $series->teamB?->name }}
- **Format:** Best of {{ $series->best_of }}
- **Scheduled Time:** {{ $series->scheduled_at?->format('M d, Y H:i T') ?? 'TBA' }}

@if($lobbyDetails)
### Lobby / Room Credentials:
{{ $lobbyDetails }}
@endif

Please ensure all team members join the match lobby 15 minutes prior to start time.

Thanks,<br>
**Outlaw Showdown 2026 Tournament Operations**
</x-mail::message>
