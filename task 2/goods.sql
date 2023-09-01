SELECT g.name, af.name, afv.name
FROM additional_goods_field_values agfv
    JOIN goods g on agfv.good_id = g.id
    JOIN additional_fields af on af.id = agfv.additional_field_id
    JOIN additional_field_values afv on agfv.good_id = afv.id
