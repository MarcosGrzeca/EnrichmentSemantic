<?php

SET GLOBAL max_allowed_packet=1073741824;
select 
    concat('[',
        GROUP_CONCAT(
            JSON_OBJECT(    
  'id', id,
  'text', textEmbedding,
  'label', q2                )
            SEPARATOR ',')
    ,']')
FROM tweets_amazon
WHERE q2 IN (0,1)