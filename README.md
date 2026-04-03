# Albion Online Discord Kill/Death Tracker

Laravel-based bot that tracks Albion Online player kills and deaths and sends notifications to Discord via webhooks.

## Features

- Track multiple Albion Online players
- Send kill notifications to dedicated Discord channel
- Send death notifications to dedicated Discord channel
- Rich Discord embeds with kill/death details
- Automatic inventory/equipment image generation
- Runs every minute via Laravel scheduler
- Prevents duplicate notifications

## Requirements

- PHP 8.2+
- Composer
- SQLite (or MySQL/PostgreSQL)
- GD extension for PHP (for image generation)

## Installation

1. Clone the repository and install dependencies:
```bash
composer install
```

2. Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Configure Discord webhooks in `.env`:
```env
DISCORD_WEBHOOK_KILLS=https://discord.com/api/webhooks/YOUR_KILLS_WEBHOOK_URL
DISCORD_WEBHOOK_DEATHS=https://discord.com/api/webhooks/YOUR_DEATHS_WEBHOOK_URL
```

To get webhook URLs:
- Go to your Discord server
- Right-click on a channel → Edit Channel
- Integrations → Webhooks → New Webhook
- Copy the webhook URL

5. Run migrations:
```bash
php artisan migrate
```

6. (Optional) Copy Arial Bold font to `resources/fonts/ARIALBD.TTF`:
   - Windows: Copy from `C:\Windows\Fonts\arialbd.ttf`
   - macOS: Copy from `/System/Library/Fonts/Supplemental/Arial Bold.ttf`

## Usage

### Adding Players to Track

Use Laravel Tinker to add players:

```bash
php artisan tinker
```

Then:

```php
// Add a player
App\Models\Player::create([
    'albion_id' => 'PLAYER_ID_HERE',
    'name' => 'PlayerName',
    'active' => true
]);

// List all tracked players
App\Models\Player::all();

// Deactivate a player (stop tracking)
$player = App\Models\Player::where('albion_id', 'PLAYER_ID_HERE')->first();
$player->update(['active' => false]);
```

**How to find a player ID:**
1. Go to https://albiononline.com/killboard
2. Search for the player
3. The player ID is in the URL: `https://albiononline.com/killboard/player/PLAYER_ID_HERE`

### Initialize Player History

**IMPORTANT**: After adding players, you must initialize their history to avoid spamming old kills/deaths:

```bash
php artisan albion:initialize-history
```

This marks all existing kills and deaths as "already processed" without sending notifications. Only run this when:
- Adding new players
- After clearing the database
- First time setup

### Running the Tracker

#### Manual Run (for testing):
```bash
php artisan albion:track-kills
```

#### Production (with scheduler):

The tracker runs automatically every minute via Laravel's scheduler.

Add this to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or use a process manager like Supervisor to run:
```bash
php artisan schedule:work
```

## Discord Notification Format

### Kill Notifications
- **Title**: ⚔️ New Kill Alert!
- **Fields**: Killer, Victim, Location
- **Link**: Direct link to killboard
- **Image**: Victim's inventory/equipment

### Death Notifications
- **Title**: 💀 Player Death Alert!
- **Fields**: Victim, Killer, Location
- **Link**: Direct link to killboard
- **Image**: Lost inventory/equipment

## Project Structure

```
app/
├── Console/Commands/
│   └── TrackKills.php          # Main tracking command
├── Models/
│   ├── Player.php              # Tracked players
│   ├── ProcessedKill.php       # Processed kills (prevent duplicates)
│   └── ProcessedDeath.php      # Processed deaths (prevent duplicates)
└── Services/
    ├── AlbionApiService.php    # Albion Online API client
    ├── DiscordWebhookService.php # Discord webhook sender
    └── InventoryImageService.php # Inventory image generator
```

## Troubleshooting

### No notifications appearing

1. Check webhook URLs are correct in `.env`
2. Test webhook manually:
```bash
curl -X POST "YOUR_WEBHOOK_URL" \
  -H "Content-Type: application/json" \
  -d '{"content": "Test message"}'
```

### Images not generating

1. Verify GD extension is installed:
```bash
php -m | grep gd
```

2. Check font file exists at `resources/fonts/ARIALBD.TTF`

### Scheduler not running

1. Verify cron is configured correctly
2. Check Laravel logs: `storage/logs/laravel.log`
3. Test manually: `php artisan schedule:run`

## API Rate Limits

The Albion Online API has no official rate limits, but be respectful. The default 1-minute interval is reasonable for most use cases.

## License

MIT License
