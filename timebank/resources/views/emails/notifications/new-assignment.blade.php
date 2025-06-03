<x-mail::message>
# New Service Request Assignment

Hello {{ $volunteerName }},

You have been matched with a new service request:

**Title:** {{ $requestTitle }}

**Description:**
{{ Str::limit($requestDescription, 200) }}

You can view the full details of the service request and accept or decline the assignment by clicking the button below:

<x-mail::button :url="$requestUrl">
View Service Request
</x-mail::button>

Thank you for your willingness to help!

Regards,<br>
{{ config('app.name') }}
</x-mail::message>
