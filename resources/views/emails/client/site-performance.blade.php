{{-- Body --}}
@component('mail::message')
<h1 style="text-align: center; color: #57A0BE; margin-top: 10px;"><strong>{{ $user->client?$user->client->name:$user->display_name }}</strong></h1>
<h4><strong>{{ $user->site_stats['visits'] }}</strong> people visited your website</h4>
{{-- Table --}}
@component('mail::table')
|   |   |   |
|:-------------:|:-------------:|:-------------:|
| <h1>{{ $user->site_stats['googlesources'] }}</h1>found you on google | {!! $user->phone_stats['total']>0?"<h1>".$user->phone_stats['total']."</h1>called you":"" !!} | {!! $user->site_stats['emails']>0?"<h1>".$user->site_stats['emails']."</h1>emailed you":"" !!} |
@endcomponent

{{-- Panel --}}
@component('mail::panel')
    @if(count($user->site_stats['locations']))
        <h2>Top Visitor Locations</h2>
* {{ $user->site_stats['locations']->implode("name","\n* ") }}
    @endif
@endcomponent

{{-- Button --}}
@component('mail::button', ['url' => url('/')])
See Full Report
@endcomponent

@endcomponent
