#!/usr/bin/env python3
import sys
import chess
import chess.syzygy

# Path to your tablebases
TB_PATH = "/root/tablebases"

# Sample 3–4 piece positions (all FENs are syntactically valid and legal).
POSITIONS = {
    "KQ vs K":       "4k3/8/8/8/8/8/Q3K3/8 w - - 0 1",
    "KR vs K":       "4k3/8/8/8/8/8/R3K3/8 w - - 0 1",
    "KQ vs KR":      "4k2r/8/8/8/8/8/Q3K3/8 w - - 0 1",
    "KN vs K":       "4k3/8/8/8/8/8/N3K3/8 w - - 0 1",
    "KB vs K":       "4k3/8/8/8/8/8/B3K3/8 w - - 0 1",
    "KQ vs KB":      "4k2b/8/8/8/8/8/Q3K3/8 w - - 0 1",
    "KR vs KN":      "4k2n/8/8/8/8/8/R3K3/8 w - - 0 1",
    # Theoretical loss example: White alone king vs Black king+queen, White to move -> loss
    "K vs KQ (White to move - LOSS)": "q3k3/8/8/8/8/8/8/4K3 w - - 0 1",
}

def probe_position(tb, name, fen):
    print(f"{name:30} | FEN: {fen}")
    # validate FEN / construct board
    try:
        board = chess.Board(fen)
    except ValueError as e:
        print(f"  Invalid FEN: {e}")
        print("-" * 60)
        return

    # Try probing WDL (win/draw/loss)
    try:
        wdl = tb.probe_wdl(board)  # -2 = loss, 0 = draw, +2 = win
    except chess.syzygy.MissingTableError as e:
        # Graceful handling when the requested WDL table doesn't exist
        print(f"  Missing WDL table: {e}")
        print("  Suggestion: verify the appropriate .rtbw files exist in", TB_PATH)
        print("-" * 60)
        return
    except Exception as e:
        print(f"  Error probing WDL: {e}")
        print("-" * 60)
        return

    # Try probing DTZ (distance to zeroing move) when relevant
    dtz = "N/A"
    if wdl != 0:
        try:
            dtz = tb.probe_dtz(board)
        except chess.syzygy.MissingTableError as e:
            print(f"  Missing DTZ table: {e} (DTZ unavailable)")
            dtz = "N/A"
        except Exception as e:
            print(f"  Error probing DTZ: {e}")
            dtz = "N/A"

    print(f"  WDL: {wdl} (2=Win, 0=Draw, -2=Loss)")
    print(f"  DTZ: {dtz}")
    print("-" * 60)


def main():
    print("Using tablebase path:", TB_PATH)
    print()

    try:
        with chess.syzygy.open_tablebase(TB_PATH) as tb:
            for name, fen in POSITIONS.items():
                probe_position(tb, name, fen)
    except FileNotFoundError as e:
        print("Could not open tablebase directory:", e)
        print("Make sure TB_PATH points to the folder containing your .rtbw/.rtbz files.")
        sys.exit(1)
    except Exception as e:
        print("Unexpected error opening tablebase:", e)
        sys.exit(1)


if __name__ == "__main__":
    main()

