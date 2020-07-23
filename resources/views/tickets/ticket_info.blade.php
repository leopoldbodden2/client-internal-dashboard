<p>Thank you {{ ucfirst($user->name) }} for contacting our support team. A support ticket has been opened for you. You will be notified when a response is made by email. The details of your ticket are shown below:</p>
<p>Title: {{ $ticket->title }}</p>
<p>Category: {{ $ticket->category->name }}</p>
<p>Priority: {{ $ticket->priority }}</p>
<p>Status: {{ $ticket->status }}</p>
<p>You can view the ticket at any time at {{ route('ticket.show', $ticket->ticket_id) }}</p>
