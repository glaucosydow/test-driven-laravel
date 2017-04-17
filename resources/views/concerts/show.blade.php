<h1>{{ $concert->title }}</h1>
<h2>{{ $concert->subtitle }}</h2>
<p>{{ $concert->formatted_date }}</p>
<p>Doors: {{ $concert->formatted_time }}</p>
<p>Price: {{ $concert->ticket_price_in_dollars }}</p>
<p>Venue: {{ $concert->venue }}</p>
<p>Address: {{ $concert->venue_address }}</p>
<p>{{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}</p>
<p>Info: {{ $concert->additional_information }}</p>