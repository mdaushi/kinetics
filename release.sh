#!/bin/bash

# Hentikan script jika ada error
set -e

if [ -z "$1" ]; then
  echo "Error: Versi baru tidak diberikan."
  echo "Cara penggunaan: ./release.sh <versi>"
  echo "Contoh: ./release.sh 0.1.0"
  exit 1
fi

VERSION=$1

# Hapus huruf 'v' di awal jika user tidak sengaja mengetiknya
# (agar package.json tetap "0.1.0" bukan "v0.1.0")
VERSION="${VERSION#v}"

TAG_NAME="v$VERSION"

echo "🚀 Memulai proses rilis untuk versi $TAG_NAME..."

# Update versi di packages/core/package.json
echo "📦 Memperbarui versi di packages/core..."
cd packages/core
# --no-git-tag-version digunakan agar npm tidak membuat commit/tag sendiri
npm version $VERSION --no-git-tag-version > /dev/null
cd ../..

# Update versi di packages/react/package.json
echo "📦 Memperbarui versi di packages/react..."
cd packages/react
npm version $VERSION --no-git-tag-version > /dev/null
cd ../..

# Commit perubahan file package.json
echo "💾 Menyimpan perubahan ke Git..."
git add packages/core/package.json packages/react/package.json
git commit -m "chore: bump version to $TAG_NAME"

# Membuat Git Tag
echo "🏷️ Membuat Git Tag $TAG_NAME..."
git tag $TAG_NAME

# Push branch dan tag ke GitHub
echo "☁️ Mendorong (push) perubahan dan tag ke GitHub..."
git push origin main
git push origin $TAG_NAME

echo ""
echo "🎉 Selesai! Versi $TAG_NAME berhasil di-push."
