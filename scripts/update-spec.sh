#!/bin/bash

# Kintone REST API Spec Download Script
# This script downloads only the API specification files without dependencies

set -e

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SPEC_DIR="$PROJECT_ROOT/rest-api-spec"
TEMP_DIR="$PROJECT_ROOT/temp-spec-download"

echo "🔄 Downloading Kintone REST API specification..."

# Clean up any existing temp directory
if [ -d "$TEMP_DIR" ]; then
    rm -rf "$TEMP_DIR"
fi

# Clone with minimal depth (compatible with older git versions)
echo "📡 Cloning repository with minimal footprint..."
git clone --depth 1 https://github.com/kintone/rest-api-spec.git "$TEMP_DIR"

# Remove existing spec directory if it exists
if [ -d "$SPEC_DIR" ]; then
    rm -rf "$SPEC_DIR"
fi

# Copy only the kintone directory (no package.json, node_modules, etc.)
echo "📁 Copying API specification files..."
mkdir -p "$SPEC_DIR"

# Navigate to temp directory and check if kintone directory exists
cd "$TEMP_DIR"
if [ -d "kintone" ]; then
    cp -r kintone/* "$SPEC_DIR/"
else
    echo "⚠️  kintone directory not found, checking repository structure..."
    ls -la
    echo "❌ Error: Could not find kintone directory in the repository"
    cd "$PROJECT_ROOT"
    rm -rf "$TEMP_DIR"
    exit 1
fi

# Clean up temp directory
cd "$PROJECT_ROOT"
rm -rf "$TEMP_DIR"

echo "✅ API specification downloaded successfully!"

# Show latest version available
LATEST_VERSION=$(find "$SPEC_DIR" -maxdepth 1 -type d -name "20*" | sort -r | head -1 | xargs basename 2>/dev/null || echo "None found")

if [ "$LATEST_VERSION" != "None found" ]; then
    echo "📋 Latest API version: $LATEST_VERSION"
    echo "🔧 Run 'composer generate' to regenerate the PHP client with the latest specification"
else
    echo "⚠️  No dated API versions found in the specification"
fi

echo "ℹ️  Note: rest-api-spec/ directory is git-ignored and will not be committed to your repository"