#!/bin/bash
set -e

# Ensure system packages are updated
sudo apt update -qq
sudo apt install -y python3 python3-venv python3-pip

# Create directory for venv
mkdir -p ~/tb-venv
cd ~/tb-venv

# Create virtual environment
python3 -m venv venv

# Activate environment
source venv/bin/activate

# Upgrade pip
pip install --upgrade pip

# Install python-chess
pip install chess

echo "Virtual environment ready."
echo "To activate it later, run:"
echo "  source ~/tb-venv/venv/bin/activate"
