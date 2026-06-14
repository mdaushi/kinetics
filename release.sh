#!/bin/bash

set -e

if [ -z "$1" ]; then
  echo "Error: Versi baru tidak diberikan."
  echo "Cara penggunaan: ./release.sh <versi>"
  echo "Contoh: ./release.sh 0.1.0"
  exit 1
fi

VERSION=$1
VERSION="${VERSION#v}"
TAG_NAME="v$VERSION"

# Validasi working tree bersih
if [ -n "$(git status --porcelain)" ]; then
  echo "Error: Working tree tidak bersih. Commit atau stash dulu."
  exit 1
fi

echo "🚀 Memulai proses rilis untuk versi $TAG_NAME..."

# Test PHP
echo "🧪 Menjalankan PHP tests..."
composer test

# Test JS
# echo "🧪 Menjalankan JS tests..."
# pnpm -r run test

echo ""
echo "Akan release $TAG_NAME. Lanjut? (y/N)"
read -r confirm
if [ "$confirm" != "y" ]; then
  echo "Dibatalkan."
  exit 0
fi

# Update versi
echo "📦 Memperbarui versi di packages/core..."
cd packages/core
npm version $VERSION --no-git-tag-version > /dev/null
cd ../..

echo "📦 Memperbarui versi di packages/react..."
cd packages/react
npm version $VERSION --no-git-tag-version > /dev/null
cd ../..

# Commit, tag, push
echo "💾 Menyimpan perubahan ke Git..."
git add packages/core/package.json packages/react/package.json
git commit -m "chore: bump version to $TAG_NAME"

echo "🏷️ Membuat Git Tag $TAG_NAME..."
git tag $TAG_NAME

echo "☁️ Mendorong perubahan dan tag ke GitHub..."
git push origin main
git push origin $TAG_NAME

echo ""
echo "🎉 Selesai! Versi $TAG_NAME berhasil di-push."