# Composer Repository Setup Guide

This guide explains how to distribute the Chat Widget package via Composer for easy installation across multiple Laravel projects.

## Table of Contents

1. [Distribution Methods](#distribution-methods)
2. [Option 1: Public Packagist](#option-1-public-packagist-recommended)
3. [Option 2: Private Repository (Satis)](#option-2-private-repository-satis)
4. [Option 3: Git Repository](#option-3-git-repository-direct)
5. [Version Management](#version-management)
6. [Publishing Updates](#publishing-updates)
7. [Testing Package Installation](#testing-package-installation)

---

## Distribution Methods

### Comparison Table

| Method | Best For | Cost | Ease of Use | Private |
|--------|----------|------|-------------|---------|
| **Public Packagist** | Open-source projects | Free | ⭐⭐⭐⭐⭐ | ❌ |
| **Private Packagist** | Commercial packages | Paid | ⭐⭐⭐⭐ | ✅ |
| **Satis** | Self-hosted private | Free (hosting cost) | ⭐⭐⭐ | ✅ |
| **Git Repository** | Quick setup, small teams | Free | ⭐⭐⭐⭐ | Optional |

---

## Option 1: Public Packagist (Recommended)

[Packagist.org](https://packagist.org/) is the main Composer package repository. Free for open-source projects.

### Step 1: Prepare Git Repository

Ensure your package is in a Git repository (GitHub, GitLab, Bitbucket):

```bash
cd c:\PhpProjects\microrepairnet-web\packages\microrepairnet\chat-widget

# Initialize git (if not already done)
git init

# Add all files
git add .

# Commit
git commit -m "Initial release v1.1.0"

# Create GitHub repository (via GitHub CLI or web interface)
gh repo create jorodriguezpr/laravel-ai-chat --public --source=. --remote=origin

# Push to GitHub
git branch -M main
git push -u origin main
```

### Step 2: Tag First Release

```bash
# Create annotated tag
git tag -a v1.1.0 -m "Release version 1.1.0"

# Push tag to GitHub
git push origin v1.1.0
```

### Step 3: Submit to Packagist

1. **Create Packagist account**: Go to [packagist.org](https://packagist.org/) and sign up
2. **Submit package**: Click "Submit" in top navigation
3. **Enter repository URL**: `https://github.com/jorodriguezpr/laravel-ai-chat`
4. **Click "Check"**: Packagist will validate your `composer.json`
5. **Click "Submit"**: Your package is now published!

### Step 4: Enable Auto-Updates (GitHub Integration)

1. Go to your package page on Packagist
2. Click your username → "My Packages" → "microrepairnet/chat-widget"
3. Go to "Settings" tab
4. Set up GitHub webhook for automatic updates on new releases

### Step 5: Install from Packagist

Now anyone can install your package:

```bash
composer require microrepairnet/chat-widget
```

That's it! No repository configuration needed in `composer.json`.

---

## Option 2: Private Repository (Satis)

For private packages, use [Satis](https://github.com/composer/satis) - a static Composer repository generator.

### Step 1: Install Satis

```bash
# On your server (Linux)
composer create-project composer/satis:dev-main /var/www/satis

cd /var/www/satis
```

### Step 2: Configure Satis

Create `satis.json`:

```json
{
    "name": "MicroRepair.net Private Packages",
    "homepage": "https://packages.microrepair.net",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/jorodriguezpr/laravel-ai-chat"
        }
    ],
    "require-all": true,
    "archive": {
        "directory": "dist",
        "format": "tar",
        "prefix-url": "https://packages.microrepair.net"
    }
}
```

### Step 3: Build Repository

```bash
php bin/satis build satis.json public/
```

### Step 4: Serve via Web Server

**Nginx configuration** (`/etc/nginx/sites-available/satis`):

```nginx
server {
    listen 80;
    server_name packages.microrepair.net;
    
    root /var/www/satis/public;
    index index.html;
    
    location / {
        try_files $uri $uri/ =404;
        
        # Enable CORS for Composer
        add_header Access-Control-Allow-Origin *;
    }
}
```

Enable site and restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/satis /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

### Step 5: Automate Updates

Create cron job to rebuild repository:

```bash
# crontab -e
*/15 * * * * cd /var/www/satis && php bin/satis build satis.json public/ --quiet
```

### Step 6: Configure Client Projects

In client Laravel projects, add to `composer.json`:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.microrepair.net"
        }
    ]
}
```

Then install:

```bash
composer require microrepairnet/chat-widget
```

### Step 7: Authentication (Optional)

For password-protected repositories:

```bash
# On client machine
composer config http-basic.packages.microrepair.net username password
```

---

## Option 3: Git Repository (Direct)

Quick setup using Git repository directly. No separate package server needed.

### Step 1: Push Package to Git

```bash
cd c:\PhpProjects\microrepairnet-web\packages\microrepairnet\chat-widget

# Initialize and push (if not done)
git init
git add .
git commit -m "Initial commit v1.1.0"
git remote add origin https://github.com/jorodriguezpr/laravel-ai-chat.git
git push -u origin main

# Tag version
git tag v1.1.0
git push origin v1.1.0
```

### Step 2: Configure Client Projects

In your Laravel project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/jorodriguezpr/laravel-ai-chat.git"
        }
    ],
    "require": {
        "microrepairnet/chat-widget": "^1.1"
    }
}
```

### Step 3: Install Package

```bash
composer install
```

### For Private Repositories

#### Using SSH Keys (Recommended)

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:jorodriguezpr/laravel-ai-chat.git"
        }
    ]
}
```

Ensure SSH key is added to GitHub account.

#### Using Personal Access Token

```bash
# Set credential helper
composer config --global github-oauth.github.com YOUR_GITHUB_TOKEN
```

---

## Version Management

### Semantic Versioning

Follow [SemVer](https://semver.org/):

- **MAJOR** (1.0.0 → 2.0.0): Breaking changes
- **MINOR** (1.0.0 → 1.1.0): New features, backwards compatible
- **PATCH** (1.1.0 → 1.1.1): Bug fixes, backwards compatible

### Version Constraints in Client Projects

```json
{
    "require": {
        "microrepairnet/chat-widget": "^1.1"
    }
}
```

**Constraint Meanings**:

- `^1.1`: >= 1.1.0, < 2.0.0 (recommended)
- `~1.1.0`: >= 1.1.0, < 1.2.0
- `1.1.*`: >= 1.1.0, < 1.2.0
- `>=1.1.0`: Any version >= 1.1.0
- `1.1.0`: Exact version only

### Creating Releases

#### Step 1: Update Version in composer.json

```json
{
    "version": "1.2.0"
}
```

#### Step 2: Update CHANGELOG.md

Document changes in CHANGELOG.md:

```markdown
## [1.2.0] - 2026-04-20

### Added
- New feature X
- Enhancement Y

### Fixed
- Bug fix Z
```

#### Step 3: Commit and Tag

```bash
git add composer.json CHANGELOG.md
git commit -m "Release version 1.2.0"
git tag -a v1.2.0 -m "Version 1.2.0 - Feature X and Bug Fix Z"
git push origin main
git push origin v1.2.0
```

#### Step 4: Update Repository

- **Packagist**: Auto-updates via webhook
- **Satis**: Cron will rebuild (or run manually: `php bin/satis build satis.json public/`)
- **Git**: Tag push is enough

---

## Publishing Updates

### Minor Updates (1.1.0 → 1.2.0)

```bash
# Make changes, test thoroughly
git add .
git commit -m "Add feature X"

# Update version in composer.json
# Update CHANGELOG.md

git add composer.json CHANGELOG.md
git commit -m "Bump version to 1.2.0"
git tag -a v1.2.0 -m "Release 1.2.0"
git push origin main --tags
```

Client projects update with:

```bash
composer update microrepairnet/chat-widget
```

### Patch Updates (1.1.0 → 1.1.1)

Same process as minor updates. For urgent fixes:

```bash
git checkout -b hotfix-1.1.1
# Fix bug
git commit -m "Fix critical bug"
git tag v1.1.1
git push origin hotfix-1.1.1 --tags
```

### Major Updates (1.x → 2.0.0)

Include migration guide in CHANGELOG.md:

```markdown
## [2.0.0] - 2026-06-01

### Breaking Changes
- Removed deprecated method X
- Changed Y to Z

### Upgrade Guide
1. Update composer.json: `"microrepairnet/chat-widget": "^2.0"`
2. Run: `composer update`
3. Follow migration steps...
```

---

## Testing Package Installation

### Test in Fresh Laravel Project

```bash
# Create test project
composer create-project laravel/laravel test-chat-widget
cd test-chat-widget

# Add repository (if using Git or Satis)
composer config repositories.chat-widget vcs https://github.com/jorodriguezpr/laravel-ai-chat.git

# Install package
composer require microrepairnet/chat-widget:^1.1

# Run migrations
php artisan migrate

# Test widget
php artisan serve
```

### Verify Installation

Check these files exist:

```bash
# Package installed
ls vendor/microrepairnet/chat-widget

# Service provider auto-discovered
php artisan package:discover

# Routes registered
php artisan route:list | grep chat

# Migrations run
php artisan migrate:status
```

### Test Publishing

```bash
# Publish configuration
php artisan vendor:publish --tag=chat-widget-config

# Verify published
ls config/chat-widget.php

# Publish views
php artisan vendor:publish --tag=chat-widget-views

# Verify
ls resources/views/vendor/chat-widget
```

---

## Continuous Integration

### GitHub Actions for Auto-Testing

Create `.github/workflows/test.yml` in package repository:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php: [8.1, 8.2, 8.3]
        laravel: [10.*, 11.*, 13.*]
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
      
      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress
      
      - name: Run Tests
        run: vendor/bin/phpunit
```

---

## Package Metadata Best Practices

### Essential composer.json Fields

```json
{
    "name": "microrepairnet/chat-widget",
    "description": "Laravel package for real-time chat widget with AI support",
    "keywords": ["laravel", "chat", "widget", "ai", "livechat"],
    "homepage": "https://github.com/jorodriguezpr/laravel-ai-chat",
    "license": "MIT",
    "type": "library",
    "authors": [
        {
            "name": "Jose Rodriguez Arroyo",
            "email": "jrpcone@gmail.com",
            "homepage": "https://www.microrepair.net",
            "role": "Developer"
        }
    ],
    "support": {
        "issues": "https://github.com/jorodriguezpr/laravel-ai-chat/issues",
        "source": "https://github.com/jorodriguezpr/laravel-ai-chat",
        "docs": "https://github.com/jorodriguezpr/laravel-ai-chat#readme"
    }
}
```

### README Badges

Add to README.md for professionalism:

```markdown
[![Latest Version](https://img.shields.io/packagist/v/microrepairnet/chat-widget.svg)](https://packagist.org/packages/microrepairnet/chat-widget)
[![Total Downloads](https://img.shields.io/packagist/dt/microrepairnet/chat-widget.svg)](https://packagist.org/packages/microrepairnet/chat-widget)
[![License](https://img.shields.io/packagist/l/microrepairnet/chat-widget.svg)](https://packagist.org/packages/microrepairnet/chat-widget)
```

---

## Troubleshooting

### Issue: Package Not Found

**Check**:
1. Package name matches in composer.json: `"microrepairnet/chat-widget"`
2. Repository URL is correct
3. Git tag exists: `git tag -l`
4. Packagist shows package (if using Packagist)

**Solution**:
```bash
# Clear Composer cache
composer clear-cache

# Try again
composer require microrepairnet/chat-widget
```

### Issue: Wrong Version Installed

**Check**:
```bash
composer show microrepairnet/chat-widget
```

**Solution**:
```bash
# Update to latest
composer update microrepairnet/chat-widget

# Or specify version
composer require microrepairnet/chat-widget:^1.1
```

### Issue: Auto-Discovery Not Working

**Verify** `composer.json` has:
```json
"extra": {
    "laravel": {
        "providers": [
            "Microrepairnet\\ChatWidget\\ChatWidgetServiceProvider"
        ]
    }
}
```

**Force discovery**:
```bash
composer dump-autoload
php artisan package:discover
```

---

## Recommended Workflow

### For Package Development

1. **Work in package directory**: `c:\PhpProjects\microrepairnet-web\packages\microrepairnet\chat-widget\`
2. **Test in host project**: `c:\PhpProjects\microrepairnet-web\` (with path repository)
3. **Commit changes**: Regular git commits
4. **Create release**: Tag version when ready
5. **Publish**: Push to GitHub, auto-updates Packagist

### For Client Projects

1. **Add dependency**: `composer require microrepairnet/chat-widget`
2. **Run migrations**: `php artisan migrate`
3. **Configure**: Publish config if needed
4. **Update regularly**: `composer update microrepairnet/chat-widget`

---

## Summary

### Quick Reference

**Public Distribution (Packagist)**:
```bash
git tag v1.1.0
git push origin v1.1.0
# Packagist auto-updates
```

**Client Installation**:
```bash
composer require microrepairnet/chat-widget
php artisan migrate
```

**Private Distribution (Satis)**:
```bash
php bin/satis build satis.json public/
```

**Git Direct**:
```json
{
    "repositories": [{"type": "vcs", "url": "https://github.com/jorodriguezpr/laravel-ai-chat.git"}]
}
```

---

**Package Version**: 1.1.0  
**Last Updated**: April 15, 2026  
**Author**: Jose Rodriguez Arroyo  
**Email**: jrpcone@gmail.com
