#!/usr/bin/env bash
set -e

# Start command for the Railway "worker" service.
#
# This repo's default Railway service only serves HTTP traffic — nothing
# processes the `database` queue (used by App\Jobs\GenerateAiBlogPost).
# Point a second Railway service (same repo/branch, no public domain) at
# this script as its Custom Start Command, sharing the same env vars
# (DB_*, OPENAI_*, CLOUDINARY_*) as the web service.
#
# --timeout must stay >= the slowest job's own $timeout property.
# DB_QUEUE_RETRY_AFTER (config/queue.php) must stay comfortably above
# --timeout too, or a slow-but-alive job gets picked up by a second
# worker and run twice.
php artisan queue:work --timeout=300 --max-time=3600
