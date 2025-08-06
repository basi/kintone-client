#!/bin/bash

# Kintone REST API Spec Update Script
# This script updates the API specification subtree from the upstream repository

set -e

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SPEC_DIR="$PROJECT_ROOT/rest-api-spec"

echo "🔄 Updating Kintone REST API specification..."

# Check if we're in a git repository
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo "❌ Error: Not in a git repository"
    exit 1
fi

# Check if the subtree exists
if [ ! -d "$SPEC_DIR" ]; then
    echo "❌ Error: rest-api-spec directory not found. Run initial setup first."
    exit 1
fi

# Update the subtree
echo "📡 Fetching latest changes from upstream..."
git subtree pull --prefix=rest-api-spec https://github.com/kintone/rest-api-spec.git main --squash

echo "✅ API specification updated successfully!"

# Show latest version available
LATEST_VERSION=$(find "$SPEC_DIR/kintone" -maxdepth 1 -type d -name "20*" | sort -r | head -1 | xargs basename 2>/dev/null || echo "None found")

if [ "$LATEST_VERSION" != "None found" ]; then
    echo "📋 Latest API version: $LATEST_VERSION"
    echo "🔧 Run 'composer generate' to regenerate the PHP client with the latest specification"
else
    echo "⚠️  No dated API versions found in the specification"
fi