#!/bin/bash
set -e

echo "Activating virtual environment..."
source ~/tb-venv/venv/bin/activate

echo "Running test_tb.py..."
python3 ~/test_tb.py
