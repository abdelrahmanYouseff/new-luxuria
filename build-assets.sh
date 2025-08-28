#!/bin/bash

echo "Building assets..."
npm run build

echo "Copying manifest.json to correct location..."
cp public/build/.vite/manifest.json public/build/manifest.json

echo "Build completed successfully!"
echo "Manifest file is now available at: public/build/manifest.json"
