# Installation

```
$ composer install
$ cp .env.example .env
# Make sure you add your Stripe keys to the `.env` file.
$ php artisan migrate --seed --force
$ npm install
$ gulp
```

Running the front-end:

```
$ tmux new -s ticket-beast
$ php artisan serve --env=testing
```

Press `Ctrl+B, D` to detach tmux.

# Front-End

Access the front-end at [locahost:8000](http://localhost:8000).

# Running the tests

```
$ touch database/testing.sqlite
$ ./vendor/bin/phpunit tests/
```
