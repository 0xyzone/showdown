<x-mail::message>
# Welcome to Showdown, {{ $user->name }}!

Your official member account has been created successfully. You can log in using your registered **Email Address**, **Username**, or **Primary Phone Number** along with the password provided below:

- **Email Address:** {{ $user->email }}
@if($user->username)
- **Username:** {{ $user->username }}
@endif
@if($user->phone)
- **Phone Number:** {{ $user->phone }}
@endif
- **Auto-generated Password:** `{{ $plainPassword }}`

<x-mail::button :url="config('app.url') . '/maidan/login'">
Log In to Admin Panel
</x-mail::button>

> **Important Security Notice:**
> For security reasons, you will be redirected to set a new password immediately upon your first login before any other part of the admin panel can be accessed.

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
