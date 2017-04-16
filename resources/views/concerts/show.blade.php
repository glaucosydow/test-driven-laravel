<h1>{{ $concert->title }}</h1>
<h2>{{ $concert->subtitle }}</h2>
<p>{{ $concert->date->format('F j, Y') }}</p>
<p>Doors: {{ $concert->date->format('g:ia') }}</p>
<p>Price: {{ number_format($concert->ticket_price / 100, 2) }}</p>
<p>Venue: {{ $concert->venue }}</p>
<p>Address: {{ $concert->venue_address }}</p>
<p>{{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}</p>
<p>Info: {{ $concert->additional_information }}</p>