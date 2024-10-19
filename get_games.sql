with a as (
	select game_link, MAX(move_number) as move_number
	from fens
	where descriptor = ?
	group by game_link
)
	select f.fen, f.game_link, gd.outcome
    from a
    inner join fens f on (f.game_link = a.game_link and f.move_number = a.move_number)
    inner join game_data gd on (gd.game_link = f.game_link)