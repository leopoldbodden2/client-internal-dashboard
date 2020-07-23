@auth
    @if(auth()->user()->analytics)
        <li class="nav-item">
            <a class="nav-link {{ request()->route()->getName()==='visitors'?'active':'' }}"
               href="{{ route('visitors') }}">
                <span class="far fa-fw fa-users"></span>
                @if(request()->has('iframe'))
                    <br>
                @endif
                Visitors
            </a>
        </li>
    @endif
    @if(auth()->user()->hasCtm())
        <li class="nav-item">
            <a class="nav-link {{ request()->route()->getName()==='phone-calls'?'active':'' }}"
               href="{{ route('phone-calls') }}">
                <span class="far fa-fw fa-phone"></span>
                @if(request()->has('iframe'))
                    <br>
                @endif
                Phone Calls
            </a>
        </li>
    @endif
    {{-- @if(auth()->user()->hasMailgun())
        <li class="nav-item">
            <a class="nav-link {{ request()->route()->getName()==='email-contacts'?'active':'' }}"
               href="{{ route('email-contacts') }}">
                <span class="far fa-fw fa-at"></span>
                @if(request()->has('iframe'))
                    <br>
                @endif
                Email Contacts
            </a>
        </li>
    @endif --}}
    @if(auth()->user()->analytics)
        {{-- <li class="nav-item">
            <a class="nav-link {{ request()->route()->getName()==='text-messages'?'active':'' }}" href="{{ route('text-messages') }}">
                <span class="far fa-fw fa-mobile"></span>
                @if(session()->has('iframe'
                <br>
               @endif
                Text Messages
            </a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->route()->getName()==='referrers'?'active':'' }}"
               href="{{ route('referrers') }}">
                <span class="far fa-fw fa-arrow-right"></span>
                @if(request()->has('iframe'))
                    <br>
                @endif
                Referrers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->route()->getName()==='visitor-location'?'active':'' }}"
               href="{{ route('visitor-location') }}">
                <span class="far fa-fw fa-map-marker"></span>
                @if(request()->has('iframe'))
                    <br>
                @endif
                Visitor Location
            </a>
        </li>
    @endif
    {{-- <li class="nav-item">
        <a class="nav-link {{ request()->route()->getName()==='reviews'?'active':'' }}" href="{{ route('reviews') }}">
            <span class="far fa-fw fa-comment"></span>
            @if(session()->has('iframe'
            <br>
           @endif
            Reviews
        </a>
    </li> --}}

    {{-- @if(auth()->user()->client ?? auth()->user()->client->main_url)
        <li class="nav-item">
            <a class="nav-link" href="{{ auth()->user()->client->main_url }}" target="_blank" rel="nofollow noreferrer">
                <span class="far fa-fw fa-browser"></span>
                @if(session()->has('iframe'
                <br>
               @endif
                My Website
            </a>
        </li>
    @endif

    @if(auth()->user()->client ?? auth()->user()->client->main_url)
        <li class="nav-item">
            <a class="nav-link" href="{{ auth()->user()->client->main_url }}wp-admin" target="_blank" rel="nofollow noreferrer">
                <span class="far fa-fw fa-browser"></span>
                @if(session()->has('iframe'
                <br>
               @endif
                Website Admin
            </a>
        </li>
    @endif
    <li class="nav-item">
        <a class="nav-link {{ request()->route()->getName()==='ticket.index'?'active':'' }}"
           href="{{ route('ticket.index') }}">
            <span class="fa fa-btn fa-ticket"></span>
            @if(request()->has('iframe'))
                <br>
            @endif
            Tickets
        </a>
    </li>--}}

@else
    <li class="nav-item">
        <a class="nav-link" href="{{ url('login') }}">
            <span class="far fa-fw fa-sign-in-alt"></span>
            @if(request()->has('iframe'))
                <br>
            @endif
            Login
        </a>
    </li>
    {{-- <li class="nav-item">
    <a class="nav-link" href="{{ url('register') }}">
    <span class="far fa-fw fa-user-plus"></span>
    @if(session()->has('iframe'
    <br>
    @endif
    Register
    </a>
    </li> --}}
@endif
@auth
    @if(auth()->user()->admin)
        <li class="nav-item">
            <a class="nav-link {{ request()->route()->getName()==='social-calendar.index'?'active':'' }}"
               href="{{ route('social-calendar.index') }}">
                <span class="fa fa-btn fa-calendar-check"></span>
                @if(request()->has('iframe'))
                    <br>
                @endif
                Social{{request()->has('iframe')?' ':''}}Media Calendar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->route()->getName()==='users.index'?'active':'' }}"
               href="{{ route('users.index') }}">
                <span class="fa fa-user"></span>
                @if(request()->has('iframe'))
                    <br>
                @endif
                Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->route()->getName()==='clients'?'active':'' }}"
               href="{{ route('clients') }}">
                <span class="fa fa-barcode"></span>
                @if(request()->has('iframe'))
                    <br>
                @endif
                Api Clients
            </a>
        </li>

    @else
        @if($calendar = App\SocialCalendar::where('user_id',auth()->id())->orderBy('id','desc')->first())
            <li class="nav-item">
                <a class="nav-link {{ request()->route()===route('social-calendar.share',$calendar->calendar_id)?'active':'' }}"
                   href="{{ route('social-calendar.share',$calendar->calendar_id) }}">
                    <span class="fa fa-btn fa-calendar-check"></span>
                    @if(request()->has('iframe'))
                        <br>
                    @endif
                    Social{{request()->has('iframe')?' ':''}}Media Calendar
                </a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link {{ request()->route()->getName()==='social-calendar.none'?'active':'' }}"
                   href="{{ route('social-calendar.none') }}">
                    <span class="fa fa-btn fa-calendar-check"></span>
                    @if(request()->has('iframe'))
                        <br>
                    @endif
                    Social{{request()->has('iframe')?' ':''}}Media Calendar
                </a>
            </li>
        @endif
    @endif

    @impersonating
    <li class="nav-item">
        <a href="{{ route('impersonate.leave') }}" class="nav-link">
            <span class="fa fa-btn fa-user-secret"></span>
            @if(request()->has('iframe'))
                <br>
            @endif
            Leave impersonation
        </a>
    </li>
    @endImpersonating

    <li class="nav-item">
        <a class="nav-link" href="{{ route('logout') }}"
           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <span class="far fa-fw fa-sign-out"></span>
            @if(request()->has('iframe'))
                <br>
            @endif
            {{ __('Logout') }}
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </li>
@endauth
