def categorize_pos(player_color, pieces):
    descriptor = ''

    if (player_color == 'W' and pieces == ['K', 'Q', 'k']) or (
        player_color == 'B' and pieces == ['K', 'k', 'q']):
        descriptor = 'KQvK'
    elif (pieces == ['K', 'R', 'k', 'r']):
        descriptor = 'KRvKR'
    elif (player_color == 'W' and pieces == ['B', 'K', 'R', 'k', 'n', 'r']) or (
        player_color == 'B' and pieces == ['K', 'N', 'R', 'b', 'k', 'r']):
        descriptor = 'KRBvKRN'
    elif (player_color == 'W' and pieces == ['K', 'N', 'R', 'b', 'k', 'r']) or (
        player_color == 'B' and pieces == ['B', 'K', 'R', 'k', 'n', 'r']):
        descriptor = 'KRNvKRB'
    elif (pieces == ['K', 'R', 'R', 'k', 'r', 'r']):
        descriptor = 'KRRvKRR'
    elif (pieces == ['B', 'K', 'R', 'b', 'k', 'r']):
        descriptor = 'KRBvKRB'
    elif (player_color == 'W' and pieces == ['B', 'K', 'k', 'n']) or (
        player_color == 'B' and pieces == ['K', 'N', 'b', 'k']):
        descriptor = 'KBvKN'
    elif (player_color == 'W' and pieces == ['K', 'N', 'b', 'k']) or (
        player_color == 'B' and pieces == ['B', 'K', 'k', 'n']):
        descriptor = 'KNvKB'
    elif (pieces == ['K', 'N', 'R', 'k', 'n', 'r']):
        descriptor = 'KRNvKRN'
    elif (pieces == ['K', 'Q', 'k', 'q']):
        descriptor = 'KQvKQ'
    else:
        set_pieces = set(pieces)
        pawn_endgames = {'K', 'k', 'P', 'p'}
        
        if set_pieces.issubset(pawn_endgames):
            descriptor = 'Pawns'

    return descriptor