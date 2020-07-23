{{-- Body --}}
@component('mail::message')
<p>Hello {{ ucfirst($user->name) }},</p>
<p>A social media calendar is ready for your review.</p>
<p>You can view the calendar here {{ route('social-calendar.show', $socialCalendar->id) }}</p>
{{-- Button --}}
@component('mail::button', ['url' => route('social-calendar.show', $socialCalendar->id)])
View Calendar
@endcomponent
@endcomponent
