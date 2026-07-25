<x-mail::message>
# Update Regarding Your Sponsorship Inquiry

Dear {{ $query->name }},

We are following up regarding your sponsorship inquiry for **Outlaw Showdown 2026 Vol-I** on behalf of **{{ $query->company_name }}**.

@if($customMessage)
### Message from Tournament Management:
{{ $customMessage }}
@else
Our tournament organizing committee has acknowledged your proposal and would like to proceed with further discussions regarding sponsorship packages and brand placement.
@endif

Please feel free to reply directly to this email if you have any questions or additional details to share.

Thanks,<br>
**Outlaw Showdown 2026 Organizing Team**
</x-mail::message>
