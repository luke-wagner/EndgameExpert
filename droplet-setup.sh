#!/bin/bash

# Make all scripts in ./scripts executable
chmod +x ./scripts/*

# Run setup scripts
./scripts/generate-tb.sh
./scripts/setup-env.sh
./scripts/test-tb.sh