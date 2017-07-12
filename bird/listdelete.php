<?php
$id=intval(I("id"));
M("xiezilou",null,"`id`={$id}");
script("BirdPost(this,'.SavedListPost','list','#SavedTableFrame');");