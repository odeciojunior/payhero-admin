.PHONY: help dev deploy

# Default target
help:
	@echo "PayHero Manager - Docker Commands"
	@echo ""
	@echo "Development Commands: make dev [command]"
	@echo "================================================"
	@echo "Docker commands:"
	@echo "  make dev setup      - Initial setup (build, up, migrate, seed)"
	@echo "  make dev build      - Build Docker images"
	@echo "  make dev up         - Start all services (app, nginx, mysql, redis)"
	@echo "  make dev down       - Stop and remove all containers"
	@echo "  make dev restart    - Restart all services"
	@echo "  make dev logs       - Show logs from all services"
	@echo "  make dev ps         - Show running containers"
	@echo "  make dev clean      - Remove containers, volumes, and images"
	@echo ""
	@echo "Service access:"
	@echo "  make dev bash       - Access app container shell"
	@echo "  make dev mysql      - Access MySQL container shell"
	@echo "  make dev redis      - Access Redis CLI"
	@echo ""
	@echo "Database commands:"
	@echo "  make dev migrate    - Run database migrations"
	@echo "  make dev seed       - Run database seeders"
	@echo "  make dev seed-users - Run users table seeder"
	@echo "  make dev fresh      - Fresh migration with seeders"
	@echo ""
	@echo "Laravel commands:"
	@echo "  make dev artisan cmd='route:list'  - Run artisan commands"
	@echo "  make dev composer cmd='install'    - Run composer commands"
	@echo "  make dev npm cmd='install'         - Run npm commands"
	@echo "  make dev test                      - Run PHPUnit tests"
	@echo "  make dev pint                      - Run Laravel Pint code formatter"
	@echo ""
	@echo "Asset commands:"
	@echo "  make dev assets     - Build development assets"
	@echo "  make dev watch      - Watch assets for changes"
	@echo "  make dev prod       - Build production assets"
	@echo ""

# Main dev command handler
dev:
	@if [ "$(filter-out $@,$(MAKECMDGOALS))" = "" ]; then \
		echo "Error: Please specify a command. Run 'make help' for available commands."; \
		exit 1; \
	fi
	@$(MAKE) -s dev-$(filter-out $@,$(MAKECMDGOALS))

# Prevent Make from trying to execute command arguments
%:
	@:

# Docker commands
dev-build:
	@echo "Building Docker images..."
	@if [ -f .env.development ]; then \
		set -a && . ./.env.development && set +a && \
		docker compose -f docker-compose.payhero-dev.yml build --no-cache; \
	else \
		echo "Warning: .env.development file not found. Using default settings."; \
		docker compose -f docker-compose.payhero-dev.yml build --no-cache; \
	fi

dev-up:
	@echo "Starting Docker containers..."
	@if [ -f .env.development ]; then \
		set -a && . ./.env.development && set +a && \
		docker-compose -f docker-compose.payhero-dev.yml up -d; \
	else \
		echo "Warning: .env.development file not found. Using default settings."; \
		docker-compose -f docker-compose.payhero-dev.yml up -d; \
	fi
	@echo ""
	@echo "Services started successfully!"
	@echo "Application: http://localhost:8080"
	@echo "MySQL: localhost:3306"
	@echo "Redis: localhost:6379"

dev-down:
	@echo "Stopping Docker containers..."
	docker compose -f docker-compose.payhero-dev.yml down

dev-restart:
	@$(MAKE) -s dev-down
	@$(MAKE) -s dev-up

dev-logs:
	docker compose -f docker-compose.payhero-dev.yml logs -f

dev-ps:
	docker compose -f docker-compose.payhero-dev.yml ps

dev-clean:
	@echo "Cleaning up Docker resources..."
	docker compose -f docker-compose.payhero-dev.yml down -v
	docker system prune -af

# Initial setup
dev-setup:
	@$(MAKE) -s dev-down
	@$(MAKE) -s dev-build
	@$(MAKE) -s dev-up
	@echo "#"
	@echo "Running initial setup..."
	@echo "#"
	@$(MAKE) -s dev-check-mysql
	@$(MAKE) -s dev-check-php
	@echo "#"
	@echo "Mysql and Php checked... can run migrate"
	@echo "#"
	@$(MAKE) -s dev-migrate
	@echo "#"
	@echo "Now install dependencies..."
	@echo "#"
	@$(MAKE) -s dev-composer cmd="install"
	@$(MAKE) -s dev-npm cmd="install"
	@echo "#"
	@echo "Running Users Seeder..."
	@echo "#"
	@$(MAKE) -s dev-seed-users
	@echo ""
	@echo "Setup complete! Application is running at http://localhost:8080"

# Service access
dev-bash:
	docker compose -f docker-compose.payhero-dev.yml exec app sh

dev-mysql:
	docker compose -f docker-compose.payhero-dev.yml exec mysql bash

dev-redis:
	docker compose -f docker-compose.payhero-dev.yml exec redis redis-cli

# Check if PHP is running and wait if necessary
dev-check-php:
	@echo "Checking if PHP is running on payhero_app..."
	@attempts=0; \
	max_attempts=12; \
	until docker compose -f docker-compose.payhero-dev.yml exec app php --version > /dev/null 2>&1; do \
		attempts=$$((attempts+1)); \
		if [ $$attempts -ge $$max_attempts ]; then \
			echo "PHP is not available after 2 minutes. Exiting."; \
			exit 1; \
		fi; \
		echo "PHP not available yet, waiting 5 seconds... ($$attempts/$$max_attempts)"; \
		sleep 5; \
	done; \
	echo "PHP is running on payhero_app container."

# Check if MySQL is running and wait if necessary
dev-check-mysql:
	@echo "Checking if MySQL is running on payhero_mysql..."
	@attempts=0; \
	max_attempts=12; \
	until docker compose -f docker-compose.payhero-dev.yml exec mysql mysqladmin ping -h127.0.0.1 -uroot -proot --silent > /dev/null 2>&1; do \
		attempts=$$((attempts+1)); \
		if [ $$attempts -ge $$max_attempts ]; then \
			echo "MySQL is not available after 1 minute. Exiting."; \
			exit 1; \
		fi; \
		echo "MySQL not available yet, waiting 5 seconds... ($$attempts/$$max_attempts)"; \
		sleep 5; \
	done; \
	echo "MySQL is running on payhero_mysql container."

# Database commands
dev-migrate: dev-check-php dev-check-mysql
	@echo "Running database migrations..."
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan migrate

dev-seed: dev-check-php
	@echo "Running database seeders..."
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan db:seed 

dev-seed-users: dev-check-php
	@echo "Running users table seeder..."
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan db:seed --class=UsersTableSeeder

dev-fresh: dev-check-php
	@echo "Running fresh migration with seeders..."
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan migrate:fresh --seed

# Laravel commands
dev-artisan: dev-check-php
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan $(cmd)

dev-composer: dev-check-php
	docker compose -f docker-compose.payhero-dev.yml exec app composer $(cmd)

dev-npm:
	docker compose -f docker-compose.payhero-dev.yml exec app npm $(cmd)

dev-test:
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan test

dev-pint:
	docker compose -f docker-compose.payhero-dev.yml exec app ./vendor/bin/pint

# Asset building
dev-assets:
	@$(MAKE) -s dev-npm cmd="run dev"

dev-watch:
	@$(MAKE) -s dev-npm cmd="run watch"

# Clear all caches
dev-clear:
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan config:clear
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan cache:clear
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan route:clear
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan view:clear

# Generate application key
dev-key:
	docker compose -f docker-compose.payhero-dev.yml exec app php artisan key:generate