#!/bin/bash
set -e

PROJECT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Pruefe IT-Architekturplaner Projektstruktur..."

mkdir -p \
    "$PROJECT/config" \
    "$PROJECT/modules" \
    "$PROJECT/assets/css" \
    "$PROJECT/assets/js" \
    "$PROJECT/assets/img" \
    "$PROJECT/projects" \
    "$PROJECT/exports" \
    "$PROJECT/templates"

echo "Projektstruktur ist bereit."
echo "Pfad: $PROJECT"
echo "Starten mit:"
echo "cd \"$PROJECT\""
echo "php -S 127.0.0.1:8080"
