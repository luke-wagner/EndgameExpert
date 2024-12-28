# Flip black/white perspective
# Flips board horizontally and vertically
def get_flipped_fen(fen):
    stack = []

    rows = fen.split('/')
    for row in rows:
        reversed_row = row[::-1] # Reverse string
        stack.append(reversed_row)

    final_fen = ""
    stack_len = len(stack)
    for i in range(stack_len):
        row = stack.pop()
        final_fen += row
        if i < stack_len - 1:
            final_fen += '/'

    return final_fen

if __name__ == "__main__":
    print(get_flipped_fen('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR')) # starting position