#!/usr/bin/env bash

cd /app

# Clear cache
rm -rf /app/var/cache/*

# Start supervisor daemon
supervisord
