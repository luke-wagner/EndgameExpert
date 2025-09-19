#!/bin/bash

# Simple tablebase generation script - builds directly on droplet

# Install dependencies
sudo apt update -qq
sudo apt install -y build-essential libzstd-dev zlib1g-dev git perl

# Create directory
mkdir -p ~/tablebases
cd ~/tablebases

# Clone and build (skip if exists)
if [ ! -d "tb" ]; then
    git clone https://github.com/syzygy1/tb.git
fi

cd tb/src
make clean
make all -j$(nproc)

# Generate tablebases directly in ~/tablebases
export PATH=$PATH:$(pwd)
export RTBWDIR=~/tablebases

cd ~/tablebases
perl tb/src/run.pl --generate --min 3 --max 4 --threads $(nproc)

# Show results
echo "Generated files:"
ls -lh ~/tablebases/*.rtb* 2>/dev/null || echo "No files generated"
