<x-mail::message>
# Assignment Status Update

Hello {{ $recipientName }},

{{ $emailMessage }}

**Service Request Title:** {{ $serviceRequest->title }}
@if($assignment->volunteer)
**Assigned Volunteer:** {{ $assignment->volunteer->name }}
@endif
**Current Status:** {{ Str::title(str_replace('_', ' ', $assignment->status)) }}

You can view the full details of the service request by clicking the button below:

<x-mail::button :url="$requestUrl">
View Service Request
</x-mail::button>

Regards,<br>
{{ config('app.name') }}
</x-mail::message>
