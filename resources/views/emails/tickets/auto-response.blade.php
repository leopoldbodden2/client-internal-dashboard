{{-- Body --}}
@component('mail::message')
@include('tickets.ticket_info')
{{-- Button --}}
@component('mail::button', ['url' => route('ticket.show', $ticket->ticket_id)])
View Ticket
@endcomponent
@endcomponent
