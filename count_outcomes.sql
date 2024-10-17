with a as (
	select distinct gd.game_link, gd.outcome from fens f
	inner join game_data gd on (gd.game_link = f.game_link)
	where f.descriptor like '%KQvK%'
)
	select outcome, count(*)
    from a
    group by outcome
    order by outcome DESC