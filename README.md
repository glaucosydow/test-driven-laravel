# Installation

```
composer install
tmux new -s ticket-beast
php artisan serve --env=testing
```

Press `Ctrl+B, D` to detach tmux.

```
export APP_ENV=testing && php artisan dusk
```
